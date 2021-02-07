<?php

declare(strict_types=1);

namespace Zing\LaravelLike\Tests\Events;

use Illuminate\Support\Facades\Event;
use Zing\LaravelLike\Events\Unliked;
use Zing\LaravelLike\Tests\Models\Channel;
use Zing\LaravelLike\Tests\Models\User;
use Zing\LaravelLike\Tests\TestCase;

class UnlikedTest extends TestCase
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
