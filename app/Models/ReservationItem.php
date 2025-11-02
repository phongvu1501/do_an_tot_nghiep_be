<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'menu_id',
        'quantity',
        'price',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

}
