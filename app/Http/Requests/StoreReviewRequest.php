<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Quyền kiểm tra ở Policy
    }

    public function rules(): array
    {
        return [
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'rating.required' => 'Vui lòng chọn số sao từ 1 đến 5.',
            'rating.min'      => 'Số sao phải ít nhất là 1.',
            'rating.max'      => 'Số sao không được quá 5.',
            'comment.max'     => 'Bình luận không được quá 1000 ký tự.',
        ];
    }
}