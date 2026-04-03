<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
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
        // 1. TẮT KIỂM TRA KHÓA NGOẠI (Để Truncate được các bảng có liên kết)
        Schema::disableForeignKeyConstraints();

        // 2. Làm sạch dữ liệu cũ (CHỈ truncate nếu bảng tồn tại)
        if (Schema::hasTable('users')) User::truncate();
        if (Schema::hasTable('categories')) Category::truncate();
        if (Schema::hasTable('locations')) Location::truncate();
        if (Schema::hasTable('tickets')) Ticket::truncate();
        if (Schema::hasTable('settings')) Setting::truncate();
        if (Schema::hasTable('time_slots')) TimeSlot::truncate();
        if (Schema::hasTable('orders')) Order::truncate();
        if (Schema::hasTable('order_items')) OrderItem::truncate();
        if (Schema::hasTable('coupons')) Coupon::truncate();

        // 3. BẬT LẠI KIỂM TRA KHÓA NGOẠI
        Schema::enableForeignKeyConstraints();

        // --- BẮT ĐẦU TẠO DỮ LIỆU MỚI ---

        // ==========================================
        // PHẦN 1: NGƯỜI DÙNG & DANH MỤC & ĐỊA ĐIỂM
        // ==========================================
        User::create(['name' => 'Super Admin', 'email' => 'admin@holomia.com', 'password' => bcrypt('Admin@123'), 'role' => 'super_admin']);
        User::create(['name' => 'Khách', 'email' => 'khach@gmail.com', 'password' => bcrypt('12345678'), 'role' => 'customer']);

        $catAction = Category::create(['name' => 'Hành động']);
        $catHorror = Category::create(['name' => 'Kinh dị']);
        $catAdventure = Category::create(['name' => 'Phiêu lưu']);
        $catSport = Category::create(['name' => 'Thể thao']);

        $locRoyal = Location::create(['name' => 'Holomia Royal City', 'address' => 'Royal City', 'hotline' => '024.6666.8888', 'is_active' => true]);
        $locAeon = Location::create(['name' => 'Holomia Aeon Mall', 'address' => 'Aeon Mall', 'hotline' => '024.9999.7777', 'is_active' => true]);

        // ==========================================
        // PHẦN 2: 6 GAME HOT (HIỆN Ở TRANG CHỦ)
        // ==========================================

        // 1. Zombie VR (Bình thường)
        $zombie = Ticket::create([
            'category_id' => $catHorror->id,
            'name' => 'Zombie VR: Đại dịch',
            'description' => 'Trải nghiệm sinh tồn giữa bầy xác sống khát máu.',
            'rules' => '- Không được tự ý tháo kính VR khi chưa kết thúc.\n- Giơ tay trái lên để nạp đạn.\n- Không di chuyển quá phạm vi vòng tròn an toàn.',
            'image_url' => 'https://images.unsplash.com/photo-1626379953822-baec19c3accd?w=600',
            'price' => 120000,
            'price_weekend' => 150000,
            'duration' => 15,
            'status' => 'active', 'avg_rating' => 4.8, 'play_count' => 1250
        ]);
        $zombie->locations()->attach($locRoyal->id);

        // 2. Beat Saber (BẢO TRÌ)
        $beatSaber = Ticket::create([
            'category_id' => $catAction->id,
            'name' => 'Beat Saber (Đang sửa)',
            'description' => 'Chém khối nhạc theo giai điệu sôi động.',
            'rules' => '- Chém đúng hướng mũi tên trên khối nhạc.\n- Tránh chém vào bom.',
            'image_url' => 'https://images.unsplash.com/photo-1593508512255-86ab42a8e620?w=600',
            'price' => 80000,
            'price_weekend' => 100000,
            'duration' => 10,
            'status' => 'maintenance',
            'avg_rating' => 5.0, 'play_count' => 3400
        ]);
        $beatSaber->locations()->attach($locAeon->id);

        // 3-6. Các game Hot khác
        $extrasHot = [
            ['Leo núi Everest VR', $catAdventure->id, 'https://images.unsplash.com/photo-1526772662000-3f88f10405ff?w=600'],
            ['Phi thuyền không gian', $catAdventure->id, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSVaAHD5YZdUeTPAfeWsS3_OA_skIBY9apoTg&s'],
            ['Đấu súng miền Tây', $catAction->id, 'https://images.unsplash.com/photo-1552820728-8b83bb6b773f?w=600'],
            ['Công viên khủng long', $catAdventure->id, 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?w=600']
        ];

        foreach ($extrasHot as $ex) {
            $t = Ticket::create([
                'category_id' => $ex[1],
                'name' => $ex[0],
                'description' => 'Trải nghiệm cảm giác mạnh chưa từng có với công nghệ thực tế ảo mới nhất.',
                'rules' => '- Tuân thủ hướng dẫn của nhân viên.\n- Ngồi đúng tư thế ghế rung.',
                'image_url' => $ex[2],
                'price' => 100000,
                'price_weekend' => 120000,
                'duration' => 20,
                'status' => 'active', 'avg_rating' => 4.5, 'play_count' => rand(500, 1000)
            ]);
            $t->locations()->attach($locRoyal->id);
        }

        // ==========================================
        // PHẦN 3: CÁC GAME BẢO TRÌ KHÁC
        // ==========================================

        $nhaMa = Ticket::create([
            'category_id' => $catHorror->id,
            'name' => 'Nhà Ma (Bảo trì)',
            'description' => 'Sửa chữa nâng cấp hệ thống rung.',
            'rules' => 'Đang cập nhật...',
            'image_url' => 'https://images.unsplash.com/photo-1509248961158-e54f6934749c?w=600',
            'price' => 90000,
            'price_weekend' => 110000,
            'duration' => 15,
            'status' => 'maintenance',
            'avg_rating' => 4.2, 'play_count' => 300
        ]);
        $nhaMa->locations()->attach($locAeon->id);

        $f1 = Ticket::create([
            'category_id' => $catAdventure->id,
            'name' => 'Đua xe F1 (Bảo trì)',
            'description' => 'Bảo dưỡng vô lăng.',
            'rules' => 'Đang cập nhật...',
            'image_url' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600',
            'price' => 150000,
            'price_weekend' => 180000,
            'duration' => 20,
            'status' => 'maintenance',
            'avg_rating' => 4.9, 'play_count' => 800
        ]);
        $f1->locations()->attach($locRoyal->id);

        // ==========================================
        // PHẦN 4: DỮ LIỆU LẤP ĐẦY (FILLER DATA)
        // ==========================================

        $fillerGames = [
            ['Half-Life: Alyx', $catAction->id, 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?w=600'],
            ['Superhot VR', $catAction->id, 'https://images.unsplash.com/photo-1535905557558-afc4877a26fc?w=600'],
            ['The Climb 2', $catAdventure->id, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRmrkEGWVo9E8sWzKKmK5A4XVCX_R0BtLHG5w&s'],
            ['Moss: Book II', $catAdventure->id, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRgSdmXXGlYDnlOeuXWdTMSAzOCs2nFFmUcTQ&s'],
            ['Resident Evil 4 VR', $catHorror->id, 'https://images.unsplash.com/photo-1542751371-adc38448a05e?w=600'],
            ['Vader Immortal', $catAction->id, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRiJE1h5totJLT3tooBMmEeBAvHauzfNV9Z9A&s'],
            ['Job Simulator', $catAdventure->id, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS3-yV3eMtnYscQGJDUj_LYh9kaBqEdVpL0ww&s'],
            ['Pistol Whip', $catAction->id, 'https://images.unsplash.com/photo-1595769816263-9b910be24d5f?w=600'],
            ['Walking Dead: S&S', $catHorror->id, 'https://images.unsplash.com/photo-1505635552518-3448ff116af3?w=600'],
            ['Eleven Table Tennis', $catSport->id, 'https://images.unsplash.com/photo-1611251135345-18c56206b863?w=600'],
            ['Walkabout Mini Golf', $catSport->id, 'https://images.unsplash.com/photo-1587574293340-e0011c4e8ecf?w=600'],
            ['Skyrim VR', $catAdventure->id, 'https://images.unsplash.com/photo-1519074069444-1ba4fff66d16?w=600'],
        ];

        $locations = [$locRoyal->id, $locAeon->id];

        foreach ($fillerGames as $game) {
            $t = Ticket::create([
                'category_id' => $game[1],
                'name' => $game[0],
                'description' => 'Trò chơi thực tế ảo hấp dẫn hàng đầu thế giới đã có mặt tại Holomia.',
                'rules' => '- Đeo dây đeo tay cầm để tránh rơi vỡ.\n- Giữ khoảng cách với người xung quanh.',
                'image_url' => $game[2],
                'price' => rand(80, 150) * 1000,
                'price_weekend' => rand(120, 200) * 1000,
                'duration' => [15, 20, 30, 45, 60][rand(0, 4)],
                'status' => 'active',
                'avg_rating' => rand(40, 50) / 10,
                'play_count' => rand(100, 5000)
            ]);
            $t->locations()->attach($locations[rand(0, 1)]);
        }

        // ==========================================
        // PHẦN 5: TẠO MÃ GIẢM GIÁ (VOUCHER)
        // ==========================================

        Coupon::create([
            'code' => 'BEDAO',
            'type' => 'fixed',
            'value' => 20000,
            'quantity' => 100,
            'expiry_date' => '2026-12-31',
        ]);

        Coupon::create([
            'code' => 'HOLOMIA10',
            'type' => 'percent',
            'value' => 10,
            'quantity' => 50,
            'expiry_date' => '2026-12-31',
        ]);

        Coupon::create([
            'code' => 'HETHAN',
            'type' => 'fixed',
            'value' => 50000,
            'quantity' => 10,
            'expiry_date' => '2023-01-01',
        ]);

        // ==========================================
        // PHẦN 6: THIẾT LẬP CHUNG (SETTINGS)
        // ==========================================
        Setting::create([
            'key' => 'weekend_surcharge_percentage',
            'value' => '30'
        ]);
    }
}