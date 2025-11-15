<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'table_id',
        'total_price',
        'payment_status'
    ];

    // mói quan hệ với bảng reservations
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

     // Mối quan hệ với bảng menus
    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'order_items')
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
    }
}