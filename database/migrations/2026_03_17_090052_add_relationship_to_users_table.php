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
            if (!Schema::hasColumn('users', 'relationship')) {
                $table->string('relationship')->nullable()->after('parent_user_id');
            }
            if (!Schema::hasColumn('users', 'companion_status')) {
                $table->enum('companion_status', ['pending', 'approved', 'rejected'])->default('pending')->after('relationship');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'relationship')) {
                $table->dropColumn('relationship');
            }
            if (Schema::hasColumn('users', 'companion_status')) {
                $table->dropColumn('companion_status');
            }
        });
    }
};
