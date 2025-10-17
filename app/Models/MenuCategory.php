<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuCategory extends Model
{
    use HasFactory;

    protected $table = 'menu_categories'; // tên bảng trong database

    protected $fillable = [
        'name',
        'description',
    ];

    // Một danh mục có thể chứa nhiều món ăn
    public function menus()
    {
        return $this->hasMany(Menu::class, 'category_id');
    }
}
