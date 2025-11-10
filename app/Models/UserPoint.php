<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPoint extends Model
{
    use HasFactory;

    protected $table = 'point_histories';

    protected $fillable = [
        'user_id',
        'type',
        'points',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
