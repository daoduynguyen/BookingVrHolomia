<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('locations', function (Blueprint $table) {
            if (!Schema::hasColumn('locations', 'description'))
                $table->text('description')->nullable()->after('hotline');
            if (!Schema::hasColumn('locations', 'logo_url'))
                $table->string('logo_url')->nullable()->after('description');
            if (!Schema::hasColumn('locations', 'banner_url'))
                $table->string('banner_url')->nullable()->after('logo_url');
            if (!Schema::hasColumn('locations', 'color'))
                $table->string('color')->default('#2563eb')->after('banner_url');
        });
    }
    public function down(): void {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn(['description','logo_url','banner_url','color']);
        });
    }
};