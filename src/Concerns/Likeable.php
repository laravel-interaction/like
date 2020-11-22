<?php

declare(strict_types=1);

namespace Zing\LaravelLike\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use function is_a;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection|\Zing\LaravelLike\Like[] $likes
 * @property-read \Illuminate\Database\Eloquent\Collection|\Zing\LaravelLike\Concerns\Fan[] $fans
 * @property-read int|null $fans_count
 *
 * @method static static|\Illuminate\Database\Eloquent\Builder whereLikedBy(\Illuminate\Database\Eloquent\Model $user)
 * @method static static|\Illuminate\Database\Eloquent\Builder whereNotLikedBy(\Illuminate\Database\Eloquent\Model $user)
 */
trait Likeable
{
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

        if ($this->relationLoaded('fans')) {
            return $this->fans->contains($user);
        }

        return tap($this->relationLoaded('likes') ? $this->likes : $this->likes())
            ->where(config('like.column_names.user_foreign_key'), $user->getKey())->count() > 0;
    }

    public function isNotLikedBy(Model $user): bool
    {
        return ! $this->isLikedBy($user);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function likes(): MorphMany
    {
        return $this->morphMany(config('like.models.like'), 'likeable');
    }

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
        $number = $this->fansCount();
        $divisors = collect($divisors ?? config('like.divisors'));
        $divisor = $divisors->keys()->filter(
            function ($divisor) use ($number) {
                return $divisor <= abs($number);
            }
        )->last(null, 1);

        if ($divisor === 1) {
            return (string) $number;
        }

        return number_format(round($number / $divisor, $precision, $mode), $precision) . $divisors->get($divisor);
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
