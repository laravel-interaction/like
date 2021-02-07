<?php

declare(strict_types=1);

namespace Zing\LaravelLike\Tests\Events;

use Illuminate\Support\Facades\Event;
use Zing\LaravelLike\Events\Liked;
use Zing\LaravelLike\Tests\Models\Channel;
use Zing\LaravelLike\Tests\Models\User;
use Zing\LaravelLike\Tests\TestCase;

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
