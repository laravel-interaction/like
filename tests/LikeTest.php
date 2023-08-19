<?php

declare(strict_types=1);

namespace LaravelInteraction\Like\Tests;

use Illuminate\Support\Carbon;
use LaravelInteraction\Like\Like;
use LaravelInteraction\Like\Tests\Models\Channel;
use LaravelInteraction\Like\Tests\Models\User;

/**
 * @internal
 */
final class LikeTest extends TestCase
{
    private \LaravelInteraction\Like\Tests\Models\User $user;

    private \LaravelInteraction\Like\Tests\Models\Channel $channel;

    private \LaravelInteraction\Like\Like $like;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::query()->create();
        $this->channel = Channel::query()->create();
        $this->user->like($this->channel);
        $this->like = Like::query()->firstOrFail();
    }

    public function testLikeTimestamp(): void
    {
        $this->assertInstanceOf(Carbon::class, $this->like->created_at);
        $this->assertInstanceOf(Carbon::class, $this->like->updated_at);
    }

    public function testScopeWithType(): void
    {
        $this->assertSame(1, Like::query()->withType(Channel::class)->count());
        $this->assertSame(0, Like::query()->withType(User::class)->count());
    }

    public function testGetTable(): void
    {
        $this->assertSame(config('like.table_names.pivot'), $this->like->getTable());
    }

    public function testFan(): void
    {
        $this->assertInstanceOf(User::class, $this->like->fan);
    }

    public function testLikeable(): void
    {
        $this->assertInstanceOf(Channel::class, $this->like->likeable);
    }

    public function testUser(): void
    {
        $this->assertInstanceOf(User::class, $this->like->user);
    }

    public function testIsLikedTo(): void
    {
        $this->assertTrue($this->like->isLikedTo($this->channel));
        $this->assertFalse($this->like->isLikedTo($this->user));
    }

    public function testIsLikedBy(): void
    {
        $this->assertFalse($this->like->isLikedBy($this->channel));
        $this->assertTrue($this->like->isLikedBy($this->user));
    }
}
