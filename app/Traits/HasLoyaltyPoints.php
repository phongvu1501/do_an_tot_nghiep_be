<?php

namespace App\Traits;

use App\Models\User;

use App\Models\UserPoint;

trait HasLoyaltyPoints
{
    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pointHistories()
    {
        return $this->hasMany(UserPoint::class, 'user_id');
    }

    /**
     * Tính toán số điểm hiện tại của người dùng.
     * Điểm = Tổng điểm EARN - Tổng điểm USE
     *
     * @return int
     */
    public function getCurrentPoints(): int
    {
        // Tính tổng điểm EARN (loại 'earn')
        $earned = $this->pointHistories()
            ->where('type', 'earn')
            ->sum('points');

        // Tính tổng điểm USE (loại 'use')
        $used = $this->pointHistories()
            ->where('type', 'use')
            ->sum('points');

        return $earned - $used;
    }

    /**
     * Ghi lại lịch sử tích điểm (EARN).
     * Đây là hàm được gọi khi người dùng hoàn thành đơn hàng, v.v.
     *
     * @param int $points Số điểm tích lũy (phải lớn hơn 0)
     * @param string $description Mô tả nguồn tích điểm
     * @return UserPoint|\Illuminate\Database\Eloquent\Model|null
     */
    public function earnPoints(int $points, string $description): ?UserPoint
    {
        if ($points <= 0) {
            return null;
        }

        return $this->pointHistories()->create([
            'type' => 'earn',
            'points' => $points,
            'description' => $description,
        ]);
    }

    /**
     * Ghi lại lịch sử sử dụng điểm (USE).
     * Đây là hàm được gọi khi người dùng đổi điểm lấy voucher.
     *
     * @param int $points Số điểm đã sử dụng/đổi (phải lớn hơn 0)
     * @param string $description Mô tả việc sử dụng điểm (thường là voucher đã đổi)
     * @return UserPoint|\Illuminate\Database\Eloquent\Model|null
     */
    public function usePoints(int $points, string $description): ?UserPoint
    {
        if ($points <= 0) {
            return null;
        }

        return $this->pointHistories()->create([
            'type' => 'use',
            'points' => $points,
            'description' => $description,
        ]);
    }
}
