<?php

declare(strict_types=1);

namespace LaravelInteraction\Like\Tests\Events;

use Illuminate\Support\Facades\Event;
use LaravelInteraction\Like\Events\Liked;
use LaravelInteraction\Like\Tests\Models\Channel;
use LaravelInteraction\Like\Tests\Models\User;
use LaravelInteraction\Like\Tests\TestCase;

class LikedTest extends TestCase
{
    public function testOnce(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        Event::fake([Liked::class]);
        $user->like($channel);
        Event::assertDispatchedTimes(Liked::class);
    }

    public function testTimes(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        Event::fake([Liked::class]);
        $user->like($channel);
        $user->like($channel);
        $user->like($channel);
        Event::assertDispatchedTimes(Liked::class);
    }

    public function testToggle(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        Event::fake([Liked::class]);
        $user->toggleLike($channel);
        Event::assertDispatchedTimes(Liked::class);
    }
}
