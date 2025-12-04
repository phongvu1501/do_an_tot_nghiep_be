<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;

class UserStatisticsController extends Controller
{
    public function index()
    {
        // Tổng số user
        $totalUsers = User::count();

        // User mới trong tháng
        $newUsersThisMonth = User::whereMonth('created_at', Carbon::now()->month)
                                ->whereYear('created_at', Carbon::now()->year)
                                ->count();

        // User từng đặt bàn (distinct)
        $usersWithReservation = User::whereHas('reservations')->count();

        // Top khách đặt bàn nhiều nhất
        $topBookingUsers = User::withCount('reservations')
                            ->orderBy('reservations_count', 'desc')
                            ->take(10)
                            ->get();

        // Top khách tiêu tiền nhiều nhất
        $topSpendingUsers = User::withSum('reservations as total_spent', 'total_amount')
                            ->orderBy('total_spent', 'desc')
                            ->take(10)
                            ->get();

        return view('admin.statistics.users', compact(
            'totalUsers',
            'newUsersThisMonth',
            'usersWithReservation',
            'topBookingUsers',
            'topSpendingUsers'
        ));
    }
}
