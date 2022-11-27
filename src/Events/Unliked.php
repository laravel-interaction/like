<?php

declare(strict_types=1);

namespace LaravelInteraction\Like\Events;

use Illuminate\Database\Eloquent\Model;

class Unliked
{
    public function __construct(
        public Model $model
    ) {
    }
}
