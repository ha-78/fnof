<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('context_user')->where('role', 'user')->update(['role' => 'member']);
        DB::table('invites')->where('role', 'user')->update(['role' => 'member']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('context_user')->where('role', 'member')->update(['role' => 'user']);
        DB::table('invites')->where('role', 'member')->update(['role' => 'user']);
    }
};
