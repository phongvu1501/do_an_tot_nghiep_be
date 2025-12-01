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

    // vouchers
    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'voucher_id', 'id');
    }

    public function calculateVoucherDiscount(): float
    {
        if (!$this->voucher_id) return 0;

        $voucher = Voucher::find($this->voucher_id);
        if (!$voucher || $voucher->status !== 'active') return 0;

        $subtotal = $this->reservationItems->sum(fn($item) => $item->price * $item->quantity);
        $vat = $subtotal * 0.1;
        $total = $subtotal + $vat;

        // Kiểm tra điều kiện min/max
        if ($voucher->min_order_value && $total < $voucher->min_order_value) return 0;
        if ($voucher->order_value_allowed && $total > $voucher->order_value_allowed) return 0;

        // Tính giảm
        $discount = $voucher->discount_type === 'percent'
            ? ($total * $voucher->discount_value) / 100
            : $voucher->discount_value;

        if ($voucher->max_discount_value) {
            $discount = min($discount, $voucher->max_discount_value);
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
}
