<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\PosDevice;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CleanupOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup expired no-shows and complete finished orders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();
        $this->info("Starting cleanup at {$now->toDateTimeString()}");

        // 1. Mark finished playing orders as completed
        $playingOrders = Order::where('status', 'paid')
            ->whereNotNull('checkin_at')
            ->whereHas('slot')
            ->with(['slot'])
            ->get();

        $completedCount = 0;
        foreach ($playingOrders as $order) {
            $endTimeStr = $order->slot->date . ' ' . $order->slot->end_time;
            $endTime = Carbon::parse($endTimeStr);
            
            if ($now->greaterThan($endTime)) {
                DB::transaction(function() use ($order) {
                    $order->update(['status' => 'completed']);
                    if ($order->device_id) {
                        PosDevice::where('id', $order->device_id)->update(['status' => 'available']);
                    }
                });
                $completedCount++;
            }
        }
        $this->info("Marked {$completedCount} orders as completed and freed devices.");

        // 2. Expire no-shows
        $waitingOrders = Order::whereIn('status', ['paid', 'pending'])
            ->whereNull('checkin_at')
            ->whereHas('slot')
            ->with('slot')
            ->get();
            
        $expiredCount = 0;
        foreach ($waitingOrders as $order) {
            $deadlineStr = $order->slot->date . ' ' . $order->slot->start_time;
            $deadline = Carbon::parse($deadlineStr)->addMinutes(15);
            
            if ($now->greaterThan($deadline)) {
                DB::transaction(function() use ($order) {
                    $order->update(['status' => 'expired']);
                    if ($order->slot_id) {
                        $order->slot->decrementBooked($order->quantity ?? 1);
                    }
                });
                $expiredCount++;
            }
        }
        $this->info("Marked {$expiredCount} orders as expired (no-show) and freed slots.");
    }
}
