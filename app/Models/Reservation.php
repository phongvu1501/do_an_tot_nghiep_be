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
        'voucher_discount',
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
    return $this->belongsToMany(Menu::class, 'reservation_menu', 'reservation_id', 'menu_id')
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

    // vouchers
    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'voucher_id', 'id');
    }

    public function calculateVoucherDiscount(): float
    {
        if (!$this->voucher_id) return 0;

        $voucher = Voucher::with('tier')->find($this->voucher_id);
        if (!$voucher || $voucher->status !== 'active') return 0;

        $subtotal = $this->reservationItems->sum(fn($item) => $item->price * $item->quantity);
        $vat = $subtotal * 0.1;
        $total = $subtotal + $vat;

        // Kiểm tra điều kiện giá trị đơn hàng tối thiểu
        if ($voucher->min_order_value && $total < $voucher->min_order_value) return 0;

        // Tính giảm
        $discount = $voucher->discount_type === 'percent'
            ? ($total * $voucher->discount_value) / 100
            : $voucher->discount_value;

        // Áp dụng giới hạn giá trị giảm tối đa từ order_value_allowed (chỉ cho giảm theo %)
        if ($voucher->discount_type === 'percent' && $voucher->order_value_allowed && $discount > $voucher->order_value_allowed) {
            $discount = $voucher->order_value_allowed;
        }

        // Kiểm tra max_discount_value từ tier nếu có
        if ($voucher->tier && $voucher->tier->max_discount_value) {
            if ($discount > $voucher->tier->max_discount_value) {
                $discount = $voucher->tier->max_discount_value;
            }
        }

        return $discount;
    }
    
    public function calculateTotalAmount(): float
    {
        $subtotal = $this->reservationItems->sum(fn($item) => $item->price * $item->quantity);
        $vat = $subtotal * 0.1;
        $voucherDiscount = $this->calculateVoucherDiscount();

        return $subtotal + $vat - $voucherDiscount;
    }

    public function show($id)
{
    $user = \App\Models\User::with(['reservations.menus', 'reservations.tables'])->findOrFail($id);
    return view('admin.user.show', compact('user'));
}

}
