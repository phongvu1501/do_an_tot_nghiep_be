<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use Carbon\CarbonPeriod;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Voucher;

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

        $totalVouchersUsed = Reservation::whereBetween('created_at', [$from, $to])
            ->whereNotNull('voucher_id')
            ->count();

        $todayVouchers = now()->toDateString();

        $activeVouchers = Voucher::where('status', 'active')
            ->whereDate('start_date', '<=', $todayVouchers)
            ->whereDate('end_date', '>=', $todayVouchers)
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
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $totalToday = Reservation::whereDate('reservation_date', $today)->count();

        $totalThisMonth = Reservation::whereBetween('reservation_date', [$startOfMonth, $endOfMonth])->count();

        $totalCompletedAll = Reservation::where('status', 'completed')->count();

        $totalCancelledAll = Reservation::where('status', 'cancelled')->count();

        $totalPending = Reservation::where('status', 'pending')->count();

        //Tỷ lệ hủy= đơn hủy / tổng đơn
        $totalAllReservations = Reservation::count();
        $cancellationRate = $totalAllReservations > 0
            ? round(($totalCancelledAll / $totalAllReservations) * 100, 2)
            : 0;

        // Số đơn theo từng ca
        $reservationsByShift = Reservation::selectRaw('shift, COUNT(*) as count')
            ->groupBy('shift')
            ->get()
            ->pluck('count', 'shift')
            ->toArray();

        $morningCount = $reservationsByShift['morning'] ?? 0;
        $afternoonCount = $reservationsByShift['afternoon'] ?? 0;
        $eveningCount = $reservationsByShift['evening'] ?? 0;


        $dailyStatistics = [];
        foreach ($period as $date) {
            $dailyStatistics[] = [
                'date' => $date->format('Y-m-d'),
                'date_display' => $date->format('d/m/Y'),
                'count' => Reservation::whereDate('reservation_date', $date)->count(),
                'completed' => Reservation::whereDate('reservation_date', $date)
                    ->where('status', 'completed')
                    ->count(),
                'cancelled' => Reservation::whereDate('reservation_date', $date)
                    ->where('status', 'cancelled')
                    ->count(),
                'pending' => Reservation::whereDate('reservation_date', $date)
                    ->where('status', 'pending')
                    ->count(),
            ];
        }

        $avgNumPeople = Reservation::whereNotNull('num_people')
            ->avg('num_people');
        $avgNumPeople = $avgNumPeople ? round($avgNumPeople, 2) : 0;

        $shiftStatsInPeriod = Reservation::whereBetween('reservation_date', [$from, $to])
            ->selectRaw('shift, COUNT(*) as count')
            ->groupBy('shift')
            ->get()
            ->pluck('count', 'shift')
            ->toArray();

        $morningCountPeriod = $shiftStatsInPeriod['morning'] ?? 0;
        $afternoonCountPeriod = $shiftStatsInPeriod['afternoon'] ?? 0;
        $eveningCountPeriod = $shiftStatsInPeriod['evening'] ?? 0;

        $reservationChartLabels = collect($dailyStatistics)->pluck('date_display')->toArray();
        $reservationChartData = collect($dailyStatistics)->pluck('count')->toArray();
        $reservationChartCompleted = collect($dailyStatistics)->pluck('completed')->toArray();
        $reservationChartCancelled = collect($dailyStatistics)->pluck('cancelled')->toArray();
        $reservationChartPending = collect($dailyStatistics)->pluck('pending')->toArray();
        $totalUsers = User::count();
        $newUsersThisMonth = User::whereBetween('created_at', [$from, $to])->count();
        $usersWithReservation = User::whereHas('reservations')->count();

        $topBookingUsers = User::withCount('reservations')
            ->orderByDesc('reservations_count')
            ->take(5)
            ->get();

        $topSpendingUsers = User::select(
            'users.id',
            'users.name',
            DB::raw('SUM(reservations.total_amount) as total_spent')
        )
            ->join('reservations', 'reservations.user_id', '=', 'users.id')
            ->where('reservations.status', 'completed')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_spent')
            ->take(5)
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
            'totalVouchersUsed',
            'todayVouchers',
            'activeVouchers',
            'tablesToday',
            'from',
            'to',
            // Thống kê đặt bàn
            'totalToday',
            'totalThisMonth',
            'totalCompletedAll',
            'totalCancelledAll',
            'totalPending',
            'cancellationRate',
            'morningCount',
            'afternoonCount',
            'eveningCount',
            'morningCountPeriod',
            'afternoonCountPeriod',
            'eveningCountPeriod',
            'dailyStatistics',
            'avgNumPeople',
            'reservationChartLabels',
            'reservationChartData',
            'reservationChartCompleted',
            'reservationChartCancelled',
            'reservationChartPending',


            'totalUsers',
            'newUsersThisMonth',
            'usersWithReservation',
            'topBookingUsers',
            'topSpendingUsers',

        ));
    }


    public function reservationStatistics(Request $request)
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $filterType = $request->input('filter', 'this_month'); // today, this_week, this_month, this_year, custom

        switch ($filterType) {
            case 'today':
                $from = Carbon::today();
                $to = Carbon::today()->endOfDay();
                $filterLabel = 'Hôm nay';
                break;

            case 'this_week':
                $from = Carbon::now()->startOfWeek();
                $to = Carbon::now()->endOfWeek();
                $filterLabel = 'Tuần này';
                break;

            case 'this_month':
                $from = Carbon::now()->startOfMonth();
                $to = Carbon::now()->endOfMonth();
                $filterLabel = 'Tháng này';
                break;

            case 'this_year':
                $from = Carbon::now()->startOfYear();
                $to = Carbon::now()->endOfYear();
                $filterLabel = 'Năm nay';
                break;

            case 'custom':
                $from = $request->input('from') ? Carbon::parse($request->input('from'))->startOfDay() : Carbon::now()->startOfMonth();
                $to = $request->input('to') ? Carbon::parse($request->input('to'))->endOfDay() : Carbon::now()->endOfMonth();
                $filterLabel = $from->format('d/m/Y') . ' - ' . $to->format('d/m/Y');
                break;

            case 'all':
                $from = Reservation::min('reservation_date') ? Carbon::parse(Reservation::min('reservation_date')) : Carbon::now()->startOfYear();
                $to = Reservation::max('reservation_date') ? Carbon::parse(Reservation::max('reservation_date')) : Carbon::now()->endOfYear();
                $filterLabel = 'Tất cả';
                break;

            default:
                $from = Carbon::now()->startOfMonth();
                $to = Carbon::now()->endOfMonth();
                $filterLabel = 'Tháng này';
        }


        $totalReservationsInPeriod = Reservation::whereBetween('reservation_date', [$from->toDateString(), $to->toDateString()])
            ->count();

        $totalCompleted = Reservation::whereBetween('reservation_date', [$from->toDateString(), $to->toDateString()])
            ->where('status', 'completed')
            ->count();

        $totalCancelled = Reservation::whereBetween('reservation_date', [$from->toDateString(), $to->toDateString()])
            ->where('status', 'cancelled')
            ->count();

        $totalPending = Reservation::whereBetween('reservation_date', [$from->toDateString(), $to->toDateString()])
            ->where('status', 'pending')
            ->count();

        $cancellationRate = $totalReservationsInPeriod > 0
            ? round(($totalCancelled / $totalReservationsInPeriod) * 100, 2)
            : 0;

        $reservationsByShift = Reservation::where('status', 'completed')
            ->selectRaw('shift, COUNT(*) as count, SUM(num_people) as total_people')
            ->groupBy('shift')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->shift => [
                    'count' => $item->count,
                    'total_people' => $item->total_people ?? 0
                ]];
            })
            ->toArray();

        $morningCount = isset($reservationsByShift['morning']) ? $reservationsByShift['morning']['count'] : 0;
        $afternoonCount = isset($reservationsByShift['afternoon']) ? $reservationsByShift['afternoon']['count'] : 0;
        $eveningCount = isset($reservationsByShift['evening']) ? $reservationsByShift['evening']['count'] : 0;

        $morningPeople = isset($reservationsByShift['morning']) ? ($reservationsByShift['morning']['total_people'] ?? 0) : 0;
        $afternoonPeople = isset($reservationsByShift['afternoon']) ? ($reservationsByShift['afternoon']['total_people'] ?? 0) : 0;
        $eveningPeople = isset($reservationsByShift['evening']) ? ($reservationsByShift['evening']['total_people'] ?? 0) : 0;

        $period = CarbonPeriod::create($from, '1 day', $to);
        $dailyStatistics = [];

        foreach ($period as $date) {
            $dailyStatistics[] = [
                'date' => $date->format('Y-m-d'),
                'date_display' => $date->format('d/m/Y'),
                'count' => Reservation::whereDate('reservation_date', $date)->count(),
                'completed' => Reservation::whereDate('reservation_date', $date)
                    ->where('status', 'completed')
                    ->count(),
                'cancelled' => Reservation::whereDate('reservation_date', $date)
                    ->where('status', 'cancelled')
                    ->count(),
                'pending' => Reservation::whereDate('reservation_date', $date)
                    ->where('status', 'pending')
                    ->count(),
                'total_people' => Reservation::whereDate('reservation_date', $date)
                    ->where('status', 'completed')
                    ->whereNotNull('num_people')
                    ->sum('num_people') ?? 0,
                'morning_people' => Reservation::whereDate('reservation_date', $date)
                    ->where('status', 'completed')
                    ->where('shift', 'morning')
                    ->whereNotNull('num_people')
                    ->sum('num_people') ?? 0,
                'afternoon_people' => Reservation::whereDate('reservation_date', $date)
                    ->where('status', 'completed')
                    ->where('shift', 'afternoon')
                    ->whereNotNull('num_people')
                    ->sum('num_people') ?? 0,
                'evening_people' => Reservation::whereDate('reservation_date', $date)
                    ->where('status', 'completed')
                    ->where('shift', 'evening')
                    ->whereNotNull('num_people')
                    ->sum('num_people') ?? 0,
            ];
        }

        $avgNumPeople = Reservation::whereBetween('reservation_date', [$from->toDateString(), $to->toDateString()])
            ->where('status', 'completed')
            ->whereNotNull('num_people')
            ->avg('num_people');
        $avgNumPeople = $avgNumPeople ? round($avgNumPeople, 2) : 0;

        $totalGuests = Reservation::whereBetween('reservation_date', [$from->toDateString(), $to->toDateString()])
            ->where('status', 'completed')
            ->whereNotNull('num_people')
            ->sum('num_people');
        $totalGuests = $totalGuests ?? 0;

        $shiftStatsInPeriod = Reservation::whereBetween('reservation_date', [$from->toDateString(), $to->toDateString()])
            ->where('status', 'completed')
            ->selectRaw('shift, COUNT(*) as count, SUM(num_people) as total_people')
            ->groupBy('shift')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->shift => [
                    'count' => $item->count,
                    'total_people' => $item->total_people ?? 0
                ]];
            })
            ->toArray();

        $morningCountPeriod = isset($shiftStatsInPeriod['morning']) ? $shiftStatsInPeriod['morning']['count'] : 0;
        $afternoonCountPeriod = isset($shiftStatsInPeriod['afternoon']) ? $shiftStatsInPeriod['afternoon']['count'] : 0;
        $eveningCountPeriod = isset($shiftStatsInPeriod['evening']) ? $shiftStatsInPeriod['evening']['count'] : 0;

        $morningPeoplePeriod = isset($shiftStatsInPeriod['morning']) ? ($shiftStatsInPeriod['morning']['total_people'] ?? 0) : 0;
        $afternoonPeoplePeriod = isset($shiftStatsInPeriod['afternoon']) ? ($shiftStatsInPeriod['afternoon']['total_people'] ?? 0) : 0;
        $eveningPeoplePeriod = isset($shiftStatsInPeriod['evening']) ? ($shiftStatsInPeriod['evening']['total_people'] ?? 0) : 0;

        $chartLabels = collect($dailyStatistics)->pluck('date_display')->toArray();
        $chartData = collect($dailyStatistics)->pluck('count')->toArray();
        $chartCompleted = collect($dailyStatistics)->pluck('completed')->toArray();
        $chartCancelled = collect($dailyStatistics)->pluck('cancelled')->toArray();
        $chartPending = collect($dailyStatistics)->pluck('pending')->toArray();
        $chartGuests = collect($dailyStatistics)->pluck('total_people')->toArray();
        $chartGuestsMorning = collect($dailyStatistics)->pluck('morning_people')->toArray();
        $chartGuestsAfternoon = collect($dailyStatistics)->pluck('afternoon_people')->toArray();
        $chartGuestsEvening = collect($dailyStatistics)->pluck('evening_people')->toArray();

        return view('admin.reservationStatistics.index', compact(
            'totalReservationsInPeriod',
            'totalCompleted',
            'totalCancelled',
            'totalPending',
            'cancellationRate',
            'morningCount',
            'afternoonCount',
            'eveningCount',
            'morningPeople',
            'afternoonPeople',
            'eveningPeople',
            'morningCountPeriod',
            'afternoonCountPeriod',
            'eveningCountPeriod',
            'morningPeoplePeriod',
            'afternoonPeoplePeriod',
            'eveningPeoplePeriod',
            'dailyStatistics',
            'avgNumPeople',
            'totalGuests',
            'filterType',
            'filterLabel',
            'from',
            'to',
            'chartLabels',
            'chartData',
            'chartCompleted',
            'chartCancelled',
            'chartPending',
            'chartGuests',
            'chartGuestsMorning',
            'chartGuestsAfternoon',
            'chartGuestsEvening'
        ));
    }
    public function voucherStatistics(Request $request)
    {
        $filterType = $request->input('filter', 'this_month');

        switch ($filterType) {
            case 'today':
                $from = Carbon::today();
                $to = Carbon::today()->endOfDay();
                $filterLabel = 'Hôm nay';
                break;

            case 'this_week':
                $from = Carbon::now()->startOfWeek();
                $to = Carbon::now()->endOfWeek();
                $filterLabel = 'Tuần này';
                break;

            case 'this_month':
                $from = Carbon::now()->startOfMonth();
                $to = Carbon::now()->endOfMonth();
                $filterLabel = 'Tháng này';
                break;

            case 'this_year':
                $from = Carbon::now()->startOfYear();
                $to = Carbon::now()->endOfYear();
                $filterLabel = 'Năm nay';
                break;

            case 'custom':
                $from = $request->input('from')
                    ? Carbon::parse($request->input('from'))->startOfDay()
                    : Carbon::now()->startOfMonth();

                $to = $request->input('to')
                    ? Carbon::parse($request->input('to'))->endOfDay()
                    : Carbon::now()->endOfMonth();

                $filterLabel = $from->format('d/m/Y') . ' - ' . $to->format('d/m/Y');
                break;

            case 'all':
                $from = Voucher::min('created_at')
                    ? Carbon::parse(Voucher::min('created_at'))
                    : Carbon::now()->startOfYear();

                $to = Voucher::max('created_at')
                    ? Carbon::parse(Voucher::max('created_at'))
                    : Carbon::now()->endOfYear();

                $filterLabel = 'Tất cả';
                break;

            default:
                $from = Carbon::now()->startOfMonth();
                $to = Carbon::now()->endOfMonth();
                $filterLabel = 'Tháng này';
        }


        $totalVouchersUsed = Reservation::whereNotNull('voucher_id')
            ->whereBetween('created_at', [$from, $to])
            ->count();

        $totalVoucherCreated = Voucher::whereBetween('created_at', [$from, $to])->count();

        $expiredVoucher = Voucher::whereBetween('end_date', [$from, $to])
            ->where('end_date', '<', Carbon::now())
            ->count();

        $activeVouchers = Voucher::where('status', 'active')
            ->whereDate('start_date', '<=', $to)
            ->whereDate('end_date', '>=', $from)
            ->orderBy('created_at', 'desc')
            ->get();

        $period = CarbonPeriod::create($from, '1 day', $to);

        $dailyStatistics = [];

        foreach ($period as $date) {
            $dailyStatistics[] = [
                'date' => $date->format('Y-m-d'),
                'date_display' => $date->format('d/m/Y'),

                'created' => Voucher::whereDate('created_at', $date)->count(),

                'expired' => Voucher::whereDate('end_date', $date)
                    ->where('end_date', '<', Carbon::now())
                    ->count(),

                'used' => Reservation::whereNotNull('voucher_id')
                    ->whereDate('created_at', $date)
                    ->count(),
            ];
        }

        $chartLabels = collect($dailyStatistics)->pluck('date_display')->toArray();
        $chartCreated = collect($dailyStatistics)->pluck('created')->toArray();
        $chartExpired = collect($dailyStatistics)->pluck('expired')->toArray();
        $chartUsed = collect($dailyStatistics)->pluck('used')->toArray();

        return view('admin.voucherStatistics.index', compact(
            'filterType',
            'filterLabel',
            'from',
            'to',
            'totalVouchersUsed',
            'totalVoucherCreated',
            'expiredVoucher',
            'activeVouchers',
            'dailyStatistics',
            'chartLabels',
            'chartCreated',
            'chartExpired',
            'chartUsed'
        ));
    }
}
