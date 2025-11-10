<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Reservation;
use App\Models\Review;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Class ReviewApiController
 * 
 * Xử lý API đánh giá cho lần đặt bàn
 */
class ReviewApiController extends Controller
{
    use AuthorizesRequests;
    public function index(Reservation $reservation)
    {
        $this->authorize('view', $reservation);
        $review = $reservation->review;

        return response()->json([
            'data' => $review ? [
                'id' => $review->id,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'created_at' => $review->created_at->format('d/m/Y H:i'),
            ] : null
        ]);
    }
    /**
     * Gửi đánh giá mới
     */
    public function store(StoreReviewRequest $request, Reservation $reservation): JsonResponse
    {
        
        $userId = auth()->id(); // ID user test trong bảng users

        $review = $reservation->review()->create([
            'user_id' => $userId,
            'rating'  => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'message' => 'Cảm ơn bạn đã đánh giá dịch vụ!',
            'data'    => new ReviewResource($review)
        ], 201);
    }

    /**
     * Xem đánh giá
     */
    public function show(Reservation $reservation): JsonResponse
    {
        $review = $reservation->review;

        return response()->json([
            'data' => $review ? new ReviewResource($review) : null
        ]);
    }

    public function update(Review $review, Request $request)
    {
        $this->authorize('update', $review);

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        $review->update($request->only('rating', 'comment'));

        return response()->json([
            'message' => 'Cập nhật đánh giá thành công!',
            'data' => $review
        ]);
    }

    public function destroy(Review $review)
    {
        $this->authorize('delete', $review);
        $review->delete();

        return response()->json([
            'message' => 'Xóa đánh giá thành công!'
        ]);
    }
}
