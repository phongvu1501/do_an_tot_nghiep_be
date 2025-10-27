<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $table = 'vouchers';

    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'max_uses',
        'min_order_value',
        'status',
        'start_date',
        'end_date'
    ];
}
