<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BanAn extends Model
{
    use HasFactory;

    protected $table = 'tables';

    protected $fillable = [
        'table_number',
        'capacity',
        'status',
        'available_date',
        'available_from',
        'available_until',
    ];

    public function reservations()
    {
        return $this->belongsToMany(Reservation::class, 'reservation_tables', 'table_id', 'reservation_id');
    }

}
