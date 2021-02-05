<?php

declare(strict_types=1);

namespace Zing\LaravelLike;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;
use Zing\LaravelLike\Events\Liked;
use Zing\LaravelLike\Events\Unliked;

/**
 * @property \Illuminate\Database\Eloquent\Model $user
 * @property \Illuminate\Database\Eloquent\Model $fan
 * @property \Illuminate\Database\Eloquent\Model $likeable
 *
 * @method static \Zing\LaravelLike\Like|\Illuminate\Database\Eloquent\Builder withType(string $type)
 * @method static \Zing\LaravelLike\Like|\Illuminate\Database\Eloquent\Builder query()
 */
class Like extends MorphPivot
{
    protected function uuids(): bool
    {
        return (bool) config('like.uuids');
    }

    public function getIncrementing(): bool
    {
        return $this->uuids() ? true : parent::getIncrementing();
    }

    public function getKeyName(): string
    {
        return $this->uuids() ? 'uuid' : parent::getKeyName();
    }

    public function getKeyType(): string
    {
        return $this->uuids() ? 'string' : parent::getKeyType();
    }

    protected $dispatchesEvents = [
        'created' => Liked::class,
        'deleted' => Unliked::class,
    ];

    public function getTable()
    {
        return config('like.table_names.likes') ?: parent::getTable();
    }

    protected static function booted(): void
    {
        parent::booted();

        static::creating(
            function (self $like): void {
                if ($like->uuids()) {
                    $like->{$like->getKeyName()} = Str::orderedUuid();
                }
            }
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function likeable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('like.models.user'), config('like.column_names.user_foreign_key'));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fan(): BelongsTo
    {
        return $this->user();
    }

    public function isLikedBy(Model $user): bool
    {
        return $user->is($this->fan);
    }

    public function isLikedTo(Model $object): bool
    {
        return $object->is($this->likeable);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithType(Builder $query, string $type): Builder
    {
        return $query->where('likeable_type', app($type)->getMorphClass());
    }
}
