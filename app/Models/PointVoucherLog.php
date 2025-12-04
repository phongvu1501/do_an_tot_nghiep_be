<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointVoucherLog extends Model
{
    use HasFactory;

    protected $table = 'point_voucher_logs';

    protected $fillable = [
        'user_id',
        'voucher_id',
        'points_spent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function voucher()
    {
        return $this->belongsTo(PointVoucherTier::class, 'voucher_id');
    }
}
