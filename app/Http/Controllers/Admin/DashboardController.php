<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $dashboard = "Trang thống kê";

        // Lấy filter thời gian từ request, nếu không có thì mặc định ví dụ 30 ngày
        $from = $request->input('from') ? Carbon::parse($request->input('from')) : Carbon::now()->subDays(30);
        $to = $request->input('to') ? Carbon::parse($request->input('to')) : Carbon::now();

        // --- TỔNG QUAN ---
        $totalReservations = Reservation::whereBetween('created_at', [$from, $to])->count();

        $totalCancelled = Reservation::whereBetween('created_at', [$from, $to])
            ->where('status', 'cancelled')
            ->count();

        $totalCompleted = Reservation::whereBetween('created_at', [$from, $to])
            ->where('status', 'completed')
            ->count();

        $totalRevenue = Reservation::whereBetween('created_at', [$from, $to])
            ->where('status', 'completed')
            ->sum('total_amount');

        $newUsers = User::whereBetween('created_at', [$from, $to])->count();

        // --- BIỂU ĐỒ THEO NGÀY ---
        $period = CarbonPeriod::create($from, '1 day', $to);

        $chartReserved = [];     // Đặt mới
        $chartCancelled = [];    // Hủy
        $chartCompleted = [];    // Hoàn thành
        $chartRevenue = [];      // Doanh thu theo ngày
        $chartNewUsers = [];     // User mới

        foreach ($period as $date) {
            $chartReserved[] = Reservation::whereDate('created_at', $date)->count();

            $chartCancelled[] = Reservation::whereDate('created_at', $date)
                ->where('status', 'cancelled')
                ->count();

            $chartCompleted[] = Reservation::whereDate('created_at', $date)
                ->where('status', 'completed')
                ->count();

            $chartRevenue[] = Reservation::whereDate('created_at', $date)
                ->where('status', 'completed')
                ->sum('total_amount');

            $chartNewUsers[] = User::whereDate('created_at', $date)->count();
        }

        $labels = collect($period)->map(fn($d) => $d->format('Y-m-d'))->toArray();

        return view('admin.layouts.dashboard', compact(
            'dashboard',
            
            'totalReservations',
            'totalCancelled',
            'totalCompleted',
            'totalRevenue',
            'newUsers',

            'labels',
            'chartReserved',
            'chartCancelled',
            'chartCompleted',
            'chartRevenue',
            'chartNewUsers',

            'from',
            'to'
        ));

        // return view('admin.layouts.dashboard', compact('dashboard'));
    }
}
