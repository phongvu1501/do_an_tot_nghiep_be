<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointLog extends Model
{
    use HasFactory;

    protected $table = 'point_logs';

    protected $fillable = [
        'user_id',
        'points',
        'action',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
