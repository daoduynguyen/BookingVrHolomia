<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$orders = \App\Models\Order::orderBy('id', 'desc')->take(5)->get(['id', 'customer_name', 'created_at']);
foreach($orders as $o) {
    echo "Order ID: {$o->id} | Name: {$o->customer_name} | Created: {$o->created_at}\n";
}
