<?php

namespace Fastaar\Laravel\Http\Middleware;

use Closure;
use Fastaar\WebhookSignature;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyWebhookSignature
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('fastaar.webhook_secret');

        if (empty($secret)) {
            abort(500, 'Fastaar webhook secret is not configured.');
        }

        $signature = $request->header('X-Fastaar-Signature') ?? '';
        $rawBody = $request->getContent();

        if (! WebhookSignature::verify((string) $secret, (string) $rawBody, (string) $signature)) {
            abort(400, 'Invalid webhook signature.');
        }

        return $next($request);
    }
}
