<?php

use App\Enums\MealStatus;
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
        Schema::create('meals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('recipe_link', 2048)->nullable();
            $table->text('comment')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status', 25)->default(MealStatus::Wishlist->value)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meals');
    }
};
