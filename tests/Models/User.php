<?php

declare(strict_types=1);

namespace LaravelInteraction\Like\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelInteraction\Like\Concerns\Fan;
use LaravelInteraction\Like\Concerns\Likeable;

/**
 * @method static \LaravelInteraction\Like\Tests\Models\User|\Illuminate\Database\Eloquent\Builder query()
 */
class User extends Model
{
    use Fan;
    use Likeable;
}
