<?php

namespace WaqasYousaf\Dulluhan\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DulluhanApiAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('dulluhan.api_security.enabled', false)) {
            return $next($request);
        }

        if (! $this->hasValidApiKey($request)) {
            return response()->json(['message' => 'Invalid or missing Dulluhan API key.'], 401);
        }

        if (! $this->hasAllowedDomain($request)) {
            return response()->json(['message' => 'This domain is not allowed to access the Dulluhan API.'], 403);
        }

        return $next($request);
    }

    private function hasValidApiKey(Request $request): bool
    {
        $keys = config('dulluhan.api_security.keys', []);

        if ($keys === []) {
            return false;
        }

        $header = config('dulluhan.api_security.header', 'X-Dulluhan-Api-Key');
        $parameter = config('dulluhan.api_security.query_parameter', 'api_key');
        $provided = $request->header($header) ?: $request->query($parameter);

        return is_string($provided) && in_array($provided, $keys, true);
    }

    private function hasAllowedDomain(Request $request): bool
    {
        $domains = config('dulluhan.api_security.allowed_domains', []);

        if ($domains === []) {
            return true;
        }

        $origin = $request->headers->get('origin') ?: $request->headers->get('referer') ?: $request->getHost();
        $host = parse_url($origin, PHP_URL_HOST) ?: $origin;

        return in_array($host, $domains, true);
    }
}
