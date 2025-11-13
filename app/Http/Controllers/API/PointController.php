<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\PointLog;

class PointController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * API: Tích điểm cho người dùng hiện tại
     */
    public function addPoints(Request $request)
    {
        $validated = $request->validate([
            'points' => 'required|integer|min:1',
            'action' => 'nullable|string',
        ]);

        $user = Auth::user();

        $user->increment('points', $validated['points']);

        PointLog::create([
            'user_id' => $user->id,
            'points' => $validated['points'],
            'action' => $validated['action'] ?? 'Tích điểm thủ công',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Tích điểm thành công!',
            'data' => [
                'user_id' => $user->id,
                'total_points' => $user->points,
            ],
        ]);
    }

    /**
     * API: Admin cộng điểm cho người dùng khác
     */
    public function adminAddPoints(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'points' => 'required|integer|min:1',
            'action' => 'nullable|string',
        ]);

        $user = User::find($validated['user_id']);
        $user->increment('points', $validated['points']);

        PointLog::create([
            'user_id' => $user->id,
            'points' => $validated['points'],
            'action' => $validated['action'] ?? 'Thưởng điểm bởi admin',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Admin đã cộng điểm cho người dùng thành công!',
            'data' => [
                'username' => $user->username ?? $user->email,
                'total_points' => $user->points,
            ],
        ]);
    }

    /**
     * API: Lịch sử tích điểm
     */
    public function history()
    {
        $user = Auth::user();
        $logs = PointLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Lịch sử tích điểm của bạn',
            'data' => $logs,
        ]);
    }
}
