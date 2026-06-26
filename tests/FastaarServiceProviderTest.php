<?php

use Fastaar\FastaarClient;
use Fastaar\Laravel\Facades\Fastaar;

it('registers config defaults', function (): void {
    expect(config('fastaar.api_key'))->toBe('fk_test_123456');
    expect(config('fastaar.webhook_secret'))->toBe('wh_secret_123456');
    expect(config('fastaar.timeout_seconds'))->toBe(15);
});

it('binds FastaarClient as a singleton', function (): void {
    $client1 = app(FastaarClient::class);
    $client2 = app(FastaarClient::class);

    expect($client1)->toBeInstanceOf(FastaarClient::class);
    expect($client2)->toBeInstanceOf(FastaarClient::class);
    expect($client1)->toBe($client2);
});

it('resolves the client alias', function (): void {
    $client = app('fastaar');
    expect($client)->toBeInstanceOf(FastaarClient::class);
});

it('resolves the facade', function (): void {
    $resolved = Fastaar::getFacadeRoot();
    expect($resolved)->toBeInstanceOf(FastaarClient::class);
});
