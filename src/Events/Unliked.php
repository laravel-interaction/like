<?php

declare(strict_types=1);

namespace LaravelInteraction\Like\Events;

use Illuminate\Database\Eloquent\Model;

class Unliked
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $like;

    public function __construct(Model $like)
    {
        $this->like = $like;
    }
}
