<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_code',
        'user_id',
        'reservation_date',
        'shift',
        'num_people',
        'depsection',
        'voucher_id',
        'status',
        'deposit',
        'total_amount',
        'payment_url',
        'cancellation_reason',
        'phone_confirmed',
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'phone_confirmed' => 'boolean',
    ];

    // Quan hệ với User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ với Voucher
    // public function voucher()
    // {
    //     return $this->belongsTo(Voucher::class);
    // }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'reservation_menu')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    public function tables()
    {
        return $this->belongsToMany(BanAn::class, 'reservation_tables', 'reservation_id', 'table_id');
    }

    public function reservationItems()
    {
        return $this->hasMany(ReservationItem::class);
    }

    // Quan hệ 1-1 với Review
    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    // Kiểm tra xem đã đánh giá chưa
    public function hasReviewed(): bool
    {
        return $this->review()->exists();
    }

    /**
     * Tính tiền cọc bàn (không bao gồm cọc đồ ăn)
     * @return float
     */
    public function getTableDeposit(): float
    {
        $tables = $this->tables;
        
        if ($tables->isEmpty()) {
            return 0;
        }

        // Kiểm tra có phòng VIP không
        $hasVipRoom = $tables->where('type', 'vip')->isNotEmpty();
        $hasNormalTables = $tables->where('type', 'normal')->isNotEmpty();

        // Phòng VIP: Luôn cọc theo cấu hình
        if ($hasVipRoom) {
            return max(1, (float)Setting::getValue('deposit_vip_rooms', 1000000));
        }

        // Bàn thường: Chỉ cọc nếu là ngày lễ
        if ($hasNormalTables) {
            $holidayDate = DepositRequiredDate::where('is_active', true)
                ->whereDate('date', $this->reservation_date)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($holidayDate) {
                $normalDeposit = $holidayDate->deposit_normal_tables ?? Setting::getValue('deposit_normal_tables', 500000);
                return max(1, (float)$normalDeposit);
            }
        }

        // Ngày thường + bàn thường: không cần cọc bàn
        return 0;
    }

    /**
     * Tính tiền cọc đồ ăn ban đầu (số tiền đã cọc khi đặt bàn)
     * @return float
     */
    public function getFoodDeposit(): float
    {
        // Cọc đồ ăn ban đầu = Tổng cọc đã trả - Cọc bàn
        // Vì deposit = table_deposit + food_deposit (khi tạo reservation)
        $totalDeposit = $this->deposit ?? 0;
        $tableDeposit = $this->getTableDeposit();
        $foodDeposit = $totalDeposit - $tableDeposit;
        
        // Đảm bảo không âm
        return max(0, $foodDeposit);
    }
}
