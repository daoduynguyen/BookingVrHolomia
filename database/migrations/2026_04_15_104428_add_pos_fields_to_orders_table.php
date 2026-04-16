<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('pos_shift_id')->nullable()->constrained('pos_shifts')->nullOnDelete()->after('location_id');
            $table->foreignId('device_id')->nullable()->constrained('pos_devices')->nullOnDelete()->after('pos_shift_id');
            $table->boolean('is_pos_sale')->default(false)->after('device_id');
            $table->timestamp('checkin_at')->nullable()->after('is_pos_sale');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['pos_shift_id']);
            $table->dropForeign(['device_id']);
            $table->dropColumn(['pos_shift_id', 'device_id', 'is_pos_sale', 'checkin_at']);
        });
    }
};