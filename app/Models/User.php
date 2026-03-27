<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'is_banned',
        'address',
        'birthday',
        'avatar',
        'gender',
        'language',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Định dạng dữ liệu
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // --- KHAI BÁO QUAN HỆ ---

    // 1 User có thể có nhiều Đơn hàng (Bookings)
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class)->orderBy('created_at', 'desc');
    }
    /**
     * Kiểm tra xem người dùng có phải là Admin không
     */
    public function isAdmin()
    {
        // Kiểm tra cột 'role' trong Database có giá trị là 'admin' hay không
        return $this->role === 'admin';
    }

    // User.php
    public function favorites()
    {
        // Quan hệ nhiều-nhiều với bảng Ticket thông qua bảng phụ 'wishlists'
        return $this->belongsToMany(Ticket::class, 'wishlists', 'user_id', 'ticket_id')->withTimestamps();
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    // Quan hệ: 1 User có thể viết nhiều đánh giá
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}