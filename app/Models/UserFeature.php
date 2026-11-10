<?php

namespace App\Models;

use App\Enums\Feature;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['context_user_id', 'feature', 'granted'])]
class UserFeature extends Model
{
    /**
     * @return BelongsTo<ContextUser, $this>
     */
    public function membership(): BelongsTo
    {
        return $this->belongsTo(ContextUser::class, 'context_user_id');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'feature' => Feature::class,
            'granted' => 'boolean',
        ];
    }
}
