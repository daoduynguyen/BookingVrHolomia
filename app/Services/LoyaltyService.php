<?php
namespace App\Services;

use App\Models\User;
use App\Models\Order;

class LoyaltyService
{
    // Tỷ lệ: 10,000 VNĐ = 1 điểm
    const POINTS_PER_VND = 10000;

    // Ngưỡng hạng
    const TIERS = [
        'Thành viên' => 0,
        'Bạc' => 100,
        'Vàng' => 300,
        'Kim Cương' => 700,
        'VIP' => 1500,
    ];

    const TIER_DISCOUNTS = [
        'Thành viên' => 0,
        'Bạc' => 3,
        'Vàng' => 5,
        'Kim Cương' => 8,
        'VIP' => 10,
    ];

    public static function addPoints(User $user, Order $order): void
    {
        $pointsEarned = (int) floor($order->total_amount / self::POINTS_PER_VND);
        $user->points += $pointsEarned;
        $user->tier = self::calculateTier($user->points);
        $user->save();
    }

    public static function calculateTier(int $points): string
    {
        $tier = 'Thành viên';
        foreach (self::TIERS as $name => $threshold) {
            if ($points >= $threshold)
                $tier = $name;
        }
        return $tier;
    }

    public static function getTierDiscount(User $user): float
    {
        return (self::TIER_DISCOUNTS[$user->tier] ?? 0) / 100;
    }
}
