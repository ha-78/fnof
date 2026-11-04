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
        Schema::create('invites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('context_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inviter_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('email')->index();
            $table->string('role');
            $table->string('token_hash', 64)->unique();
            $table->text('token');
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index(['context_id', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invites');
    }
};
