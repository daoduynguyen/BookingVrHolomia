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
        Schema::table('pos_devices', function (Blueprint $table) {
            $table->foreignId('location_id')->after('id')->constrained()->cascadeOnDelete();
            $table->string('name')->after('location_id');
            $table->string('status')->default('available')->after('name');
            $table->integer('battery_percent')->nullable()->after('status');
            $table->string('mac_address')->nullable()->after('battery_percent');
            $table->text('error_note')->nullable()->after('mac_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_devices', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropColumn(['location_id', 'name', 'status', 'battery_percent', 'mac_address', 'error_note']);
        });
    }
};
