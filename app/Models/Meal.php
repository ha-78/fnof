<?php

namespace App\Models;

use App\Enums\MealStatus;
use Database\Factories\MealFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'name', 'recipe_link', 'comment', 'status'])]
class Meal extends Model
{
    /** @use HasFactory<MealFactory> */
    use HasFactory;

    protected $attributes = [
        'status' => 'wishlist',
    ];

    public function hasDetails(): bool
    {
        return filled($this->recipe_link) || filled($this->comment);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => MealStatus::class,
        ];
    }
}
