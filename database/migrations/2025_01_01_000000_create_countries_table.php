<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('phone_code', 10)->nullable();
            $table->string('currency_symbol', 10)->nullable();
            $table->string('currency_name', 50)->nullable();
            $table->string('currency_unit', 50)->nullable();
            $table->decimal('currency_factor', 10, 4)->default(1);
            $table->string('flag')->nullable();
            $table->string('timezone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('country_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
            $table->string('local', 10); // ar,en,...
            $table->string('name');
            $table->timestamps();

            $table->unique(['country_id', 'local']);
            $table->index(['local']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('country_translations');
        Schema::dropIfExists('countries');
    }
};
