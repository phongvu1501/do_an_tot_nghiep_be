<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'menus';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'category_id',
        'description',
        'price',
        'image',
        'status',
    ];

    // Quan hệ: Mỗi món ăn thuộc 1 danh mục
    public function category()
    {
        return $this->belongsTo(MenuCategory::class, 'category_id');
    }

    // Biến đổi hiển thị trạng thái (giúp dễ show trong view)
    public function getStatusTextAttribute()
    {
        return $this->status ? 'Hiển thị' : 'Ẩn';
    }

    public function reservations()
    {
        return $this->belongsToMany(Reservation::class, 'reservation_menu')->withPivot('quantity');
    }

    public function orders()
    {
        return $this->belongstoMany(Order::class, 'order_items')
                    ->withPivot('quantity', 'price')
                    ->withtimestamps();
    }
}
