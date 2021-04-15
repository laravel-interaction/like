<?php

declare(strict_types=1);

namespace LaravelInteraction\Like\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use LaravelInteraction\Support\Interaction;
use function is_a;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection|\LaravelInteraction\Like\Like[] $likeableLikes
 * @property-read \Illuminate\Database\Eloquent\Collection|\LaravelInteraction\Like\Concerns\Fan[] $fans
 * @property-read string|int|null $fans_count
 *
 * @method static static|\Illuminate\Database\Eloquent\Builder whereLikedBy(\Illuminate\Database\Eloquent\Model $user)
 * @method static static|\Illuminate\Database\Eloquent\Builder whereNotLikedBy(\Illuminate\Database\Eloquent\Model $user)
 */
trait Likeable
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function fans(): BelongsToMany
    {
        return $this->morphToMany(
            config('like.models.user'),
            'likeable',
            config('like.models.like'),
            null,
            config('like.column_names.user_foreign_key')
        )->withTimestamps();
    }

    public function fansCount(): int
    {
        if ($this->fans_count !== null) {
            return (int) $this->fans_count;
        }

        $this->loadCount('fans');

        return (int) $this->fans_count;
    }

    public function fansCountForHumans($precision = 1, $mode = PHP_ROUND_HALF_UP, $divisors = null): string
    {
        return Interaction::numberForHumans(
            $this->fansCount(),
            $precision,
            $mode,
            $divisors ?? config('like.divisors')
        );
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $user
     *
     * @return bool
     */
    public function isLikedBy(Model $user): bool
    {
        if (! is_a($user, config('like.models.user'))) {
            return false;
        }
        $fansLoaded = $this->relationLoaded('fans');

        if ($fansLoaded) {
            return $this->fans->contains($user);
        }

        return ($this->relationLoaded('likeableLikes') ? $this->likeableLikes : $this->likeableLikes())
            ->where(config('like.column_names.user_foreign_key'), $user->getKey())
            ->count() > 0;
    }

    public function isNotLikedBy(Model $user): bool
    {
        return ! $this->isLikedBy($user);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function likeableLikes(): MorphMany
    {
        return $this->morphMany(config('like.models.like'), 'likeable');
    }

    public function scopeWhereLikedBy(Builder $query, Model $user): Builder
    {
        return $query->whereHas(
            'fans',
            function (Builder $query) use ($user) {
                return $query->whereKey($user->getKey());
            }
        );
    }

    public function scopeWhereNotLikedBy(Builder $query, Model $user): Builder
    {
        return $query->whereDoesntHave(
            'fans',
            function (Builder $query) use ($user) {
                return $query->whereKey($user->getKey());
            }
        );
    }
}
