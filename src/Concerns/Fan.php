<?php

declare(strict_types=1);

namespace LaravelInteraction\Like\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use LaravelInteraction\Like\Like;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection|\LaravelInteraction\Like\Like[] $fanLikes
 * @property-read int|null $fan_likes_count
 */
trait Fan
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fanLikes(): HasMany
    {
        return $this->hasMany(
            config('like.models.like'),
            config('like.column_names.user_foreign_key'),
            $this->getKeyName()
        );
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     *
     * @return bool
     */
    public function hasLiked(Model $object): bool
    {
        return ($this->relationLoaded('fanLikes') ? $this->fanLikes : $this->fanLikes())
            ->where('likeable_id', $object->getKey())
            ->where('likeable_type', $object->getMorphClass())
            ->count() > 0;
    }

    public function hasNotLiked(Model $object): bool
    {
        return ! $this->hasLiked($object);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     *
     * @return \LaravelInteraction\Like\Like
     */
    public function like(Model $object): Like
    {
        $attributes = [
            'likeable_id' => $object->getKey(),
            'likeable_type' => $object->getMorphClass(),
        ];

        return $this->fanLikes()
            ->where($attributes)
            ->firstOr(function () use ($attributes) {
                $fanLikesLoaded = $this->relationLoaded('fanLikes');
                if ($fanLikesLoaded) {
                    $this->unsetRelation('fanLikes');
                }

                return $this->fanLikes()
                    ->create($attributes);
            });
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     *
     * @return bool|\LaravelInteraction\Like\Like
     */
    public function toggleLike(Model $object)
    {
        return $this->hasLiked($object) ? $this->unlike($object) : $this->like($object);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     *
     * @return bool
     */
    public function unlike(Model $object): bool
    {
        $hasNotLiked = $this->hasNotLiked($object);
        if ($hasNotLiked) {
            return true;
        }
        $fanLikesLoaded = $this->relationLoaded('fanLikes');
        if ($fanLikesLoaded) {
            $this->unsetRelation('fanLikes');
        }

        return (bool) $this->likedItems(get_class($object))
            ->detach($object->getKey());
    }

    /**
     * @param string $class
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    protected function likedItems(string $class): MorphToMany
    {
        return $this->morphedByMany(
            $class,
            'likeable',
            config('like.models.like'),
            config('like.column_names.user_foreign_key')
        )
            ->withTimestamps();
    }
}
