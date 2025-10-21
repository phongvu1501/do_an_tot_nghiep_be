<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Menu;
use App\Http\Requests\StoreOrderRequest; // Import Form Request cho Create
use App\Http\Requests\UpdateOrderRequest; // Import Form Request cho Update
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    //Lấy danh sách tất cả các đơn hàng. 
    public function index(): JsonResponse
    {
        // Lấy tất cả Order cùng menus liên quan
        $orders = Order::with('menus')->latest()->get();

        return response()->json($orders);
    }

    //tạo đơn hàng mới
    public function store(StoreOrderRequest $request): JsonResponse
    {
        // Khởi tạo biến
        $totalPrice = 0;
        $menuItems = [];
        
        // Lặp qua danh sách món ăn để tính Total Price và chuẩn bị data cho sync()
        foreach ($request->items as $item) {
            $menuItem = Menu::find($item['menu_id']);
            
            // Ưu tiên sử dụng giá từ request (giảm giá), nếu không thì lấy giá mặc định từ Menu.
            $itemPrice = $item['price'] ?? $menuItem->price;
            $totalPrice += $itemPrice * $item['quantity'];

            /*
             Cấu trúc mảng: $menuItems
             [
                [0 => 'quantity1', 'price1'],
                [1 => 'quantity2', 'price2'],
             ]
            */            
            $menuItems[$item['menu_id']] = [
                'quantity' => $item['quantity'],
                'price' => $itemPrice, 
            ];
        }

        // 3. Bắt đầu giao dịch (Transaction)
        DB::beginTransaction();
        try {
            //Tạo bản ghi Order chính
            $order = Order::create([
                'reservation_id' => $request->reservation_id,
                'total_price' => $totalPrice,
                'payment_status' => $request->payment_status ?? 'pending',
            ]);

            // 3.2. Gắn các món ăn vào Order (lưu vào bảng order_items)
            $order->menus()->sync($menuItems);

            DB::commit(); // Xác nhận giao dịch.

            // 4. Tải lại mối quan hệ để response JSON có đầy đủ chi tiết món ăn.
            $order->load('menus'); 

            return response()->json($order, 201);
        } catch (\Exception $e) {
            DB::rollBack(); // Nếu có lỗi, hoàn tác tất cả thay đổi trong giao dịch
            return response()->json(['message' => 'Lỗi khi tạo đơn hàng.', 'error' => $e->getMessage()], 500);
        }
    }

    //Hiển thị chi tiết một đơn hàng cụ thể.
    public function show(Order $order): JsonResponse
    {
        $order->load('menus');
        return response()->json($order);
    }


    //Cập nhật một đơn hàng
    public function update(UpdateOrderRequest $request, Order $order): JsonResponse
    {
        DB::beginTransaction();
        try {
            // 1. Cập nhật các trường chính của Order
            $order->update($request->only(['reservation_id', 'payment_status']));
            
            // 2. Cập nhật Order Items (Chỉ khi trường 'items' có mặt trong request)
            if ($request->has('items')) {
                $totalPrice = 0;
                $menuItems = [];
                
                foreach ($request->items as $item) {
                    $menuItem = Menu::find($item['menu_id']);
                    $itemPrice = $item['price'] ?? $menuItem->price;

                    $totalPrice += $itemPrice * $item['quantity'];

                    $menuItems[$item['menu_id']] = [
                        'quantity' => $item['quantity'],
                        'price' => $itemPrice,
                    ];
                }
                
                // Dùng sync() để thay thế toàn bộ danh sách OrderItems cũ bằng danh sách mới
                $order->menus()->sync($menuItems);

                // Cập nhật lại total_price của Order
                $order->update(['total_price' => $totalPrice]);
            }

            DB::commit();

            $order->load('menus'); // Tải lại data mới nhất trước khi trả về
            return response()->json($order);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Lỗi khi cập nhật đơn hàng.', 'error' => $e->getMessage()], 500);
        }
    }

    //Xóa một đơn hàng
    public function destroy(Order $order): JsonResponse
    {
        DB::beginTransaction();
        try {
            // Xóa tất cả các liên kết trong bảng trung gian (order_items)
            $order->menus()->detach();
            
            // 2. Xóa bản ghi Order
            $order->delete();

            DB::commit();

            return response()->json(null, 204);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Lỗi khi xóa đơn hàng.', 'error' => $e->getMessage()], 500);
        }
    }
}