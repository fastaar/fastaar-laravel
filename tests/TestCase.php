<?php

namespace Fastaar\Laravel\Tests;

use Fastaar\Laravel\FastaarServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            FastaarServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Setup configuration defaults for tests
        $app['config']->set('fastaar.api_key', 'fk_test_123456');
        $app['config']->set('fastaar.webhook_secret', 'wh_secret_123456');
    }
}
