<?php

declare(strict_types=1);

namespace LaravelInteraction\Like\Tests\Concerns;

use LaravelInteraction\Like\Tests\Models\Channel;
use LaravelInteraction\Like\Tests\Models\User;
use LaravelInteraction\Like\Tests\TestCase;
use Mockery;

class LikeableTest extends TestCase
{    public function modelClasses(): array
{
    return[
        [Channel::class],
        [User::class],
    ];
}

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Like\Tests\Models\User|\LaravelInteraction\Like\Tests\Models\Channel|string $modelClass
     */
    public function testLikes(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->like($model);
        self::assertSame(1, $model->likeableLikes()->count());
        self::assertSame(1, $model->likeableLikes->count());
    }
    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Like\Tests\Models\User|\LaravelInteraction\Like\Tests\Models\Channel|string $modelClass
     */
    public function testFansCount(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->like($model);
        self::assertSame(1, $model->fansCount());
        $user->unlike($model);
        self::assertSame(1, $model->fansCount());
        $model->loadCount('fans');
        self::assertSame(0, $model->fansCount());
    }

    public function data(): array
    {
        return [
            [0, '0', '0', '0'],
            [1, '1', '1', '1'],
            [12, '12', '12', '12'],
            [123, '123', '123', '123'],
            [12345, '12.3K', '12.35K', '12.34K'],
            [1234567, '1.2M', '1.23M', '1.23M'],
            [123456789, '123.5M', '123.46M', '123.46M'],
            [12345678901, '12.3B', '12.35B', '12.35B'],
            [1234567890123, '1.2T', '1.23T', '1.23T'],
            [1234567890123456, '1.2Qa', '1.23Qa', '1.23Qa'],
            [1234567890123456789, '1.2Qi', '1.23Qi', '1.23Qi'],
        ];
    }

    /**
     * @dataProvider data
     *
     * @param mixed $actual
     * @param mixed $onePrecision
     * @param mixed $twoPrecision
     * @param mixed $halfDown
     */
    public function testFansCountForHumans($actual, $onePrecision, $twoPrecision, $halfDown): void
    {
        $channel = Mockery::mock(Channel::class);
        $channel->shouldReceive('fansCountForHumans')->passthru();
        $channel->shouldReceive('fansCount')->andReturn($actual);
        self::assertSame($onePrecision, $channel->fansCountForHumans());
        self::assertSame($twoPrecision, $channel->fansCountForHumans(2));
        self::assertSame($halfDown, $channel->fansCountForHumans(2, PHP_ROUND_HALF_DOWN));
    }
    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Like\Tests\Models\User|\LaravelInteraction\Like\Tests\Models\Channel|string $modelClass
     */
    public function testIsLikedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        self::assertFalse($model->isLikedBy($model));
        $user->like($model);
        self::assertTrue($model->isLikedBy($user));
        $model->load('fans');
        $user->unlike($model);
        self::assertTrue($model->isLikedBy($user));
        $model->load('fans');
        self::assertFalse($model->isLikedBy($user));
    }
    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Like\Tests\Models\User|\LaravelInteraction\Like\Tests\Models\Channel|string $modelClass
     */
    public function testIsNotLikedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        self::assertTrue($model->isNotLikedBy($model));
        $user->like($model);
        self::assertFalse($model->isNotLikedBy($user));
        $model->load('fans');
        $user->unlike($model);
        self::assertFalse($model->isNotLikedBy($user));
        $model->load('fans');
        self::assertTrue($model->isNotLikedBy($user));
    }
    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Like\Tests\Models\User|\LaravelInteraction\Like\Tests\Models\Channel|string $modelClass
     */
    public function testFans(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->like($model);
        self::assertSame(1, $model->fans()->count());
        $user->unlike($model);
        self::assertSame(0, $model->fans()->count());
    }
    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Like\Tests\Models\User|\LaravelInteraction\Like\Tests\Models\Channel|string $modelClass
     */
    public function testScopeWhereLikedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->like($model);
        self::assertSame(1, $modelClass::query()->whereLikedBy($user)->count());
        self::assertSame(0, $modelClass::query()->whereLikedBy($other)->count());
    }
    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Like\Tests\Models\User|\LaravelInteraction\Like\Tests\Models\Channel|string $modelClass
     */
    public function testScopeWhereNotLikedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->like($model);
        self::assertSame($modelClass::query()->whereKeyNot($model->getKey())->count(), $modelClass::query()->whereNotLikedBy($user)->count());
        self::assertSame($modelClass::query()->count(), $modelClass::query()->whereNotLikedBy($other)->count());
    }
}
