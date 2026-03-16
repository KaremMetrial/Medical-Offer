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
        Schema::create('nationalities', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('nationality_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nationality_id')->constrained('nationalities')->cascadeOnDelete();
            $table->string('name');
            $table->string('local', 10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nationalities');
        Schema::dropIfExists('nationality_translations');
    }
};
