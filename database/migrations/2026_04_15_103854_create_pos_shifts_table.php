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
        Schema::create('pos_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();       // nhân viên mở ca
            $table->foreignId('location_id')->constrained();
            $table->decimal('opening_cash', 15, 0)->default(0); // tiền lẻ đầu ca
            $table->decimal('closing_cash', 15, 0)->nullable(); // tiền đếm lúc đóng
            $table->decimal('cash_difference', 15, 0)->nullable(); // chênh lệch
            $table->timestamp('opened_at')->useCurrent();
            $table->timestamp('closed_at')->nullable();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->text('closing_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_shifts');
    }
};
