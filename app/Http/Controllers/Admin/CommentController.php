<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;

class CommentController extends Controller
{
    public function index()
    {
        $title = 'Quản lý bình luận';

        $comments = Review::with(['user', 'reservation'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.comment.index', compact('title', 'comments'));
    }

    public function toggleStatus(Review $review)
    {
        $review->status = $review->status == 1 ? 0 : 1;
        $review->save();

        return redirect()->back()->with('success', 'Cập nhật trạng thái bình luận thành công!');
    }

    
}
