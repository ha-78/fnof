<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Meals belong to a household like threads, lists and bookmarks.
     * Existing meals join their author's household, or the first one.
     */
    public function up(): void
    {
        Schema::table('meals', function (Blueprint $table) {
            $table->foreignId('context_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        if (DB::table('meals')->exists()) {
            $fallback = DB::table('contexts')->orderBy('id')->value('id')
                ?? DB::table('contexts')->insertGetId([
                    'name' => 'Familjen',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            DB::table('meals')->whereNull('context_id')->orderBy('id')->chunkById(200, function ($meals) use ($fallback): void {
                foreach ($meals as $meal) {
                    $contextId = DB::table('context_user')
                        ->where('user_id', $meal->user_id)
                        ->orderBy('id')
                        ->value('context_id');

                    DB::table('meals')
                        ->where('id', $meal->id)
                        ->update(['context_id' => $contextId ?? $fallback]);
                }
            });
        }

        Schema::table('meals', function (Blueprint $table) {
            $table->foreignId('context_id')->nullable(false)->change();
            $table->index(['context_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meals', function (Blueprint $table) {
            $table->dropIndex(['context_id', 'status']);
            $table->dropConstrainedForeignId('context_id');
        });
    }
};
