<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Category;
use App\Models\Location;
use App\Models\Ticket;
use App\Models\Coupon;
use App\Models\Setting;
use App\Models\TimeSlot;
use App\Models\Order;
use App\Models\OrderItem;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        if (Schema::hasTable('order_items')) OrderItem::truncate();
        if (Schema::hasTable('orders')) Order::truncate();
        if (Schema::hasTable('time_slots')) TimeSlot::truncate();
        if (Schema::hasTable('location_ticket')) DB::table('location_ticket')->truncate();
        if (Schema::hasTable('wishlists')) DB::table('wishlists')->truncate();
        if (Schema::hasTable('tickets')) Ticket::truncate();
        if (Schema::hasTable('locations')) Location::truncate();
        if (Schema::hasTable('categories')) Category::truncate();
        if (Schema::hasTable('coupons')) Coupon::truncate();
        if (Schema::hasTable('settings')) Setting::truncate();
        if (Schema::hasTable('users')) User::truncate();

        Schema::enableForeignKeyConstraints();

        // ===== USERS =====
        User::create([
            'name'     => 'Super Admin',
            'email'    => 'admin@holomia.com',
            'password' => bcrypt('Admin@123'),
            'role'     => 'super_admin',
        ]);
        User::create([
            'name'     => 'Khách',
            'email'    => 'khach@gmail.com',
            'password' => bcrypt('12345678'),
            'role'     => 'customer',
        ]);

        // ===== CATEGORIES =====
        $catAction    = Category::create(['name' => 'Hành động']);
        $catHorror    = Category::create(['name' => 'Kinh dị']);
        $catAdventure = Category::create(['name' => 'Phiêu lưu']);
        $catSport     = Category::create(['name' => 'Thể thao']);

        // ===== LOCATIONS =====
        $locRoyal = Location::create([
            'name'      => 'Holomia Royal City',
            'address'   => 'Royal City, Thanh Xuân, Hà Nội',
            'hotline'   => '024.6666.8888',
            'is_active' => true,
        ]);
        $locAeon = Location::create([
            'name'      => 'Holomia Aeon Mall',
            'address'   => 'Aeon Mall Hà Đông, Hà Nội',
            'hotline'   => '024.9999.7777',
            'is_active' => true,
        ]);

        // ===== TICKETS =====
        $tickets = [
            [
                'category_id'   => $catHorror->id,
                'location_id'   => $locRoyal->id,
                'name'          => 'Zombie VR: Đại dịch',
                'description'   => 'Trải nghiệm sinh tồn giữa bầy xác sống khát máu.',
                'image_url'     => 'https://images.unsplash.com/photo-1626379953822-baec19c3accd?w=600',
                'price'         => 120000,
                'price_weekend' => 150000,
                'duration'      => 15,
                'status'        => 'active',
                'avg_rating'    => 4.8,
                'play_count'    => 1250,
            ],
            [
                'category_id'   => $catAction->id,
                'location_id'   => $locAeon->id,
                'name'          => 'Beat Saber',
                'description'   => 'Chém khối nhạc theo giai điệu sôi động.',
                'image_url'     => 'https://images.unsplash.com/photo-1593508512255-86ab42a8e620?w=600',
                'price'         => 80000,
                'price_weekend' => 100000,
                'duration'      => 10,
                'status'        => 'maintenance',
                'avg_rating'    => 5.0,
                'play_count'    => 3400,
            ],
            [
                'category_id'   => $catAdventure->id,
                'location_id'   => $locRoyal->id,
                'name'          => 'Leo núi Everest VR',
                'description'   => 'Trải nghiệm cảm giác mạnh chưa từng có.',
                'image_url'     => 'https://images.unsplash.com/photo-1526772662000-3f88f10405ff?w=600',
                'price'         => 100000,
                'price_weekend' => 120000,
                'duration'      => 20,
                'status'        => 'active',
                'avg_rating'    => 4.5,
                'play_count'    => 800,
            ],
            [
                'category_id'   => $catAdventure->id,
                'location_id'   => $locAeon->id,
                'name'          => 'Phi thuyền không gian',
                'description'   => 'Khám phá vũ trụ bao la trong cabin phi thuyền VR.',
                'image_url'     => 'https://images.unsplash.com/photo-1516339901601-2e1b62dc0c45?w=600',
                'price'         => 100000,
                'price_weekend' => 120000,
                'duration'      => 20,
                'status'        => 'active',
                'avg_rating'    => 4.5,
                'play_count'    => 650,
            ],
            [
                'category_id'   => $catAction->id,
                'location_id'   => $locRoyal->id,
                'name'          => 'Đấu súng miền Tây',
                'description'   => 'Trở thành cao bồi trong thế giới miền Tây hoang dã.',
                'image_url'     => 'https://images.unsplash.com/photo-1552820728-8b83bb6b773f?w=600',
                'price'         => 100000,
                'price_weekend' => 120000,
                'duration'      => 20,
                'status'        => 'active',
                'avg_rating'    => 4.5,
                'play_count'    => 920,
            ],
            [
                'category_id'   => $catAdventure->id,
                'location_id'   => $locAeon->id,
                'name'          => 'Công viên khủng long',
                'description'   => 'Sống sót giữa bầy khủng long hung dữ.',
                'image_url'     => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?w=600',
                'price'         => 100000,
                'price_weekend' => 120000,
                'duration'      => 20,
                'status'        => 'active',
                'avg_rating'    => 4.5,
                'play_count'    => 710,
            ],
            [
                'category_id'   => $catHorror->id,
                'location_id'   => $locAeon->id,
                'name'          => 'Nhà Ma',
                'description'   => 'Khám phá ngôi nhà ma bí ẩn.',
                'image_url'     => 'https://images.unsplash.com/photo-1509248961158-e54f6934749c?w=600',
                'price'         => 90000,
                'price_weekend' => 110000,
                'duration'      => 15,
                'status'        => 'maintenance',
                'avg_rating'    => 4.2,
                'play_count'    => 300,
            ],
            [
                'category_id'   => $catAdventure->id,
                'location_id'   => $locRoyal->id,
                'name'          => 'Đua xe F1',
                'description'   => 'Cảm giác tốc độ đỉnh cao trên đường đua F1.',
                'image_url'     => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600',
                'price'         => 150000,
                'price_weekend' => 180000,
                'duration'      => 20,
                'status'        => 'maintenance',
                'avg_rating'    => 4.9,
                'play_count'    => 800,
            ],
            [
                'category_id'   => $catAction->id,
                'location_id'   => $locRoyal->id,
                'name'          => 'Half-Life: Alyx',
                'description'   => 'Cuộc chiến sinh tồn trong thế giới Half-Life.',
                'image_url'     => 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?w=600',
                'price'         => 130000,
                'price_weekend' => 160000,
                'duration'      => 30,
                'status'        => 'active',
                'avg_rating'    => 4.9,
                'play_count'    => 2100,
            ],
            [
                'category_id'   => $catAction->id,
                'location_id'   => $locAeon->id,
                'name'          => 'Superhot VR',
                'description'   => 'Thời gian chỉ di chuyển khi bạn di chuyển.',
                'image_url'     => 'https://images.unsplash.com/photo-1535905557558-afc4877a26fc?w=600',
                'price'         => 110000,
                'price_weekend' => 140000,
                'duration'      => 20,
                'status'        => 'active',
                'avg_rating'    => 4.7,
                'play_count'    => 1800,
            ],
            [
                'category_id'   => $catHorror->id,
                'location_id'   => $locRoyal->id,
                'name'          => 'Resident Evil 4 VR',
                'description'   => 'Huyền thoại kinh dị nay có mặt trong VR.',
                'image_url'     => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?w=600',
                'price'         => 140000,
                'price_weekend' => 170000,
                'duration'      => 45,
                'status'        => 'active',
                'avg_rating'    => 4.8,
                'play_count'    => 3200,
            ],
            [
                'category_id'   => $catSport->id,
                'location_id'   => $locAeon->id,
                'name'          => 'Eleven Table Tennis',
                'description'   => 'Bóng bàn VR chân thực nhất thế giới.',
                'image_url'     => 'https://images.unsplash.com/photo-1611251135345-18c56206b863?w=600',
                'price'         => 90000,
                'price_weekend' => 110000,
                'duration'      => 20,
                'status'        => 'active',
                'avg_rating'    => 4.6,
                'play_count'    => 1500,
            ],
            [
                'category_id'   => $catSport->id,
                'location_id'   => $locRoyal->id,
                'name'          => 'Walkabout Mini Golf',
                'description'   => 'Golf mini thư giãn trong không gian VR.',
                'image_url'     => 'https://images.unsplash.com/photo-1587574293340-e0011c4e8ecf?w=600',
                'price'         => 80000,
                'price_weekend' => 100000,
                'duration'      => 30,
                'status'        => 'active',
                'avg_rating'    => 4.4,
                'play_count'    => 900,
            ],
            [
                'category_id'   => $catAdventure->id,
                'location_id'   => $locAeon->id,
                'name'          => 'Skyrim VR',
                'description'   => 'Thế giới Skyrim huyền thoại trong tầm tay.',
                'image_url'     => 'https://images.unsplash.com/photo-1519074069444-1ba4fff66d16?w=600',
                'price'         => 120000,
                'price_weekend' => 150000,
                'duration'      => 60,
                'status'        => 'active',
                'avg_rating'    => 4.7,
                'play_count'    => 2800,
            ],
        ];

        foreach ($tickets as $data) {
            $locId  = $data['location_id'];
            $ticket = Ticket::create($data);
            $ticket->locations()->attach($locId);
        }

        // ===== COUPONS =====
        Coupon::create(['code' => 'BEDAO',     'type' => 'fixed',   'value' => 20000, 'quantity' => 100, 'expiry_date' => '2026-12-31']);
        Coupon::create(['code' => 'HOLOMIA10', 'type' => 'percent', 'value' => 10,    'quantity' => 50,  'expiry_date' => '2026-12-31']);
        Coupon::create(['code' => 'HETHAN',    'type' => 'fixed',   'value' => 50000, 'quantity' => 10,  'expiry_date' => '2023-01-01']);

        // ===== SETTINGS =====
        Setting::create(['key' => 'weekend_surcharge_percentage', 'value' => '30']);
    }
}