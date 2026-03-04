<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('providers', function (Blueprint $table) {
            $table->id();

            $table->string('logo')->nullable();
            $table->string('cover')->nullable();

            $table->string('phone')->nullable();

            $table->unsignedTinyInteger('experince_years')->nullable();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();

            $table->enum('status', ['pending', 'active', 'suspended'])->default('pending');
            $table->boolean('is_varified')->default(false);

            $table->unsignedBigInteger('views')->default(0);

            $table->timestamps();

            $table->index(['status', 'is_varified']);
            $table->index(['country_id']);
        });

        Schema::create('provider_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnDelete();
            $table->string('local', 10);

            $table->string('name');
            $table->string('title')->nullable();
            $table->text('description')->nullable();

            $table->timestamps();

            $table->unique(['provider_id', 'local']);
            $table->index(['local']);
        });

        Schema::create('provider_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['provider_id', 'category_id']);
            $table->index(['category_id']);
        });

        Schema::create('provider_branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnDelete();

            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->foreignId('governorate_id')->nullable()->constrained('governorates')->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();

            $table->string('name_ar')->nullable();
            $table->string('name_en')->nullable();

            $table->string('address');
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();

            $table->string('phone')->nullable();

            $table->json('working_hours_json')->nullable();

            $table->boolean('is_main')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['provider_id', 'is_active']);
            $table->index(['city_id']);
            $table->index(['governorate_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_branches');
        Schema::dropIfExists('provider_categories');
        Schema::dropIfExists('provider_translations');
        Schema::dropIfExists('providers');
    }
};
