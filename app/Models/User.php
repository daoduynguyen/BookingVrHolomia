<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ADMIN_PERMISSION_OPTIONS = [
        'dashboard' => 'Thống kê',
        'slots' => 'Lịch khung giờ',
        'bookings' => 'Duyệt đơn hàng',
        'tickets' => 'Quản lý vé',
        'locations' => 'Cơ sở',
        'coupons' => 'Kho Voucher',
        'users' => 'Người dùng',
        'settings' => 'Hệ thống',
        'contacts' => 'Tin nhắn',
        'chatbot' => 'Chatbot AI',
    ];

    public const DEFAULT_BRANCH_ADMIN_PERMISSIONS = [
        'dashboard',
        'slots',
        'bookings',
        'chatbot',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'location_id',
        'admin_permissions',
        'is_banned',
        'address',
        'birthday',
        'avatar',
        'gender',
        'language',
        'provider_name',
        'provider_id',
        'balance',
        'points',
        'tier',
        'notification_prefs',
        'ui_settings',
        'privacy_settings',
        'default_payment',
        'currency',
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
        'notification_prefs' => 'array',
        'ui_settings' => 'array',
        'privacy_settings' => 'array',
        'admin_permissions' => 'array',
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

    public static function adminPermissionOptions(): array
    {
        return self::ADMIN_PERMISSION_OPTIONS;
    }

    public static function defaultAdminPermissions(): array
    {
        return self::DEFAULT_BRANCH_ADMIN_PERMISSIONS;
    }

    public function resolvedAdminPermissions(): array
    {
        if (in_array($this->role, ['super_admin', 'admin'], true)) {
            return array_keys(self::ADMIN_PERMISSION_OPTIONS);
        }

        if (!in_array($this->role, ['branch_admin', 'staff'], true)) {
            return [];
        }

        if ($this->admin_permissions === null) {
            return self::DEFAULT_BRANCH_ADMIN_PERMISSIONS;
        }

        $permissions = is_array($this->admin_permissions) ? $this->admin_permissions : [];

        return array_values(array_intersect($permissions, array_keys(self::ADMIN_PERMISSION_OPTIONS)));
    }

    public function canAccessAdminSection(string $section): bool
    {
        return in_array($section, $this->resolvedAdminPermissions(), true);
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