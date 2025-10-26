<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BanAn extends Model
{
    use HasFactory;

    protected $table = 'tables';

    protected $fillable = [
        'name',
        'limit_number',
        'status',
    ];

    public function reservations()
    {
        return $this->belongsToMany(Reservation::class, 'reservation_tables', 'table_id', 'reservation_id');
    }

}
