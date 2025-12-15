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
            'topCategory'    => $topCategory,
            'filterType'     => $request->input('filter', 'this_month'),
            'filterLabel'    => null,
        ]);
    }
}
