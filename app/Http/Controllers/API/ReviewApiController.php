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
 * API xử lý đánh giá
 */
class ReviewApiController extends Controller
{
    use AuthorizesRequests;

    /**
     * Danh sách reservation có thể đánh giá
     */
    public function index(): JsonResponse
    {
        $reservations = Reservation::where('user_id', auth()->id())
            ->where('status', 'completed')
            ->with(['review' => function ($q) {
                $q->where('status', 1);
            }])
            ->latest('reservation_date')
            ->get()
            ->map(function ($reservation) {
                $hasReview = $reservation->review()->where('status', 1)->exists();

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
                        'status'     => $reservation->review->status,
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
                'status'  => 1,
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
     */
    public function show(Reservation $reservation): JsonResponse
    {
        $this->authorize('view', $reservation);

        $review = $reservation->review()->where('status', 1)->first();

        return response()->json([
            'data' => $review ? new ReviewResource($review) : null,
        ]);
    }

    /**
     * Cập nhật đánh giá
     */
    public function update(StoreReviewRequest $request, Review $review)
    {
        $this->authorize('update', $review);

        $data = $request->validated();

        // 3. Cập nhật đánh giá
        $review->update([
            'rating'   => $data['rating'],
            'comment'  => $data['comment'],
            'status'   => $data['status'] ?? $review->status,
        ]);

        return new ReviewResource($review);
    }


    /**
     */
    public function hide(Review $review): JsonResponse
    {
        $this->authorize('updateStatus', $review);

        $review->update(['status' => 0]);

        return response()->json([
            'message' => 'Ẩn đánh giá thành công!',
        ]);
    }
}