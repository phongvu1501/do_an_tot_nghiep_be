<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Menu;
use Carbon\Carbon;

class MenuStatisticsController extends Controller
{
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

        return view('admin.menuStatistics.index', [
            'from' => $fromCarbon,
            'to' => $toCarbon,
            'totalMenus' => $totalMenus,
            'bestSelling' => $bestSelling,
            'revenuePerItem' => $revenuePerItem,
            'topCategory' => $topCategory,
            'filterType' => $request->input('filter', 'this_month'),
            'filterLabel' => null,
            'menu' => $menu,
        ]);
    }
}
