<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // باقات العضوية
        Schema::create('member_plans', function (Blueprint $table) {
            $table->id();
            $table->decimal('price', 10, 2)->default(0);
            $table->unsignedInteger('duration_days')->default(30);
            $table->json('features_json')->nullable();
            $table->boolean('is_active')->default(true);

            // لو تبي تستخدم نفس الجدول لباقات مزودين/مراكز (اختياري)
            $table->boolean('is_provider')->default(false);

            $table->timestamps();
            $table->index(['is_active', 'is_provider']);
        });

        Schema::create('plan_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('member_plans')->cascadeOnDelete();
            $table->string('local', 10);
            $table->string('name');
            $table->string('label')->nullable();
            $table->timestamps();

            $table->unique(['plan_id', 'local']);
            $table->index(['local']);
        });

        // اشتراك المستخدم (العضوية)
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained('member_plans')->restrictOnDelete();

            $table->dateTime('start_at');
            $table->dateTime('end_at');

            $table->enum('status', ['active', 'expired', 'canceled', 'pending'])->default('pending');
            $table->enum('payment_status', ['paid', 'unpaid', 'refunded'])->default('unpaid');

            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['end_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('plan_translations');
        Schema::dropIfExists('member_plans');
    }
};
