<?php

declare(strict_types=1);

namespace LaravelInteraction\Like;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use LaravelInteraction\Like\Events\Liked;
use LaravelInteraction\Like\Events\Unliked;
use LaravelInteraction\Support\InteractionList;
use LaravelInteraction\Support\Models\Interaction;

/**
 * @property \Illuminate\Database\Eloquent\Model $user
 * @property \Illuminate\Database\Eloquent\Model $fan
 * @property \Illuminate\Database\Eloquent\Model $likeable
 *
 * @method static \LaravelInteraction\Like\Like|\Illuminate\Database\Eloquent\Builder withType(string $type)
 * @method static \LaravelInteraction\Like\Like|\Illuminate\Database\Eloquent\Builder query()
 */
class Like extends Interaction
{
    protected $interaction = InteractionList::LIKE;

    protected $tableNameKey = 'likes';

    protected $morphTypeName = 'likeable';

    protected $dispatchesEvents = [
        'created' => Liked::class,
        'deleted' => Unliked::class,
    ];

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
}
