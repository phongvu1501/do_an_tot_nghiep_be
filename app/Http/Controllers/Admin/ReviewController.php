<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // Liệt kê review (có phân trang, lọc, sắp xếp)
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);

        $query = Review::with(['user:id,name', 'reservation:id,reservation_code,reservation_date'])
            ->orderBy('created_at', 'desc');

        // bộ lọc
        if ($rating = $request->query('rating')) {
            $query->where('rating', (int)$rating);
        }
        if ($q = $request->query('q')) {
            $query->where('comment', 'like', "%{$q}%");
        }

        $reviews = $query->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => $reviews
        ]);
    }

    // Xoă review (soft delete)
    public function destroy($id)
    {
        $review = Review::find($id);

        if (!$review) {
            return response()->json([
                'status' => false,
                'message' => 'Review not found'
            ], 404);
        }

        $review->delete();

        return response()->json([
            'status' => true,
            'message' => 'Review deleted successfully'
        ]);
    }
}
