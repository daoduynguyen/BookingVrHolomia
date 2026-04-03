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
    Schema::create('time_slots', function (Blueprint $table) {
        $table->id();
        $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
        $table->unsignedBigInteger('location_id')->nullable();
        $table->date('date');
        $table->time('start_time');
        $table->time('end_time');
        $table->integer('capacity')->default(10);
        $table->integer('booked_count')->default(0);
        $table->enum('status', ['open', 'full', 'closed'])->default('open');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('time_slots');
}
};
