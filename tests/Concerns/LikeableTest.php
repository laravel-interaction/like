<?php

declare(strict_types=1);

namespace Zing\LaravelLike\Tests\Concerns;

use Mockery;
use Zing\LaravelLike\Tests\Models\Channel;
use Zing\LaravelLike\Tests\Models\User;
use Zing\LaravelLike\Tests\TestCase;

class LikeableTest extends TestCase
{
    public function testLikes(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->like($channel);
        self::assertSame(1, $channel->likes()->count());
        self::assertSame(1, $channel->likes->count());
    }

    public function testFansCount(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->like($channel);
        self::assertSame(1, $channel->fansCount());
        $user->unlike($channel);
        self::assertSame(1, $channel->fansCount());
        $channel->loadCount('fans');
        self::assertSame(0, $channel->fansCount());
    }

    public function data(): array
    {
        return [
            [0, '0', '0', '0'],
            [1, '1', '1', '1'],
            [12, '12', '12', '12'],
            [123, '123', '123', '123'],
            [12345, '12.3K', '12.35K', '12.34K'],
            [1234567, '1.2M', '1.23M', '1.23M'],
            [123456789, '123.5M', '123.46M', '123.46M'],
            [12345678901, '12.3B', '12.35B', '12.35B'],
            [1234567890123, '1.2T', '1.23T', '1.23T'],
            [1234567890123456, '1.2Qa', '1.23Qa', '1.23Qa'],
            [1234567890123456789, '1.2Qi', '1.23Qi', '1.23Qi'],
        ];
    }

    /**
     * @dataProvider data
     *
     * @param mixed $actual
     * @param mixed $onePrecision
     * @param mixed $twoPrecision
     * @param mixed $halfDown
     */
    public function testFansCountForHumans($actual, $onePrecision, $twoPrecision, $halfDown): void
    {
        $channel = Mockery::mock(Channel::class);
        $channel->shouldReceive('fansCountForHumans')->passthru();
        $channel->shouldReceive('fansCount')->andReturn($actual);
        self::assertSame($onePrecision, $channel->fansCountForHumans());
        self::assertSame($twoPrecision, $channel->fansCountForHumans(2));
        self::assertSame($halfDown, $channel->fansCountForHumans(2, PHP_ROUND_HALF_DOWN));
    }

    public function testIsLikedBy(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        self::assertFalse($channel->isLikedBy($channel));
        $user->like($channel);
        self::assertTrue($channel->isLikedBy($user));
        $channel->load('fans');
        $user->unlike($channel);
        self::assertTrue($channel->isLikedBy($user));
        $channel->load('fans');
        self::assertFalse($channel->isLikedBy($user));
    }

    public function testIsNotLikedBy(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        self::assertTrue($channel->isNotLikedBy($channel));
        $user->like($channel);
        self::assertFalse($channel->isNotLikedBy($user));
        $channel->load('fans');
        $user->unlike($channel);
        self::assertFalse($channel->isNotLikedBy($user));
        $channel->load('fans');
        self::assertTrue($channel->isNotLikedBy($user));
    }

    public function testFans(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->like($channel);
        self::assertSame(1, $channel->fans()->count());
        $user->unlike($channel);
        self::assertSame(0, $channel->fans()->count());
    }

    public function testScopeWhereLikedBy(): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $channel = Channel::query()->create();
        $user->like($channel);
        self::assertSame(1, Channel::query()->whereLikedBy($user)->count());
        self::assertSame(0, Channel::query()->whereLikedBy($other)->count());
    }

    public function testScopeWhereNotLikedBy(): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $channel = Channel::query()->create();
        $user->like($channel);
        self::assertSame(0, Channel::query()->whereNotLikedBy($user)->count());
        self::assertSame(1, Channel::query()->whereNotLikedBy($other)->count());
    }
}
