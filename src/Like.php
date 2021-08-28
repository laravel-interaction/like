<?php

declare(strict_types=1);

namespace LaravelInteraction\Like;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;
use LaravelInteraction\Like\Events\Liked;
use LaravelInteraction\Like\Events\Unliked;

/**
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Database\Eloquent\Model $user
 * @property \Illuminate\Database\Eloquent\Model $fan
 * @property \Illuminate\Database\Eloquent\Model $likeable
 *
 * @method static \LaravelInteraction\Like\Like|\Illuminate\Database\Eloquent\Builder withType(string $type)
 * @method static \LaravelInteraction\Like\Like|\Illuminate\Database\Eloquent\Builder query()
 */
class Like extends MorphPivot
{
    protected $dispatchesEvents = [
        'created' => Liked::class,
        'deleted' => Unliked::class,
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(
            function (self $like): void {
                if ($like->uuids()) {
                    $like->{$like->getKeyName()} = Str::orderedUuid();
                }
            }
        );
    }

    public function fan(): BelongsTo
    {
        return $this->user();
    }

    public $incrementing = true;

    public function getIncrementing(): bool
    {
        if ($this->uuids()) {
            return false;
        }

        return parent::getIncrementing();
    }

    public function getKeyName(): string
    {
        return $this->uuids() ? 'uuid' : parent::getKeyName();
    }

    public function getKeyType(): string
    {
        return $this->uuids() ? 'string' : parent::getKeyType();
    }

    public function getTable()
    {
        return config('like.table_names.likes') ?: parent::getTable();
    }

    public function isLikedBy(Model $user): bool
    {
        return $user->is($this->fan);
    }

    public function isLikedTo(Model $object): bool
    {
        return $object->is($this->likeable);
    }

    public function likeable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeWithType(Builder $query, string $type): Builder
    {
        return $query->where('likeable_type', app($type)->getMorphClass());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('like.models.user'), config('like.column_names.user_foreign_key'));
    }

    protected function uuids(): bool
    {
        return (bool) config('like.uuids');
    }
}
