<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$loc = \App\Models\Location::where('slug','royal-city')->first();
if (!$loc) {
    // Try to find any location
    $loc = \App\Models\Location::first();
}
echo "Location: {$loc->name}, slug: {$loc->slug}, total_devices: {$loc->total_devices}\n";

$slots = \App\Models\TimeSlot::where('location_id', $loc->id)
    ->whereDate('date', today())
    ->orderBy('start_time')
    ->get();

echo "Slots today (".today()->format('Y-m-d')."): {$slots->count()}\n\n";

foreach ($slots as $s) {
    echo "Slot#{$s->id} ticket_id={$s->ticket_id} [{$s->start_time}-{$s->end_time}] status={$s->status} booked={$s->booked_count}/{$s->capacity}\n";
}

echo "\n--- Availabilities ---\n";
$avails = \App\Models\TimeSlot::getTrueAvailabilitiesForDate($loc->id, today()->format('Y-m-d'));
foreach ($avails as $id => $a) {
    echo "Slot#{$id} => available={$a}\n";
}

// Also show PosDevices
$devices = \App\Models\PosDevice::where('location_id', $loc->id)->get();
echo "\n--- POS Devices ({$devices->count()}) ---\n";
$statusCounts = $devices->groupBy('status')->map->count();
foreach ($statusCounts as $status => $count) {
    echo "  {$status}: {$count}\n";
}
