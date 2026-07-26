<?php

namespace WaqasYousaf\Dullahan\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class DullahanApiAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $throttleEnabled = config('dullahan.api_throttle.enabled', true);
        $rateLimitKey = null;
        $maxAttempts = 60;

        if ($throttleEnabled) {
            $rateLimitKey = 'dullahan_api_limit:' . $request->ip();
            $maxAttempts = (int) config('dullahan.api_throttle.max_attempts', 60);
            $decayMinutes = (int) config('dullahan.api_throttle.decay_minutes', 1);

            if (RateLimiter::tooManyAttempts($rateLimitKey, $maxAttempts)) {
                $seconds = RateLimiter::availableIn($rateLimitKey);
                return response()->json([
                    'message' => 'Too many requests. Please try again later.'
                ], 429, [
                    'Retry-After' => $seconds,
                    'X-RateLimit-Limit' => $maxAttempts,
                    'X-RateLimit-Remaining' => 0,
                    'X-RateLimit-Reset' => time() + $seconds,
                ]);
            }

            RateLimiter::hit($rateLimitKey, $decayMinutes * 60);
        }

        if (config('dullahan.api_security.enabled', false)) {
            if (! $this->hasValidApiKey($request)) {
                return response()->json(['message' => 'Invalid or missing Dullahan API key.'], 401);
            }

            if (! $this->hasAllowedDomain($request)) {
                return response()->json(['message' => 'This domain is not allowed to access the Dullahan API.'], 403);
            }
        }

        $response = $next($request);

        if ($throttleEnabled && $rateLimitKey) {
            $response->headers->set('X-RateLimit-Limit', $maxAttempts);
            $response->headers->set('X-RateLimit-Remaining', RateLimiter::remaining($rateLimitKey, $maxAttempts));
        }

        return $response;
    }

    private function hasValidApiKey(Request $request): bool
    {
        $keys = config('dullahan.api_security.keys', []);

        if ($keys === []) {
            return false;
        }

        $header = config('dullahan.api_security.header', 'X-Dullahan-Api-Key');
        $parameter = config('dullahan.api_security.query_parameter', 'api_key');
        $provided = $request->header($header) ?: $request->query($parameter);

        return is_string($provided) && in_array($provided, $keys, true);
    }

    private function hasAllowedDomain(Request $request): bool
    {
        $domains = config('dullahan.api_security.allowed_domains', []);

        if ($domains === []) {
            return true;
        }

        $origin = $request->headers->get('origin') ?: $request->headers->get('referer') ?: $request->getHost();
        $host = parse_url($origin, PHP_URL_HOST) ?: $origin;

        return in_array($host, $domains, true);
    }
}
