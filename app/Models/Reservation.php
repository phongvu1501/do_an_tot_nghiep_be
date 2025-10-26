<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'user_id',
        'reservation_date',
        'shift',
        'num_people',
        'depsection',
        'voucher_id',
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
}
