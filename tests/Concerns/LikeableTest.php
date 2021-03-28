<?php

declare(strict_types=1);

namespace LaravelInteraction\Like\Tests\Concerns;

use LaravelInteraction\Like\Tests\Models\Channel;
use LaravelInteraction\Like\Tests\Models\User;
use LaravelInteraction\Like\Tests\TestCase;

class LikeableTest extends TestCase
{
    public function modelClasses(): array
    {
        return[[Channel::class], [User::class]];
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

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Like\Tests\Models\User|\LaravelInteraction\Like\Tests\Models\Channel|string $modelClass
     */
    public function testFansCountForHumans(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->like($model);
        self::assertSame('1', $model->fansCountForHumans());
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
        self::assertSame(
            $modelClass::query()->whereKeyNot($model->getKey())->count(),
            $modelClass::query()->whereNotLikedBy($user)->count()
        );
        self::assertSame($modelClass::query()->count(), $modelClass::query()->whereNotLikedBy($other)->count());
    }
}
