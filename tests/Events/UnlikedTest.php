<?php

declare(strict_types=1);

namespace LaravelInteraction\Like\Tests\Events;

use Illuminate\Support\Facades\Event;
use LaravelInteraction\Like\Events\Unliked;
use LaravelInteraction\Like\Tests\Models\Channel;
use LaravelInteraction\Like\Tests\Models\User;
use LaravelInteraction\Like\Tests\TestCase;

/**
 * @internal
 */
final class UnlikedTest extends TestCase
{
    public function testOnce(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->like($channel);
        Event::fake();
        $user->unlike($channel);
        Event::assertDispatchedTimes(Unliked::class);
    }

    public function testTimes(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->like($channel);
        Event::fake([Unliked::class]);
        $user->unlike($channel);
        $user->unlike($channel);
        Event::assertDispatchedTimes(Unliked::class);
    }

    public function testToggle(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        Event::fake([Unliked::class]);
        $user->toggleLike($channel);
        $user->toggleLike($channel);
        Event::assertDispatchedTimes(Unliked::class);
    }
}
