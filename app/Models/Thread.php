<?php

namespace App\Models;

use Database\Factories\ThreadFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable(['user_id', 'body', 'title', 'archived_at'])]
class Thread extends Model
{
    /** @use HasFactory<ThreadFactory> */
    use HasFactory, SoftDeletes;

    public function displayTitle(): string
    {
        $title = Str::of((string) $this->title)->trim()->toString();

        if ($title !== '') {
            return $title;
        }

        return Str::of($this->body)->squish()->limit(80)->toString();
    }

    public function isArchived(): bool
    {
        return $this->archived_at !== null;
    }

    /**
     * List threads by newest activity: the thread's creation time, or its latest reply.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('recentActivity', function (Builder $builder): void {
            $reply = new Reply;
            $createdAt = $builder->qualifyColumn('created_at');
            $latestReplyAt = Reply::query()
                ->select($reply->qualifyColumn('created_at'))
                ->whereColumn($reply->qualifyColumn('thread_id'), $builder->qualifyColumn('id'))
                ->latest($reply->qualifyColumn('created_at'))
                ->limit(1);

            $builder
                ->orderByRaw(
                    'greatest('.$createdAt.', coalesce(('.$latestReplyAt->toSql().'), '.$createdAt.')) desc',
                    $latestReplyAt->getBindings(),
                )
                ->orderByDesc($builder->qualifyColumn('id'));
        });
    }

    /**
     * @param  Builder<Thread>  $query
     * @return Builder<Thread>
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereNull('archived_at');
    }

    /**
     * @param  Builder<Thread>  $query
     * @return Builder<Thread>
     */
    public function scopeArchived(Builder $query): Builder
    {
        return $query->whereNotNull('archived_at');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<Reply, $this>
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Reply::class)->orderBy('id');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'archived_at' => 'datetime',
        ];
    }
}
