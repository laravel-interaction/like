<?php

declare(strict_types=1);

namespace LaravelInteraction\Like\Tests\Concerns;

use LaravelInteraction\Like\Like;
use LaravelInteraction\Like\Tests\Models\Channel;
use LaravelInteraction\Like\Tests\Models\User;
use LaravelInteraction\Like\Tests\TestCase;

/**
 * @internal
 */
final class FanTest extends TestCase
{
    public function testLike(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->like($channel);
        $this->assertDatabaseHas(
            Like::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                'likeable_type' => $channel->getMorphClass(),
                'likeable_id' => $channel->getKey(),
            ]
        );
        $user->load('fanLikes');
        $user->unlike($channel);
        $user->load('fanLikes');
        $user->like($channel);
    }

    public function testUnlike(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->like($channel);
        $this->assertDatabaseHas(
            Like::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                'likeable_type' => $channel->getMorphClass(),
                'likeable_id' => $channel->getKey(),
            ]
        );
        $user->unlike($channel);
        $this->assertDatabaseMissing(
            Like::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                'likeable_type' => $channel->getMorphClass(),
                'likeable_id' => $channel->getKey(),
            ]
        );
    }

    public function testToggleLike(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->toggleLike($channel);
        $this->assertDatabaseHas(
            Like::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                'likeable_type' => $channel->getMorphClass(),
                'likeable_id' => $channel->getKey(),
            ]
        );
        $user->toggleLike($channel);
        $this->assertDatabaseMissing(
            Like::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                'likeable_type' => $channel->getMorphClass(),
                'likeable_id' => $channel->getKey(),
            ]
        );
    }

    public function testLikes(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->toggleLike($channel);
        self::assertSame(1, $user->fanLikes()->count());
        self::assertSame(1, $user->fanLikes->count());
    }

    public function testHasLiked(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->toggleLike($channel);
        self::assertTrue($user->hasLiked($channel));
        $user->toggleLike($channel);
        $user->load('fanLikes');
        self::assertFalse($user->hasLiked($channel));
    }

    public function testHasNotLiked(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->toggleLike($channel);
        self::assertFalse($user->hasNotLiked($channel));
        $user->toggleLike($channel);
        self::assertTrue($user->hasNotLiked($channel));
    }
}
