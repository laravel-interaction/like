# Laravel Like

User like/unlike behaviour for Laravel.

<p align="center">
<a href="https://github.com/zingimmick/laravel-like/actions"><img src="https://github.com/zingimmick/laravel-like/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://codecov.io/gh/zingimmick/laravel-like"><img src="https://codecov.io/gh/zingimmick/laravel-like/branch/main/graph/badge.svg" alt="Code Coverage" /></a>
<a href="https://packagist.org/packages/zing/laravel-like"><img src="https://poser.pugx.org/zing/laravel-like/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/zing/laravel-like"><img src="https://poser.pugx.org/zing/laravel-like/downloads" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/zing/laravel-like"><img src="https://poser.pugx.org/zing/laravel-like/v/unstable.svg" alt="Latest Unstable Version"></a>
<a href="https://packagist.org/packages/zing/laravel-like"><img src="https://poser.pugx.org/zing/laravel-like/license" alt="License"></a>
<a href="https://codeclimate.com/github/zingimmick/laravel-like/maintainability"><img src="https://api.codeclimate.com/v1/badges/82036f5ecf894e9c395d/maintainability" alt="Code Climate" /></a>
</p>

> **Requires [PHP 7.2.0+](https://php.net/releases/)**

Require Laravel Like using [Composer](https://getcomposer.org):

```bash
composer require zing/laravel-like
```

## Usage

### Setup Fan

```php
use Illuminate\Database\Eloquent\Model;
use Zing\LaravelLike\Concerns\Fan;

class User extends Model
{
    use Fan;
}
```

### Setup Likeable

```php
use Illuminate\Database\Eloquent\Model;
use Zing\LaravelLike\Concerns\Likeable;

class Channel extends Model
{
    use Likeable;
}
```

### Fan

```php
use Zing\LaravelLike\Tests\Models\Channel;
/** @var \Zing\LaravelLike\Tests\Models\User $user */
/** @var \Zing\LaravelLike\Tests\Models\Channel $channel */
// Like to Likeable
$user->like($channel);
$user->unlike($channel);
$user->toggleLike($channel);

// Compare Likeable
$user->hasLiked($channel);
$user->hasNotLiked($channel);

// Get liked info
$user->likes()->count(); 

// with type
$user->likes()->withType(Channel::class)->count(); 

// get liked channels
Channel::query()->whereLikedBy($user)->get();

// get liked channels doesnt liked
Channel::query()->whereNotLikedBy($user)->get();
```

### Likeable

```php
use Zing\LaravelLike\Tests\Models\User;
use Zing\LaravelLike\Tests\Models\Channel;
/** @var \Zing\LaravelLike\Tests\Models\User $user */
/** @var \Zing\LaravelLike\Tests\Models\Channel $channel */
// Compare Fan
$channel->isLikedBy($user); 
$channel->isNotLikedBy($user);
// Get fans info
$channel->fans->each(function (User $user){
    echo $user->getKey();
});

$channels = Channel::query()->withCount('fans')->get();
$channels->each(function (Channel $channel){
    echo $channel->fans()->count(); // 1100
    echo $channel->fans_count; // "1100"
    echo $channel->fansCount(); // 1100
    echo $channel->fansCountForHumans(); // "1.1K"
});
```

## License

Laravel Like is an open-sourced software licensed under the [MIT license](LICENSE).
