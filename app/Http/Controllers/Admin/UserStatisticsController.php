<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class UserStatisticsController extends Controller
{
    public function index(Request $request)
    {
        $filterType = $request->get('filter', 'this_month');

        if ($filterType === 'today') {
            $from = Carbon::today();
            $to = Carbon::today()->endOfDay();
            $filterLabel = 'Hôm nay';
        } elseif ($filterType === 'this_week') {
            $from = Carbon::now()->startOfWeek();
            $to = Carbon::now()->endOfWeek();
            $filterLabel = 'Tuần này';
        } elseif ($filterType === 'this_year') {
            $from = Carbon::now()->startOfYear();
            $to = Carbon::now()->endOfYear();
            $filterLabel = 'Năm nay';
        } elseif ($filterType === 'custom' && $request->filled(['from', 'to'])) {
            $from = Carbon::parse($request->from)->startOfDay();
            $to = Carbon::parse($request->to)->endOfDay();
            $filterLabel = 'Tùy chọn';
        } else {
            $from = Carbon::now()->startOfMonth();
            $to = Carbon::now()->endOfMonth();
            $filterLabel = 'Tháng này';
        }

        $userQuery = User::whereBetween('created_at', [$from, $to]);


        $totalUsers = User::count();

        $newUsersThisMonth = $userQuery->count();

        $usersWithReservation = User::whereHas('reservations', function ($q) use ($from, $to) {
            $q->whereBetween('created_at', [$from, $to]);
        })->count();

        $users = $userQuery
            ->orderByDesc('created_at')
            ->paginate(10)
            ->appends($request->query());

        $topBookingUsers = User::with([
            'reservations.tables',
            'reservations.reservationItems.menu',
            'reservations.voucher'
        ])
            ->withCount([
                'reservations' => function ($q) use ($from, $to) {
                    $q->whereBetween('created_at', [$from, $to]);
                }
            ])
            ->orderByDesc('reservations_count')
            ->take(10)
            ->get();


        $topSpendingUsers = User::with([
            'reservations.tables',
            'reservations.reservationItems.menu',
            'reservations.voucher'
        ])
            ->withSum([
                'reservations as total_spent' => function ($q) use ($from, $to) {
                    $q->whereBetween('created_at', [$from, $to]);
                }
            ], 'total_amount')
            ->orderByDesc('total_spent')
            ->take(10)
            ->get();


        $chartData = User::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total')
        )
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $chartLabels = $chartData->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->format('d/m'))
            ->toArray();

        $chartUsers = DB::table('users')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total')
            )
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $chartUsersWithReservation = DB::table('reservations')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(DISTINCT user_id) as total')
            )
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $period = Carbon::parse($from)->daysUntil($to);

        $chartLabels = [];
        $newUsersData = [];
        $activeUsersData = [];
        $inactiveUsersData = [];

        foreach ($period as $date) {
            $d = $date->format('Y-m-d');
            $chartLabels[] = $date->format('d/m');

            $newUsers = $chartUsers[$d]->total ?? 0;
            $activeUsers = $chartUsersWithReservation[$d]->total ?? 0;

            $inactiveUsers = max($newUsers - $activeUsers, 0);

            $newUsersData[] = $newUsers;
            $activeUsersData[] = $activeUsers;
            $inactiveUsersData[] = $inactiveUsers;
        }


        return view('admin.statistics.users', compact(
            'users',
            'totalUsers',
            'newUsersThisMonth',
            'usersWithReservation',
            'topBookingUsers',
            'topSpendingUsers',
            'chartLabels',
            'chartUsers',
            'filterType',
            'filterLabel',
            'from',
            'to',
            'chartLabels',
            'newUsersData',
            'activeUsersData',
            'inactiveUsersData',
        ));
    }
}
