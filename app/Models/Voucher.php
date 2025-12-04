<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $table = 'vouchers';

    protected $fillable = [
        'tier_id',
        'code',
        'discount_type',
        'discount_value',
        'max_uses',
        'min_order_value',
        'status',
        'start_date',
        'end_date',
        'order_value_allowed'
    ];

    public function tier()
    {
        return $this->belongsTo(PointVoucherTier::class, 'tier_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_voucher')
            ->withPivot(['status', 'used_at'])
            ->withTimestamps();
    }
}
