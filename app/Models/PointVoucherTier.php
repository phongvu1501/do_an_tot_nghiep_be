<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointVoucherTier extends Model
{
    use HasFactory;

    protected $table = 'point_voucher_tiers';

    protected $fillable = [
        'points_required',
        'discount_percent',
        'max_discount_value',
        'min_order_value',
        'name',
        'is_active',
    ];
}
