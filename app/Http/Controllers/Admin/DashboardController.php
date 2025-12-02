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

        // Lấy filter thời gian từ request, nếu không có thì mặc định 30 ngày
        $from = $request->input('from') ? Carbon::parse($request->input('from')) : Carbon::now()->subDays(30);
        $to = $request->input('to') ? Carbon::parse($request->input('to')) : Carbon::now();

        // --- TỔNG QUAN ---
        $totalReservations = Reservation::whereBetween('created_at', [$from, $to])->count();
        $reservationsList = Reservation::whereBetween('created_at', [$from, $to])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalCancelled = Reservation::whereBetween('created_at', [$from, $to])
            ->where('status', 'cancelled')
            ->count();
        $cancelledList = Reservation::whereBetween('created_at', [$from, $to])
            ->where('status', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->get();

        $totalCompleted = Reservation::whereBetween('created_at', [$from, $to])
            ->where('status', 'completed')
            ->count();
        $totalRevenue = Reservation::whereBetween('created_at', [$from, $to])
            ->where('status', 'completed')
            ->sum('total_amount');

        $newUsers = User::whereBetween('created_at', [$from, $to])->count();
        $listUsers = User::whereBetween('created_at', [$from, $to])
            ->orderBy('created_at', 'desc')
            ->get();

        // --- BIỂU ĐỒ THEO NGÀY ---
        $period = CarbonPeriod::create($from, '1 day', $to);

        $chartReserved = [];
        $chartCancelled = [];
        $chartCompleted = [];
        $chartRevenue = [];
        $chartNewUsers = [];

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

        // --- CÁC BÀN ĐẶT TRONG KHOẢNG FILTER ---
        $tablesToday = Reservation::with(['tables', 'user'])
            ->whereBetween('reservation_date', [$from, $to])
            ->orderBy('reservation_date', 'asc')
            ->orderBy('shift', 'asc')
            ->get();

        return view('admin.layouts.dashboard', compact(
            'dashboard',
            'totalReservations',
            'reservationsList',
            'totalCancelled',
            'cancelledList',
            'totalCompleted',
            'totalRevenue',
            'newUsers',
            'listUsers',
            'labels',
            'chartReserved',
            'chartCancelled',
            'chartCompleted',
            'chartRevenue',
            'chartNewUsers',
            'tablesToday',
            'from',
            'to'
        ));
    }
}
