<?php

declare(strict_types=1);

namespace AndyDefer\LaravelComments;

use AndyDefer\LaravelComments\Repositories\CommentRepository;
use AndyDefer\LaravelComments\Services\CommentService;
use Illuminate\Support\ServiceProvider;

final class CommentsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CommentRepository::class);
        $this->app->singleton(CommentService::class);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'Comments-migrations');
    }
}
