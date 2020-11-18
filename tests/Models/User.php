<?php

declare(strict_types=1);

namespace Zing\LaravelLike\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Zing\LaravelLike\Concerns\Fan;

/**
 * @method static \Zing\LaravelLike\Tests\Models\User|\Illuminate\Database\Eloquent\Builder query()
 */
class User extends Model
{
    use Fan;
}
