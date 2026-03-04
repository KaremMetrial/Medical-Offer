<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_provider', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('provider_id')->constrained('providers')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('provider_branches')->nullOnDelete();

            $table->timestamps();

            // Unique constraint to prevent duplicate relationships
            $table->unique(['user_id', 'provider_id', 'branch_id']);

            // Indexes for performance
            $table->index(['provider_id', 'branch_id']);
            $table->index(['user_id', 'provider_id']);
            $table->index(['branch_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_provider');
    }
};
