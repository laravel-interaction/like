<?php

declare(strict_types=1);

namespace LaravelInteraction\Like;

use LaravelInteraction\Support\InteractionList;
use LaravelInteraction\Support\InteractionServiceProvider;

class LikeServiceProvider extends InteractionServiceProvider
{
    protected $interaction = InteractionList::LIKE;
}
