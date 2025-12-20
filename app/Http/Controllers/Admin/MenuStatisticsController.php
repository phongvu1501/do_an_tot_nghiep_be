<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuStatisticsController extends Controller
{
    public function index(Request $request)
    {

        $filterType = $request->get('filter', 'this_month');

        [$from, $to, $filterType, $filterLabel] = $this->resolveDateRange($request);

        // 1. Tổng số món (chưa bị soft delete)
        $totalMenus = Menu::whereNull('deleted_at')->count();

        // 2. Query nguồn dữ liệu (gom từ nhiều bảng)
        $menuStats = $this
            ->buildMenuStatisticsQuery($from, $to)
            ->paginate(10)
            ->withQueryString();

        // 3. Món bán chạy nhất
        $bestSelling = $menuStats->sortByDesc('total_qty')->first();

        // 4. Nhóm món được chọn nhiều nhất
        $topCategory = $menuStats
            ->groupBy('category_id')
            ->map(function ($items) {
                return [
                    'name' => optional($items->first())->category_name,
                    'total_qty' => $items->sum('total_qty'),
                ];
            })
            ->sortByDesc('total_qty')
            ->first();

        return view('admin.menuStatistics.index', compact(
            'totalMenus',
            'menuStats',
            'bestSelling',
            'topCategory',
            'from',
            'to',
            'filterType',
            'filterLabel'
        ));
    }

    /**
     * =========================
     * BUILD MENU STATISTICS
     * =========================
     */
    private function buildMenuStatisticsQuery(Carbon $from, Carbon $to)
    {
        // Order items (đã thanh toán)
        $orderItems = DB::table('order_items as oi')
            ->join('orders as o', 'o.id', '=', 'oi.order_id')
            ->selectRaw('
                oi.menu_id,
                SUM(oi.quantity) as qty,
                SUM(oi.quantity * oi.price) as revenue
            ')
            ->where('o.payment_status', 'paid')
            ->whereBetween('o.created_at', [$from, $to])
            ->groupBy('oi.menu_id');

        // Reservation items (đơn hoàn tất)
        $reservationItems = DB::table('reservation_items as ri')
            ->join('reservations as r', 'r.id', '=', 'ri.reservation_id')
            ->selectRaw('
                ri.menu_id,
                SUM(ri.quantity) as qty,
                SUM(ri.quantity * ri.price) as revenue
            ')
            ->where('r.status', 'completed')
            ->whereBetween('r.created_at', [$from, $to])
            ->groupBy('ri.menu_id');

        // Reservation menu (không có price → lấy từ menus)
        $reservationMenu = DB::table('reservation_menu as rm')
            ->join('reservations as r', 'r.id', '=', 'rm.reservation_id')
            ->join('menus as m', 'm.id', '=', 'rm.menu_id')
            ->selectRaw('
                rm.menu_id,
                SUM(rm.quantity) as qty,
                SUM(rm.quantity * m.price) as revenue
            ')
            ->where('r.status', 'completed')
            ->whereBetween('r.created_at', [$from, $to])
            ->groupBy('rm.menu_id');

        // UNION ALL
        $unionQuery = $orderItems
            ->unionAll($reservationItems)
            ->unionAll($reservationMenu);

        // QUERY CUỐI
        return DB::query()
            ->fromSub($unionQuery, 't')
            ->join('menus as m', 'm.id', '=', 't.menu_id')
            ->join('menu_categories as c', 'c.id', '=', 'm.category_id')
            ->whereNull('m.deleted_at')
            ->selectRaw('
                m.id,
                m.name,
                m.image,
                m.category_id,
                c.name as category_name,
                SUM(t.qty) as total_qty,
                SUM(t.revenue) as revenue
            ')
            ->groupBy('m.id', 'm.name', 'm.image', 'm.category_id', 'c.name')
            ->orderByDesc('revenue');
    }

    /**
     * =========================
     * RESOLVE DATE RANGE
     * =========================
     */
    private function resolveDateRange(Request $request): array
    {
        $filterType = $request->get('filter', 'this_month');

        switch ($filterType) {
            case 'today':
                $from = Carbon::today();
                $to   = Carbon::today();
                $filterLabel = 'Hôm nay';
                break;

            case 'this_week':
                $from = Carbon::now()->startOfWeek();
                $to   = Carbon::now()->endOfWeek();
                $filterLabel = 'Tuần này';
                break;

            case 'this_year':
                $from = Carbon::now()->startOfYear();
                $to   = Carbon::now()->endOfYear();
                $filterLabel = 'Năm nay';
                break;

            case 'custom':
                $from = Carbon::parse($request->from)->startOfDay();
                $to   = Carbon::parse($request->to)->endOfDay();
                $filterLabel = 'Tùy chọn';
                break;

            default: // this_month
                $from = Carbon::now()->startOfMonth();
                $to   = Carbon::now()->endOfMonth();
                $filterLabel = 'Tháng này';
                break;
        }

        return [$from, $to, $filterType, $filterLabel];
    }
}
