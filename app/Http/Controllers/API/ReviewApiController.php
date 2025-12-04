<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Reservation;
use App\Models\Review;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class ReviewApiController
 *
 * Xử lý API đánh giá đặt bàn
 * - Dễ test: có API liệt kê reservation có thể đánh giá
 * - Cho phép gửi/cập nhật đánh giá
 * - Trả về lỗi rõ ràng
 */
class ReviewApiController extends Controller
{
    use AuthorizesRequests;

    /**
     * Danh sách đặt bàn hoàn tất + trạng thái đánh giá
     * GET /api/reviewable
     */
    public function index(): JsonResponse
    {
        $reservations = Reservation::where('user_id', auth()->id())
            ->where('status', 'completed')
            ->with('review')
            ->latest('reservation_date')
            ->get()
            ->map(function ($reservation) {
                $hasReview = $reservation->review()->exists();
                return [
                    'reservation_id'     => $reservation->id,
                    'reservation_code'   => $reservation->reservation_code,
                    'date'               => $reservation->reservation_date->format('d/m/Y'),
                    'shift'              => ucfirst($reservation->shift),
                    'num_people'         => $reservation->num_people,
                    'can_review'         => !$hasReview,
                    'review'             => $hasReview ? [
                        'id'         => $reservation->review->id,
                        'rating'     => $reservation->review->rating,
                        'comment'    => $reservation->review->comment,
                        'created_at' => $reservation->review->created_at->format('d/m/Y H:i'),
                    ] : null,
                ];
            });

        return response()->json([
            'message' => 'Danh sách đặt bàn hoàn tất',
            'data'    => $reservations->values(),
            'total'   => $reservations->count(),
        ]);
    }

    /**
     * Gửi hoặc cập nhật đánh giá
     * POST /api/reservations/{id}/review
     */
    public function store(StoreReviewRequest $request, Reservation $reservation): JsonResponse
    {
        $this->authorize('store', $reservation);

        $review = $reservation->review()->updateOrCreate(
            ['reservation_id' => $reservation->id],
            [
                'user_id' => auth()->id(),
                'rating'  => $request->rating,
                'comment' => $request->comment,
            ]
        );

        $action = $review->wasRecentlyCreated ? 'gửi' : 'cập nhật';

        return response()->json([
            'message' => "Đánh giá đã được {$action} thành công!",
            'data'    => new ReviewResource($review),
        ], 201);
    }

    /**
     * Xem đánh giá của một reservation
     * GET /api/reservations/{id}/review
     */
    public function show(Reservation $reservation): JsonResponse
    {
        $this->authorize('view', $reservation);

        $review = $reservation->review;

        return response()->json([
            'data' => $review ? new ReviewResource($review) : null,
        ]);
    }

    /**
     * Cập nhật đánh giá (dành cho chỉnh sửa)
     * PUT /api/reviews/{id}
     */
    public function update(Review $review, Request $request): JsonResponse
    {
        $this->authorize('update', $review);

        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        $review->update($request->only('rating', 'comment'));

        return response()->json([
            'message' => 'Cập nhật đánh giá thành công!',
            'data'    => new ReviewResource($review),
        ]);
    }

    /**
     * Xóa đánh giá
     * DELETE /api/reviews/{id}
     */
    public function destroy(Review $review): JsonResponse
    {
        $this->authorize('delete', $review);
        $review->delete();

        return response()->json([
            'message' => 'Xóa đánh giá thành công!',
        ]);
    }
}