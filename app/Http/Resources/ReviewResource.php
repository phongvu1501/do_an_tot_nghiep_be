<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ReviewResource
 *
 * Định dạng dữ liệu đánh giá trả về cho API
 * Dùng để chuyển model Review thành JSON chuẩn, dễ đọc
 */
class ReviewResource extends JsonResource
{
    /**
     * Chuyển đổi dữ liệu thành mảng
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            // ID đánh giá
            'id' => $this->id,

            // Số sao (1-5)
            'rating' => $this->rating,

            // Nội dung bình luận
            'comment' => $this->comment,

            // Thời gian tạo (định dạng Việt Nam)
            'created_at' => $this->created_at->format('d/m/Y H:i'),

            // Thông tin người đánh giá
            'user' => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
            ],
        ];
    }
}