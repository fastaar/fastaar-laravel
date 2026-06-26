<?php

use Fastaar\Laravel\Http\Middleware\VerifyWebhookSignature;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

it('aborts with 500 when webhook secret is not configured', function (): void {
    config()->set('fastaar.webhook_secret');

    $request = Request::create('/webhook', 'POST', [], [], [], [], '{"event":"payment.completed"}');
    $middleware = new VerifyWebhookSignature;

    expect(fn (): Response => $middleware->handle($request, fn (): ResponseFactory|\Illuminate\Http\Response => response('OK')))
        ->toThrow(HttpException::class, 'Fastaar webhook secret is not configured.');
});

it('aborts with 400 when webhook signature is invalid or missing', function (): void {
    $request = Request::create('/webhook', 'POST', [], [], [], [], '{"event":"payment.completed"}');
    $middleware = new VerifyWebhookSignature;

    expect(fn (): Response => $middleware->handle($request, fn (): ResponseFactory|\Illuminate\Http\Response => response('OK')))
        ->toThrow(HttpException::class, 'Invalid webhook signature.');
});

it('allows valid webhooks to pass through', function (): void {
    $secret = 'wh_secret_123456';
    config()->set('fastaar.webhook_secret', $secret);

    $timestamp = time();
    $rawBody = '{"event":"payment.completed"}';
    $hmac = hash_hmac('sha256', "{$timestamp}.{$rawBody}", $secret);
    $signatureHeader = "t={$timestamp},v1={$hmac}";

    $request = Request::create('/webhook', 'POST', [], [], [], [
        'HTTP_X_FASTAAR_SIGNATURE' => $signatureHeader,
    ], $rawBody);

    $middleware = new VerifyWebhookSignature;
    $response = $middleware->handle($request, fn (): ResponseFactory|\Illuminate\Http\Response => response('OK'));

    expect($response->getContent())->toBe('OK');
});
