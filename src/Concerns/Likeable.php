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
    public function fans(): BelongsToMany
    {
        return $this->morphToMany(
            config('like.models.user'),
            'likeable',
            config('like.models.pivot'),
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

    /**
     * @phpstan-param 1|2|3|4 $mode
     *
     * @param array<int, string>|null $divisors
     */
    public function fansCountForHumans(int $precision = 1, int $mode = PHP_ROUND_HALF_UP, $divisors = null): string
    {
        return Interaction::numberForHumans(
            $this->fansCount(),
            $precision,
            $mode,
            $divisors ?? config('like.divisors')
        );
    }

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

    public function likeableLikes(): MorphMany
    {
        return $this->morphMany(config('like.models.pivot'), 'likeable');
    }

    public function scopeWhereLikedBy(Builder $query, Model $user): Builder
    {
        return $query->whereHas('fans', static fn (Builder $query): Builder => $query->whereKey($user->getKey()));
    }

    public function scopeWhereNotLikedBy(Builder $query, Model $user): Builder
    {
        return $query->whereDoesntHave(
            'fans',
            static fn (Builder $query): Builder => $query->whereKey($user->getKey())
        );
    }
}
