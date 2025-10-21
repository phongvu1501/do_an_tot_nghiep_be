<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Xác định xem người dùng có quyền thực hiện request này hay không.
     */
    public function authorize(): bool
    {
        // Thay đổi thành true nếu bạn đã triển khai hệ thống xác thực (ví dụ: Laravel Sanctum)
        return true; 
    }

    /**
     * Định nghĩa các quy tắc validation áp dụng cho request.
     */
    public function rules(): array
    {
        return [
            // Validation cho bảng 'orders'
            'reservation_id' => ['required', 'integer', 'exists:reservations,id'],
            'payment_status' => ['nullable', 'string', 'in:pending,paid,cancelled'],
            
            // Validation cho bảng 'order_items' (Items)
            /*
                ký hiệu * để có thể báo lỗi chính xác ở bản ghi thứ bao nhiêu
                ex: items.1.quantity là báo lỗi trường quantity bản ghi thứ 2
            */ 
            'items' => ['required', 'array', 'min:1'], // Phải có ít nhất 1 món ăn
            'items.*.menu_id' => ['required', 'integer', 'exists:menus,id'], // ID món ăn phải tồn tại
            'items.*.quantity' => ['required', 'integer', 'min:1'], // Số lượng phải >= 1
            'items.*.price' => ['nullable', 'numeric', 'min:0'], // Giá tại thời điểm đặt (có thể null nếu lấy giá từ Menu)
        ];
    }
}