<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanhMucMonAn extends Model
{
    use HasFactory;

    protected $table = 'menu_categories';

      protected $fillable = [
        'name',
        'description',
        'status'
    ];
}
