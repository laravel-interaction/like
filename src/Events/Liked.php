<?php

declare(strict_types=1);

namespace LaravelInteraction\Like\Events;

use Illuminate\Database\Eloquent\Model;

class Liked
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $like;

    /**
     * Liked constructor.
     *
     * @param \Illuminate\Database\Eloquent\Model $like
     */
    public function __construct(Model $like)
    {
        $this->like = $like;
    }
}
