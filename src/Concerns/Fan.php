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
    public function fanLikes(): HasMany
    {
        return $this->hasMany(config('like.models.pivot'), config('like.column_names.user_foreign_key'));
    }

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

    public function toggleLike(Model $object): bool|Like
    {
        return $this->hasLiked($object) ? $this->unlike($object) : $this->like($object);
    }

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

        return (bool) $this->likedItems($object::class)
            ->detach($object->getKey());
    }

    protected function likedItems(string $class): MorphToMany
    {
        return $this->morphedByMany(
            $class,
            'likeable',
            config('like.models.pivot'),
            config('like.column_names.user_foreign_key')
        )
            ->withTimestamps();
    }
}
