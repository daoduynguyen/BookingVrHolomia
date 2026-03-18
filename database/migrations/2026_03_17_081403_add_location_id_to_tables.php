<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Thêm vào bảng users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'location_id')) {
                $table->unsignedBigInteger('location_id')->nullable()->after('role');
            }
        });

        // Thêm vào bảng tickets
        Schema::table('tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('tickets', 'location_id')) {
                $table->unsignedBigInteger('location_id')->nullable()->after('id');
            }
        });

        // Thêm vào bảng orders
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'location_id')) {
                $table->unsignedBigInteger('location_id')->nullable()->after('user_id');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('location_id');
        });
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('location_id');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('location_id');
        });
    }
};