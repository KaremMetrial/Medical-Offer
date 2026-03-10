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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('country_id')->nullable()->after('role')->constrained('countries')->nullOnDelete();
            $table->foreignId('governorate_id')->nullable()->after('country_id')->constrained('governorates')->nullOnDelete();
            $table->foreignId('city_id')->nullable()->after('governorate_id')->constrained('cities')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['city_id']);
            $table->dropColumn('city_id');
            $table->dropForeign(['governorate_id']);
            $table->dropColumn('governorate_id');
            $table->dropForeign(['country_id']);
            $table->dropColumn('country_id');
        });
    }
};
