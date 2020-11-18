<?php

declare(strict_types=1);

namespace Zing\LaravelLike\Tests\Concerns;

use Zing\LaravelLike\Like;
use Zing\LaravelLike\Tests\Models\Channel;
use Zing\LaravelLike\Tests\Models\User;
use Zing\LaravelLike\Tests\TestCase;

class FanTest extends TestCase
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
        self::assertSame(1, $user->likes()->count());
        self::assertSame(1, $user->likes->count());
    }

    public function testHasLiked(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->toggleLike($channel);
        self::assertTrue($user->hasLiked($channel));
        $user->toggleLike($channel);
        $user->load('likes');
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
