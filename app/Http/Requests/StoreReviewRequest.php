<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $reservation = $this->route('reservation');
        return $reservation &&
               $reservation->user_id === $this->user()->id &&
               $reservation->status === 'completed' &&
               !$reservation->hasReviewed();

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'rating.required' => 'Vui lòng chọn số sao từ 1 đến 5.',
            'rating.min' => 'Số sao phải ít nhất là 1.',
            'rating.max' => 'Số sao không được quá 5.',
            'comment.max' => 'Bình luận không được quá 1000 ký tự.',
        ];
    }
}
