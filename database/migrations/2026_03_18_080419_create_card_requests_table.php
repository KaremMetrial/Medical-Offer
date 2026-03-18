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
        Schema::create('card_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('governorate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            $table->string('receiver_name')->nullable();
            $table->string('receiver_phone', 20)->nullable();
            $table->text('address');
            $table->decimal('issuance_fee', 10, 6)->default(0);
            $table->decimal('delivery_fee', 10, 6)->default(0);
            $table->decimal('total_amount', 10, 6)->default(0);
            $table->string('status', 30)->default('pending'); // pending, processing, prepared, shipped, delivered, cancelled
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('card_requests');
    }
};
