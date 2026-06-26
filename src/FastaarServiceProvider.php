<?php

declare(strict_types=1);

namespace Fastaar\Laravel;

use Fastaar\FastaarClient;
use Illuminate\Support\ServiceProvider;

class FastaarServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/fastaar.php', 'fastaar');

        $this->app->singleton(FastaarClient::class, function ($app): FastaarClient {
            return new FastaarClient(
                apiKey: (string) config('fastaar.api_key'),
                timeoutSeconds: (int) config('fastaar.timeout_seconds', 15),
            );
        });

        $this->app->alias(FastaarClient::class, 'fastaar');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/fastaar.php' => config_path('fastaar.php'),
            ], 'fastaar-config');
        }
    }
}
