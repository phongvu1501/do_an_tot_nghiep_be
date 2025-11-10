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
    ];

    protected $casts = [
        'reservation_date' => 'date',
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

}
