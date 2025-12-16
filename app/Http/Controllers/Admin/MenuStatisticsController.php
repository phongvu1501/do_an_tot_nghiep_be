<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Menu;
use Carbon\Carbon;

class MenuStatisticsController extends Controller
{
    /**
     * Subquery thống kê chung cho menu
     * Gom 3 nguồn: order_items, reservation_items, reservation_menu
     */
    private function menuSalesQuery($from = null, $to = null)
    {
        $order = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->select(
                'order_items.menu_id',
                DB::raw('SUM(order_items.quantity) as qty'),
                DB::raw('SUM(order_items.price * order_items.quantity) as revenue')
            )
            ->where('orders.payment_status', 'paid')
            ->when($from && $to, fn ($q) =>
                $q->whereBetween('orders.created_at', [$from, $to])
            )
            ->groupBy('order_items.menu_id');

        $reservationItem = DB::table('reservation_items')
            ->join('reservations', 'reservations.id', '=', 'reservation_items.reservation_id')
            ->select(
                'reservation_items.menu_id',
                DB::raw('SUM(reservation_items.quantity) as qty'),
                DB::raw('SUM(reservation_items.price * reservation_items.quantity) as revenue')
            )
            ->where('reservations.status', 'completed')
            ->when($from && $to, fn ($q) =>
                $q->whereBetween('reservations.created_at', [$from, $to])
            )
            ->groupBy('reservation_items.menu_id');

        $reservationMenu = DB::table('reservation_menu')
            ->join('reservations', 'reservations.id', '=', 'reservation_menu.reservation_id')
            ->join('menus', 'menus.id', '=', 'reservation_menu.menu_id')
            ->select(
                'reservation_menu.menu_id',
                DB::raw('SUM(reservation_menu.quantity) as qty'),
                DB::raw('SUM(reservation_menu.quantity * menus.price) as revenue')
            )
            ->where('reservations.status', 'completed')
            ->when($from && $to, fn ($q) =>
                $q->whereBetween('reservations.created_at', [$from, $to])
            )
            ->groupBy('reservation_menu.menu_id');

        return $order
            ->unionAll($reservationItem)
            ->unionAll($reservationMenu);
    }

