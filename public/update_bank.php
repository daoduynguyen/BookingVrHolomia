<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Setting;

Setting::updateOrCreate(['key' => 'bank_name'], ['value' => 'BIDV']);
Setting::updateOrCreate(['key' => 'bank_bin'], ['value' => '970418']);
Setting::updateOrCreate(['key' => 'bank_account'], ['value' => '8860075445']);
Setting::updateOrCreate(['key' => 'bank_owner'], ['value' => 'NGUYEN']);

echo "Settings updated successfully.\n";
