<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\User;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Quản lý bình luận';

        $query = Review::with(['user', 'reservation']);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $comments = $query
            ->orderByDesc('created_at')
            ->paginate(20)
            ->appends($request->query());

        $users = User::orderBy('name')->get();

        return view('admin.comment.index', compact(
            'title',
            'comments',
            'users'
        ));
    }


    public function toggleStatus(Review $review)
    {
        $review->status = $review->status == 1 ? 0 : 1;
        $review->save();

        return redirect()->back()->with('success', 'Cập nhật trạng thái bình luận thành công!');
    }
}
