<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Menu;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    //Tạo đơn hàng mới (Order) và thêm các món ăn.
    public function store(Request $request)
{
    $user = Auth::user();

    if (!$user) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    // Kiểm tra có phải admin không
    if ($user->role !== 'admin') {
        return response()->json([
            'error' => 'Forbidden',
            'message' => 'Bạn không có quyền truy cập chức năng này.'
        ], 403);
    }

    // Validated dữ liệu
    $validator = Validator::make($request->all(), [
        'reservation_id' => 'required|exists:reservations,id',
        'menus' => 'required|array|min:1',
        'menus.*.menu_id' => 'required|exists:menus,id',
        'menus.*.quantity' => 'required|integer|min:1',
    ], [
        'reservation_id.required' => 'Mã đặt bàn là bắt buộc.',
        'reservation_id.exists' => 'Mã đặt bàn không tồn tại.',
        'menus.required' => 'Danh sách món ăn không được để trống.',
        'menus.*.menu_id.exists' => 'Món ăn không tồn tại.',
        'menus.*.quantity.min' => 'Số lượng món ăn phải tối thiểu là 1.',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 422);
    }

    try {
        DB::beginTransaction();

        // Admin có thể tìm đơn đặt bàn bằng ID
        $reservation = Reservation::where('id', $request->reservation_id)
            ->with('reservationItems')
            ->first();

        if (!$reservation) {
            return response()->json([
                'error' => 'Không tìm thấy đặt bàn',
                'message' => 'Đơn đặt bàn không tồn tại.',
            ], 404);
        }

        // Kiểm tra trạng thái status của reservation, nếu hoàn thành hay bỏ rồi thì báo message luôn
        if (in_array($reservation->status, ['completed', 'cancelled'])) {
            return response()->json([
                'error' => 'Không thể thêm món',
                'message' => 'Đơn đặt bàn đã ' . $reservation->status . ' không thể thêm hoặc xóa món.'
            ], 400);
        }

        // Chuẩn bị dữ liệu cho việc tạo bản ghi order mới, và gộp món ăn trong reservation_items
        $itemsToAttach = [];       
        $totalOrderAmount = 0;

        // Lấy tất cả các bản ghi ReservationItem hiện tại
        $currentReservationItems = $reservation->reservationItems;

        // Lặp qua Request menus để chuẩn bị dữ liệu Pivot và cập nhật Reservation Items
        foreach ($request->menus as $menuItem) {
            $menuId = $menuItem['menu_id'];
            $quantity = (int)$menuItem['quantity'];

            $menu = Menu::find($menuId);
            
            // Kiểm tra xem cái món đó có tồn tại hay không, hoặc món đó đang phục vụ hay không
            if (!$menu) {
                throw new \Exception("Món ăn ID: {$menuId} không tồn tại.");
            }

            $itemPrice = $menu->price;
            $itemTotal = $itemPrice * $quantity;
            $totalOrderAmount += $itemTotal; // Tổng tiền của order mới

            $itemsToAttach[$menuId] = [
                'quantity' => $quantity,
                'price' => $itemPrice,
                'created_at' => now(), 
                'updated_at' => now(),
            ];

            // 2. Cập nhật món mới hoặc cộng tổng số lượng vào Reservation Items
            $currentItem = $currentReservationItems->firstWhere('menu_id', $menuId);

            if ($currentItem) {
                // Món đã tồn tại: Cập nhật số lượng và giá
                $currentItem->quantity += $quantity;
                $currentItem->price = $itemPrice; 
                $currentItem->save();
            } else {
                // Món mới: Thêm bản ghi mới vào Reservation Items
                $reservation->reservationItems()->create([
                    'menu_id' => $menu->id,
                    'quantity' => $quantity,
                    'price' => $itemPrice,
                ]);
            }
        } 

        // Cập nhật lại tổng tiền của reservation  
        $reservation->load('reservationItems');

        $reservation->total_amount = $reservation->reservationItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });
        $reservation->save();

        // Tạo bản ghi order mới
        $order = Order::create([
            'reservation_id' => $reservation->id,
            'total_price' => $totalOrderAmount,
            'payment_status' => 'pending',
        ]);

        // Lưu các món gọi thêm vào Order Items
        $order->menus()->attach($itemsToAttach);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm món vào đơn hàng thành công! Tổng tiền đơn đặt bàn đã được cập nhật',
            'order' => $order->load('menus'),
            'new_items_total' => $totalOrderAmount,
            'reservation_new_total' => $reservation->total_amount,
        ], 201);
        
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'error' => 'Tạo đơn hàng thất bại',
            'message' => 'Đã có lỗi xảy ra trong quá trình tạo đơn hàng. Vui lòng thử lại.',
            'details' => $e->getMessage(),
        ], 500);
    }
}
    


    
    public function destroy(Request $request, $reservationId)
    {
        //Xác thực user + kiểm tra xem phải admin hay không
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        //  Kiểm tra vai trò 
        if ($user->role !== 'admin') {
            // Trả về lỗi 403 Forbidden - không có quyền
            return response()->json([
                'error' => 'Forbidden',
                'message' => 'Bạn không có quyền truy cập chức năng này.'
            ], 403);
        }


        $validator = Validator::make($request->all(), [
            'items_to_remove' => 'required|array|min:1',
            'items_to_remove.*.menu_id' => 'required|exists:menus,id',
            'items_to_remove.*.quantity' => 'required|integer|min:1',
        ], [
            'items_to_remove.required' => 'Danh sách món ăn cần xóa là bắt buộc.',
            'items_to_remove.*.menu_id.exists' => 'Mã món ăn không tồn tại.',
            'items_to_remove.*.quantity.min' => 'Số lượng cần xóa phải tối thiểu 1.',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $reservation = Reservation::where('id', $reservationId)
                ->with('reservationItems')
                ->first();

            if (!$reservation) {
                return response()->json([
                    'error' => 'Không tìm thấy đơn đặt bàn',
                    'message' => 'Đơn đặt bàn không tồn tại.',
                ], 404);
            }

            // Kiểm tra trạng thái reservation
            if (in_array($reservation->status, ['cancelled', 'completed'])) {
                return response()->json([
                    'error' => 'Không thể chỉnh sửa đơn',
                    'message' => 'Đơn đặt bàn này đã bị hủy hoặc đã hoàn thành. Không thể xóa hoặc chỉnh sửa món ăn.',
                ], 400);
            }
            // -----------------------------------------------------------

            foreach ($request->items_to_remove as $itemToRemoveRequest) {
                $menuId = $itemToRemoveRequest['menu_id'];
                $quantityToRemove = $itemToRemoveRequest['quantity'];

                // Tìm Reservation Item cần xóa
                $itemToUpdate = $reservation->reservationItems->firstWhere('menu_id', $menuId);

                // Kiểm tra món ăn phải tồn tại trong đơn
                if (!$itemToUpdate) {
                    throw new \Exception("Món ăn ID: {$menuId} không tồn tại trong đơn đặt bàn.");
                }

                if ($itemToUpdate->quantity <= $quantityToRemove) {
                    // Xóa toàn bộ bản ghi nếu số lượng muốn xóa >= số lượng hiện có
                    $itemToUpdate->delete();
                } else {
                    // Chỉ giảm số lượng
                    $itemToUpdate->quantity -= $quantityToRemove;
                    $itemToUpdate->save();
                }
            }

            // Cập nhật lại tổng tiền reservation
            // Cần tải lại item sau khi xóa/cập nhật
            $reservation->load('reservationItems');

            $newTotalAmount = $reservation->reservationItems->sum(function ($item) {
                return $item->price * $item->quantity;
            });

            $reservation->total_amount = $newTotalAmount;
            $reservation->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa món ăn khỏi đơn đặt bàn thành công.',
                'reservation_id' => $reservation->id,
                'new_total_amount' => $newTotalAmount,
                // sử dụng map, đỡ phải tạo mảng con chứa mỗi menu_id với quantity
                'remaining_items' => $reservation->reservationItems->map(fn($i) => ['menu_id' => $i->menu_id, 'quantity' => $i->quantity]),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Lỗi xóa món',
                'message' => 'Đã có lỗi xảy ra trong quá trình xóa món ăn.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
