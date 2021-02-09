<?php

declare(strict_types=1);

namespace LaravelInteraction\Like\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelInteraction\Like\Concerns\Likeable;

/**
 * @method static \LaravelInteraction\Like\Tests\Models\Channel|\Illuminate\Database\Eloquent\Builder query()
 */
class Channel extends Model
{
    use Likeable;
}