    public function index(Request $request)
    {
        $fromCarbon = $request->input('from') ? Carbon::parse($request->input('from'))->startOfDay() : null;
        $toCarbon   = $request->input('to')   ? Carbon::parse($request->input('to'))->endOfDay()   : null;

        $from = $fromCarbon ? $fromCarbon->toDateTimeString() : null;
        $to   = $toCarbon   ? $toCarbon->toDateTimeString()   : null;

        $orderDateWhere = ($from && $to) ? " AND o.created_at BETWEEN ? AND ? " : "";
        $resDateWhere   = ($from && $to) ? " AND r.created_at BETWEEN ? AND ? " : "";
        $res2DateWhere  = ($from && $to) ? " AND r2.created_at BETWEEN ? AND ? " : "";

        $bindings = ($from && $to) ? [$from, $to] : [];

        // 1) Total active menus
        $totalMenus = Menu::where('status', 1)->count();

        // 2) Best selling
        $bestSellingSql = "
            SELECT m.id, m.name, m.image, COALESCE(s.total_sold,0) AS total_sold
            FROM menus m
            LEFT JOIN (
                SELECT menu_id, SUM(qty) AS total_sold FROM (
                    SELECT oi.menu_id, SUM(oi.quantity) AS qty
                    FROM order_items oi
                    JOIN orders o ON o.id = oi.order_id
                    WHERE o.payment_status = 'paid' {$orderDateWhere}
                    GROUP BY oi.menu_id
                    UNION ALL
                    SELECT ri.menu_id, SUM(ri.quantity) AS qty
                    FROM reservation_items ri
                    JOIN reservations r ON r.id = ri.reservation_id
                    WHERE r.status = 'completed' {$resDateWhere}
                    GROUP BY ri.menu_id
                    UNION ALL
                    SELECT rm.menu_id, SUM(rm.quantity) AS qty
                    FROM reservation_menu rm
                    JOIN reservations r2 ON r2.id = rm.reservation_id
                    WHERE r2.status = 'completed' {$res2DateWhere}
                    GROUP BY rm.menu_id
                ) t
                GROUP BY menu_id
            ) s ON s.menu_id = m.id
            ORDER BY s.total_sold DESC
            LIMIT 1
        ";

        $bestSellingBindings = ($from && $to) ? array_merge($bindings, $bindings, $bindings) : [];
        $bestSellingRow = DB::selectOne($bestSellingSql, $bestSellingBindings);
        $bestSelling = $bestSellingRow ? (array) $bestSellingRow : null;

        // 3) Revenue per item
        $revenueSql = "
            SELECT m.id, m.name, m.image,
                   COALESCE(ois.rev,0) + COALESCE(ris.rev,0) + COALESCE(rms.rev,0) AS revenue,
                   COALESCE(ois.qty,0) + COALESCE(ris.qty,0) + COALESCE(rms.qty,0) AS total_qty
            FROM menus m
            LEFT JOIN (
                SELECT oi.menu_id, SUM(oi.price * oi.quantity) AS rev, SUM(oi.quantity) AS qty
                FROM order_items oi
                JOIN orders o ON o.id = oi.order_id
                WHERE o.payment_status = 'paid' {$orderDateWhere}
                GROUP BY oi.menu_id
            ) ois ON ois.menu_id = m.id
            LEFT JOIN (
                SELECT ri.menu_id, SUM(ri.price * ri.quantity) AS rev, SUM(ri.quantity) AS qty
                FROM reservation_items ri
                JOIN reservations r ON r.id = ri.reservation_id
                WHERE r.status = 'completed' {$resDateWhere}
                GROUP BY ri.menu_id
            ) ris ON ris.menu_id = m.id
            LEFT JOIN (
                SELECT rm.menu_id, SUM(rm.quantity * m2.price) AS rev, SUM(rm.quantity) AS qty
                FROM reservation_menu rm
                JOIN reservations r2 ON r2.id = rm.reservation_id
                JOIN menus m2 ON m2.id = rm.menu_id
                WHERE r2.status = 'completed' {$res2DateWhere}
                GROUP BY rm.menu_id
            ) rms ON rms.menu_id = m.id
            ORDER BY revenue DESC
            LIMIT 50
        ";

        $revenueBindings = ($from && $to) ? array_merge($bindings, $bindings, $bindings) : [];
        $revenueRows = DB::select($revenueSql, $revenueBindings);
        $revenuePerItem = array_map(fn($r) => (array)$r, $revenueRows);

        // 4) Top category
        $topCategorySql = "
            SELECT c.id, c.name, COALESCE(s.sum_qty,0) AS total_qty
            FROM menu_categories c
            LEFT JOIN (
                SELECT m.category_id, SUM(t.q) AS sum_qty FROM (
                    SELECT oi.menu_id, SUM(oi.quantity) AS q
                    FROM order_items oi
                    JOIN orders o ON o.id = oi.order_id
                    WHERE o.payment_status='paid' {$orderDateWhere}
                    GROUP BY oi.menu_id
                    UNION ALL
                    SELECT ri.menu_id, SUM(ri.quantity) AS q
                    FROM reservation_items ri
                    JOIN reservations r ON r.id = ri.reservation_id
                    WHERE r.status='completed' {$resDateWhere}
                    GROUP BY ri.menu_id
                    UNION ALL
                    SELECT rm.menu_id, SUM(rm.quantity) AS q
                    FROM reservation_menu rm
                    JOIN reservations r2 ON r2.id = rm.reservation_id
                    WHERE r2.status='completed' {$res2DateWhere}
                    GROUP BY rm.menu_id
                ) t
                JOIN menus m ON m.id = t.menu_id
                GROUP BY m.category_id
            ) s ON s.category_id = c.id
            ORDER BY total_qty DESC
            LIMIT 1
        ";

        $topCategoryBindings = ($from && $to) ? array_merge($bindings, $bindings, $bindings) : [];
        $topCategoryRow = DB::selectOne($topCategorySql, $topCategoryBindings);
        $topCategory = $topCategoryRow ? (array) $topCategoryRow : null;

        // 5) All menus
        $menu = Menu::all(); // giữ nguyên Eloquent collection, có trường image

        // ===== Parse date filter =====
        $fromCarbon = $request->input('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : null;

        $toCarbon = $request->input('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : null;

        $from = $fromCarbon?->toDateTimeString();
        $to   = $toCarbon?->toDateTimeString();

        // ===== Tổng số món đang active =====
        $totalMenus = Menu::where('status', 1)->count();

        // ===== Subquery tổng hợp =====
        $salesSub = DB::query()
            ->fromSub($this->menuSalesQuery($from, $to), 's')
            ->select(
                'menu_id',
                DB::raw('SUM(qty) as total_qty'),
                DB::raw('SUM(revenue) as total_revenue')
            )
            ->groupBy('menu_id');

        // ===== Món bán chạy nhất =====
        $bestSelling = Menu::leftJoinSub($salesSub, 's', 's.menu_id', '=', 'menus.id')
            ->select(
                'menus.id',
                'menus.name',
                DB::raw('COALESCE(s.total_qty, 0) as total_sold')
            )
            ->orderByDesc('total_sold')
            ->first();

        // ===== Doanh thu theo món =====
        $revenuePerItem = Menu::leftJoinSub($salesSub, 's', 's.menu_id', '=', 'menus.id')
            ->select(
                'menus.id',
                'menus.name',
                DB::raw('COALESCE(s.total_revenue, 0) as revenue'),
                DB::raw('COALESCE(s.total_qty, 0) as total_qty')
            )
            ->orderByDesc('revenue')
            ->limit(50)
            ->get();

        // ===== Danh mục bán chạy nhất =====
        $topCategory = DB::table('menu_categories')
            ->join('menus', 'menus.category_id', '=', 'menu_categories.id')
            ->leftJoinSub($salesSub, 's', 's.menu_id', '=', 'menus.id')
            ->select(
                'menu_categories.id',
                'menu_categories.name',
                DB::raw('COALESCE(SUM(s.total_qty), 0) as total_qty')
            )
            ->groupBy('menu_categories.id', 'menu_categories.name')
            ->orderByDesc('total_qty')
            ->first();

        // ===== Return view =====
        return view('admin.menuStatistics.index', [
            'from'           => $fromCarbon,
            'to'             => $toCarbon,
            'totalMenus'     => $totalMenus,
            'bestSelling'    => $bestSelling,
            'revenuePerItem' => $revenuePerItem,
            'topCategory' => $topCategory,
            'filterType' => $request->input('filter', 'this_month'),
            'filterLabel' => null,
            'menu' => $menu,
            'topCategory'    => $topCategory,
            'filterType'     => $request->input('filter', 'this_month'),
            'filterLabel'    => null,
        ]);
    }
}
