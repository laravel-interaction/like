<?php

declare(strict_types=1);

namespace Zing\LaravelLike\Tests;

use Illuminate\Support\Carbon;
use Zing\LaravelLike\Like;
use Zing\LaravelLike\Tests\Models\Channel;
use Zing\LaravelLike\Tests\Models\User;

class LikeTest extends TestCase
{
    /**
     * @var \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|\Zing\LaravelLike\Tests\Models\User
     */
    protected $user;

    /**
     * @var \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|\Zing\LaravelLike\Tests\Models\Channel
     */
    protected $channel;

    /**
     * @var \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|\Zing\LaravelLike\Like|null
     */
    protected $like;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::query()->create();
        $this->channel = Channel::query()->create();
        $this->user->like($this->channel);
        $this->like = Like::query()->first();
    }

    public function testLikeTimestamp(): void
    {
        self::assertInstanceOf(Carbon::class, $this->like->created_at);
        self::assertInstanceOf(Carbon::class, $this->like->updated_at);
    }

    public function testScopeWithType(): void
    {
        self::assertSame(1, Like::query()->withType(Channel::class)->count());
        self::assertSame(0, Like::query()->withType(User::class)->count());
    }

    public function testGetTable(): void
    {
        self::assertSame(config('like.table_names.likes'), $this->like->getTable());
    }

    public function testFan(): void
    {
        self::assertInstanceOf(User::class, $this->like->fan);
    }

    public function testLikeable(): void
    {
        self::assertInstanceOf(Channel::class, $this->like->likeable);
    }

    public function testUser(): void
    {
        self::assertInstanceOf(User::class, $this->like->user);
    }

    public function testIsLikedTo(): void
    {
        self::assertTrue($this->like->isLikedTo($this->channel));
        self::assertFalse($this->like->isLikedTo($this->user));
    }

    public function testIsLikedBy(): void
    {
        self::assertFalse($this->like->isLikedBy($this->channel));
        self::assertTrue($this->like->isLikedBy($this->user));
    }
}
