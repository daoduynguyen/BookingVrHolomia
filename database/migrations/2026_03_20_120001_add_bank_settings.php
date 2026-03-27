<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        $settings = [
            ['key' => 'bank_name',       'value' => 'Vietcombank'],
            ['key' => 'bank_account',    'value' => '1234567890'],
            ['key' => 'bank_owner',      'value' => 'NGUYEN VAN A'],
            ['key' => 'bank_bin',        'value' => '970436'], // BIN Vietcombank
            ['key' => 'sepay_api_token', 'value' => ''],
        ];
        foreach ($settings as $s) {
            DB::table('settings')->updateOrInsert(['key' => $s['key']], ['value' => $s['value'], 'updated_at' => now(), 'created_at' => now()]);
        }
    }
    public function down(): void {}
};