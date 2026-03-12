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
        Schema::table('story_views', function (Blueprint $table) {
            $table->string('ip_device')->nullable()->unique()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('story_views', function (Blueprint $table) {
            $table->dropColumn('ip_device');
        });
    }
};
