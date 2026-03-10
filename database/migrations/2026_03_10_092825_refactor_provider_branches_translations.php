<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('provider_branch_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_branch_id')
                ->constrained('provider_branches')
                ->cascadeOnDelete();
            $table->string('local', 10);
            $table->string('name');
            $table->timestamps();

            $table->unique(['provider_branch_id', 'local']);
            $table->index(['local']);
        });

        // Migrate existing data
        $branches = DB::table('provider_branches')->get();
        foreach ($branches as $branch) {
            if ($branch->name_ar) {
                DB::table('provider_branch_translations')->insert([
                    'provider_branch_id' => $branch->id,
                    'local' => 'ar',
                    'name' => $branch->name_ar,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            if ($branch->name_en) {
                DB::table('provider_branch_translations')->insert([
                    'provider_branch_id' => $branch->id,
                    'local' => 'en',
                    'name' => $branch->name_en,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        Schema::table('provider_branches', function (Blueprint $table) {
            $table->dropColumn(['name_ar', 'name_en']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('provider_branches', function (Blueprint $table) {
            $table->string('name_ar')->nullable()->after('city_id');
            $table->string('name_en')->nullable()->after('name_ar');
        });

        // Restore data from translations if possible
        $translations = DB::table('provider_branch_translations')->get();
        foreach ($translations as $translation) {
            DB::table('provider_branches')
                ->where('id', $translation->provider_branch_id)
                ->update([
                    "name_{$translation->local}" => $translation->name
                ]);
        }

        Schema::dropIfExists('provider_branch_translations');
    }
};
