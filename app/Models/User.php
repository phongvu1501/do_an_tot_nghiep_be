<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Các cột được phép gán hàng loạt.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
    ];

    /**
     * Các cột sẽ bị ẩn khi trả về JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Các kiểu dữ liệu được cast tự động.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Kiểm tra xem user có phải admin không.
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Kiểm tra xem user có phải user thường không.
     */
    public function isUser()
    {
        return $this->role === 'user';
    }
}
