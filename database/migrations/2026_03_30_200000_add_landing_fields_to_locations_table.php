<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 1 – Landing page theo cơ sở
 *
 * Thêm các cột mới vào bảng locations:
 *   - slug           : URL-friendly name (vd: "ha-noi", "ho-chi-minh")
 *   - description    : Giới thiệu ngắn hiển thị trên landing page
 *   - banner_image   : URL ảnh banner header
 *   - opening_hours  : Giờ mở cửa (chuỗi tự do, vd: "09:00 – 22:00 mỗi ngày")
 *   - maps_url       : Link Google Maps nhúng iframe
 *   - facebook_url   : Fanpage Facebook của chi nhánh (tuỳ chọn)
 *   - is_active      : Bật/tắt hiển thị landing page
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable()->after('name');
            $table->text('description')->nullable()->after('hotline');
            $table->string('banner_image')->nullable()->after('description');
            $table->string('opening_hours')->nullable()->after('banner_image');
            $table->string('maps_url')->nullable()->after('opening_hours');
            $table->string('facebook_url')->nullable()->after('maps_url');
            $table->boolean('is_active')->default(true)->after('facebook_url');
        });
    }

    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn([
                'slug', 'description', 'banner_image',
                'opening_hours', 'maps_url', 'facebook_url', 'is_active',
            ]);
        });
    }
};
