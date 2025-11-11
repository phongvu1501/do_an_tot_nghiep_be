<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $table = 'reviews';

    protected $fillable = [
        'user_id',
        'reservation_id',
        'rating',
        'comment'
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    // Người đánh giá
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Lần đặt bàn được đánh giá
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }
}