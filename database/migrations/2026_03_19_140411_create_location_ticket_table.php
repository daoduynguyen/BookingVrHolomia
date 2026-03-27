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
        Schema::create('location_ticket', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // Seed existing data
        $tickets = \Illuminate\Support\Facades\DB::table('tickets')->whereNotNull('location_id')->get();
        foreach ($tickets as $ticket) {
            \Illuminate\Support\Facades\DB::table('location_ticket')->insert([
                'location_id' => $ticket->location_id,
                'ticket_id' => $ticket->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_ticket');
    }
};
