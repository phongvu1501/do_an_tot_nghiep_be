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
        'status'
    ];
}
