<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepositRequiredDate extends Model
{
    protected $fillable = [
        'date',
        'description',
        'is_active',
        'deposit_per_table',
        'deposit_normal_tables',
        'deposit_vip_rooms',
    ];

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean',
        'deposit_per_table' => 'decimal:2',
    ];
}
