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
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('companion_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('provider_id')->constrained('providers')->onDelete('cascade');
            $table->timestamp('visit_date');
            $table->json('services')->nullable();
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->text('comment')->nullable();
            $table->enum('status', ['completed', 'cancelled'])->default('completed');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
