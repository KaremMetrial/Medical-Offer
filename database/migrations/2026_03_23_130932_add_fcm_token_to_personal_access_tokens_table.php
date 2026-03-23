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
        if (Schema::hasTable('personal_access_tokens') && !Schema::hasColumn('personal_access_tokens', 'fcm_token')) {
            Schema::table('personal_access_tokens', function (Blueprint $table) {
                $table->text('fcm_token')->nullable()->after('token');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('personal_access_tokens') && Schema::hasColumn('personal_access_tokens', 'fcm_token')) {
            Schema::table('personal_access_tokens', function (Blueprint $table) {
                $table->dropColumn('fcm_token');
            });
        }
    }
};
