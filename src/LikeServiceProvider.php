<?php

declare(strict_types=1);

namespace Zing\LaravelLike;

use Illuminate\Support\ServiceProvider;

class LikeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes(
                [
                    $this->getConfigPath() => config_path('like.php'),
                ],
                'like-config'
            );
            $this->publishes(
                [
                    $this->getMigrationsPath() => database_path('migrations'),
                ],
                'like-migrations'
            );
            if ($this->shouldLoadMigrations()) {
                $this->loadMigrationsFrom($this->getMigrationsPath());
            }
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom($this->getConfigPath(), 'like');
    }

    protected function getConfigPath(): string
    {
        return __DIR__ . '/../config/like.php';
    }

    protected function getMigrationsPath(): string
    {
        return __DIR__ . '/../migrations';
    }

    private function shouldLoadMigrations(): bool
    {
        return (bool) config('like.load_migrations');
    }
}
