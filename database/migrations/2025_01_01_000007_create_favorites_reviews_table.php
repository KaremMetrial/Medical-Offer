<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->foreignId('offer_id')->nullable()->constrained('offers')->cascadeOnDelete();
            $table->foreignId('provider_id')->nullable()->constrained('providers')->cascadeOnDelete();

            $table->timestamps();

            $table->index(['user_id']);
            $table->index(['offer_id']);
            $table->index(['provider_id']);

            // منع التكرار لنفس الهدف
            $table->unique(['user_id', 'offer_id']);
            $table->unique(['user_id', 'provider_id']);
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->foreignId('provider_id')->nullable()->constrained('providers')->cascadeOnDelete();
            $table->foreignId('offer_id')->nullable()->constrained('offers')->cascadeOnDelete();

            $table->unsignedTinyInteger('rating'); // 1..5
            $table->text('comment')->nullable();

            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->timestamps();

            $table->index(['provider_id', 'status']);
            $table->index(['offer_id', 'status']);
        });

        Schema::create('offer_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained('offers')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['offer_id', 'created_at']);
        });

        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('image_path');

            $table->enum('link_type', ['offer', 'provider', 'category', 'external'])->default('external');
            $table->unsignedBigInteger('link_id')->nullable();
            $table->string('external_url')->nullable();

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->string('position')->default('home_top');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->index(['position', 'is_active']);
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // polymorphic لو بتدفع اشتراك/غيره
            $table->string('payable_type')->nullable();
            $table->unsignedBigInteger('payable_id')->nullable();

            $table->foreignId('provider_id')->nullable()->constrained('providers')->nullOnDelete();

            $table->decimal('amount', 10, 2)->default(0);
            $table->string('method')->nullable(); // card, cash, etc
            $table->string('provider_ref')->nullable(); // ref from gateway
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');

            $table->timestamps();

            $table->index(['payable_type', 'payable_id']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('banners');
        Schema::dropIfExists('offer_views');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('favorites');
    }
};
