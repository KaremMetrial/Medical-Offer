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
        Schema::create('banner_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('banner_id')->constrained()->onDelete('cascade');
            $table->string('local', 10); // Language code (en, ar, etc.)
            $table->string('title'); // Banner title in specific language
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('banner_translations');
    }
};
