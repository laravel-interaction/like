<?php

declare(strict_types=1);

namespace Zing\LaravelLike\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Zing\LaravelLike\Concerns\Likeable;

/**
 * @method static \Zing\LaravelLike\Tests\Models\Channel|\Illuminate\Database\Eloquent\Builder query()
 */
class Channel extends Model
{
    use Likeable;
}
