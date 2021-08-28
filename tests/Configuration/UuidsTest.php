<?php

declare(strict_types=1);

namespace LaravelInteraction\Like\Tests\Configuration;

use LaravelInteraction\Like\Like;
use LaravelInteraction\Like\Tests\Models\Channel;
use LaravelInteraction\Like\Tests\Models\User;
use LaravelInteraction\Like\Tests\TestCase;

class UuidsTest extends TestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        config([
            'like.uuids' => true,
        ]);
    }

    public function testKeyType(): void
    {
        $like = new Like();
        self::assertSame('string', $like->getKeyType());
    }

    public function testIncrementing(): void
    {
        $like = new Like();
        self::assertFalse($like->getIncrementing());
    }

    public function testKeyName(): void
    {
        $like = new Like();
        self::assertSame('uuid', $like->getKeyName());
    }

    public function testKey(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->like($channel);
        self::assertIsString($user->fanLikes()->firstOrFail()->getKey());
    }
}
