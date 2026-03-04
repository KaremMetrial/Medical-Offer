<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('provider_id')->constrained('providers')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();

            // نسبة الخصم الافتراضية (اختياري)
            $table->unsignedTinyInteger('discount_percent')->default(0);

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->enum('status', ['draft', 'published', 'paused', 'expired'])->default('draft');

            $table->boolean('show_in_home')->default(false);
            $table->integer('sort_order')->default(0);

            $table->unsignedBigInteger('views')->default(0);

            $table->timestamps();

            $table->index(['provider_id', 'status']);
            $table->index(['category_id', 'status']);
            $table->index(['show_in_home', 'status']);
            $table->index(['start_date', 'end_date']);
        });

        Schema::create('offer_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained('offers')->cascadeOnDelete();
            $table->string('local', 10);

            $table->string('name');
            $table->text('description')->nullable();
            $table->text('terms')->nullable();

            $table->timestamps();

            $table->unique(['offer_id', 'local']);
            $table->index(['local']);
        });

        // صور/فيديوهات العرض
        Schema::create('offer_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained('offers')->cascadeOnDelete();
            $table->string('path');
            $table->enum('type', ['image', 'video'])->default('image');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['offer_id', 'type']);
        });

        // العرض متاح في أي فروع؟
        Schema::create('offer_branches', function (Blueprint $table) {
            $table->foreignId('offer_id')->constrained('offers')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('provider_branches')->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['offer_id', 'branch_id']);
        });

        // (اختياري) العرض له أكثر من تصنيف
        Schema::create('offer_categories', function (Blueprint $table) {
            $table->foreignId('offer_id')->constrained('offers')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['offer_id', 'category_id']);
        });

        // أهم جدول عندك: الخصم حسب الباقة
        Schema::create('offer_plan_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained('offers')->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained('member_plans')->cascadeOnDelete();
            $table->unsignedTinyInteger('discount_percent');

            $table->timestamps();

            $table->unique(['offer_id', 'plan_id']);
            $table->index(['plan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_plan_discounts');
        Schema::dropIfExists('offer_categories');
        Schema::dropIfExists('offer_branches');
        Schema::dropIfExists('offer_images');
        Schema::dropIfExists('offer_translations');
        Schema::dropIfExists('offers');
    }
};
