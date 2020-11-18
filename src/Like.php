<?php

declare(strict_types=1);

namespace Zing\LaravelLike;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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
    public $incrementing = true;

    public function getTable()
    {
        return config('like.table_names.likes') ?: parent::getTable();
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
