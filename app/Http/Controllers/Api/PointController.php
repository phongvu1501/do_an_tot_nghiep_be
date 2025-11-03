<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PointController extends Controller
{
    public function addPoints(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'order_total' => 'required|numeric|min:0',
        ]);

        $user = User::find($request->user_id);

        $earnedPoints = floor($request->order_total / 10000);

        $user->increment('points', $earnedPoints);

        return response()->json([
            'message' => 'Cộng điểm thành công',
            'earned_points' => $earnedPoints,
            'total_points' => $user->points,
        ]);
    }

    public function usePoints(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'points_to_use' => 'required|integer|min:1',
        ]);

        $user = User::find($request->user_id);

        if ($user->points < $request->points_to_use) {
            return response()->json(['message' => 'Bạn không đủ điểm để sử dụng'], 400);
        }

        $user->decrement('points', $request->points_to_use);

        return response()->json([
            'message' => 'Sử dụng điểm thành công',
            'used_points' => $request->points_to_use,
            'remaining_points' => $user->points,
        ]);
    }

    public function getPoints($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'Không tìm thấy người dùng'], 404);
        }

        return response()->json([
            'user_id' => $user->id,
            'total_points' => $user->points,
        ]);
    }
}
