<?php

declare(strict_types=1);

namespace Zing\LaravelLike\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection|\Zing\LaravelLike\Like[] $likes
 * @property-read int|null $likes_count
 */
trait Fan
{
    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     */
    public function like(Model $object): void
    {
        $this->likedItems(get_class($object))->attach($object->getKey());
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     *
     * @throws \Exception
     */
    public function unlike(Model $object): void
    {
        $this->likedItems(get_class($object))->detach($object->getKey());
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     *
     * @throws \Exception
     */
    public function toggleLike(Model $object): void
    {
        $this->likedItems(get_class($object))->toggle($object->getKey());
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     *
     * @return bool
     */
    public function hasLiked(Model $object): bool
    {
        return tap($this->relationLoaded('likes') ? $this->likes : $this->likes())
            ->where('likeable_id', $object->getKey())
            ->where('likeable_type', $object->getMorphClass())
            ->count() > 0;
    }

    public function hasNotLiked(Model $object): bool
    {
        return ! $this->hasLiked($object);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function likes(): HasMany
    {
        return $this->hasMany(config('like.models.like'), config('like.column_names.user_foreign_key'), $this->getKeyName());
    }

    /**
     * @param string $class
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    protected function likedItems(string $class): MorphToMany
    {
        return $this->morphedByMany($class, 'likeable', config('like.models.like'), config('like.column_names.user_foreign_key'), 'likeable_id')->withTimestamps();
    }
}
