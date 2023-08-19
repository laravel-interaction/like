<?php

declare(strict_types=1);

namespace LaravelInteraction\Like\Tests\Concerns;

use LaravelInteraction\Like\Tests\Models\Channel;
use LaravelInteraction\Like\Tests\Models\User;
use LaravelInteraction\Like\Tests\TestCase;

/**
 * @internal
 */
final class LikeableTest extends TestCase
{
    /**
     * @return \Iterator<array<class-string<\LaravelInteraction\Like\Tests\Models\Channel|\LaravelInteraction\Like\Tests\Models\User>>>
     */
    public static function provideModelClasses(): \Iterator
    {
        yield [Channel::class];

        yield [User::class];
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Like\Tests\Models\User|\LaravelInteraction\Like\Tests\Models\Channel> $modelClass
     */
    public function testLikes(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->like($model);
        $this->assertSame(1, $model->likeableLikes()->count());
        $this->assertSame(1, $model->likeableLikes->count());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Like\Tests\Models\User|\LaravelInteraction\Like\Tests\Models\Channel> $modelClass
     */
    public function testFansCount(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->like($model);
        $this->assertSame(1, $model->fansCount());
        $user->unlike($model);
        $this->assertSame(1, $model->fansCount());
        $model->loadCount('fans');
        $this->assertSame(0, $model->fansCount());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Like\Tests\Models\User|\LaravelInteraction\Like\Tests\Models\Channel> $modelClass
     */
    public function testFansCountForHumans(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->like($model);
        $this->assertSame('1', $model->fansCountForHumans());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Like\Tests\Models\User|\LaravelInteraction\Like\Tests\Models\Channel> $modelClass
     */
    public function testIsLikedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $this->assertFalse($model->isLikedBy($model));
        $user->like($model);
        $this->assertTrue($model->isLikedBy($user));
        $model->load('fans');
        $user->unlike($model);
        $this->assertTrue($model->isLikedBy($user));
        $model->load('fans');
        $this->assertFalse($model->isLikedBy($user));
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Like\Tests\Models\User|\LaravelInteraction\Like\Tests\Models\Channel> $modelClass
     */
    public function testIsNotLikedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $this->assertTrue($model->isNotLikedBy($model));
        $user->like($model);
        $this->assertFalse($model->isNotLikedBy($user));
        $model->load('fans');
        $user->unlike($model);
        $this->assertFalse($model->isNotLikedBy($user));
        $model->load('fans');
        $this->assertTrue($model->isNotLikedBy($user));
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Like\Tests\Models\User|\LaravelInteraction\Like\Tests\Models\Channel> $modelClass
     */
    public function testFans(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->like($model);
        $this->assertSame(1, $model->fans()->count());
        $user->unlike($model);
        $this->assertSame(0, $model->fans()->count());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Like\Tests\Models\User|\LaravelInteraction\Like\Tests\Models\Channel> $modelClass
     */
    public function testScopeWhereLikedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->like($model);
        $this->assertSame(1, $modelClass::query()->whereLikedBy($user)->count());
        $this->assertSame(0, $modelClass::query()->whereLikedBy($other)->count());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Like\Tests\Models\User|\LaravelInteraction\Like\Tests\Models\Channel> $modelClass
     */
    public function testScopeWhereNotLikedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->like($model);
        $this->assertSame(
            $modelClass::query()->whereKeyNot($model->getKey())->count(),
            $modelClass::query()->whereNotLikedBy($user)->count()
        );
        $this->assertSame($modelClass::query()->count(), $modelClass::query()->whereNotLikedBy($other)->count());
    }
}
