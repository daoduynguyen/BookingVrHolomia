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
    Schema::table('users', function (Blueprint $table) {
        $table->json('notification_prefs')->nullable()->after('language');
        $table->json('ui_settings')->nullable()->after('notification_prefs');
        $table->json('privacy_settings')->nullable()->after('ui_settings');
        $table->string('default_payment')->default('wallet')->after('privacy_settings');
        $table->string('currency')->default('VND')->after('default_payment');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['notification_prefs','ui_settings','privacy_settings','default_payment','currency']);
    });
}
};
