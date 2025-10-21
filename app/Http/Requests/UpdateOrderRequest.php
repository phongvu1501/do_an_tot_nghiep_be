<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Xác định xem người dùng có quyền thực hiện request này hay không.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Định nghĩa các quy tắc validation áp dụng cho request.
     */
    public function rules(): array
    {
        return [
            // 'sometimes' chỉ kiểm tra trường này nếu nó có mặt trong request
            'reservation_id' => ['sometimes', 'integer', 'exists:reservations,id'],
            'payment_status' => ['sometimes', 'string', 'in:pending,paid,cancelled'],
            
            // Khi cập nhật, nếu có gửi 'items', thì phải là array và có ít nhất 1 phần tử
            'items' => ['sometimes', 'array', 'min:1'], 
            'items.*.menu_id' => ['required', 'integer', 'exists:menus,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}