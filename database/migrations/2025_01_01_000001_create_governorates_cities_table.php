<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('governorates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->restrictOnDelete();
            $table->boolean('is_active')->default(true);
            $table->string('local', 10)->nullable(); // optional label
            $table->timestamps();

            $table->index(['country_id', 'is_active']);
        });

        Schema::create('governorate_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('governorate_id')->constrained('governorates')->cascadeOnDelete();
            $table->string('local', 10);
            $table->string('name');
            $table->timestamps();

            $table->unique(['governorate_id', 'local']);
            $table->index(['local']);
        });

        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('governorate_id')->constrained('governorates')->restrictOnDelete();
            $table->boolean('is_active')->default(true);
            $table->string('local', 10)->nullable();
            $table->timestamps();

            $table->index(['governorate_id', 'is_active']);
        });

        Schema::create('city_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained('cities')->cascadeOnDelete();
            $table->string('local', 10);
            $table->string('name');
            $table->timestamps();

            $table->unique(['city_id', 'local']);
            $table->index(['local']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('city_translations');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('governorate_translations');
        Schema::dropIfExists('governorates');
    }
};
