<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceCanonicalHost
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $appUrl = (string) config('app.url');
        $canonicalHost = parse_url($appUrl, PHP_URL_HOST);

        if (!$canonicalHost) {
            return $next($request);
        }

        $currentHost = strtolower((string) $request->getHost());
        $canonicalHost = strtolower((string) $canonicalHost);

        if ($currentHost === $canonicalHost || in_array($currentHost, ['localhost', '127.0.0.1'], true)) {
            return $next($request);
        }

        $scheme = parse_url($appUrl, PHP_URL_SCHEME) ?: ($request->isSecure() ? 'https' : 'http');
        $port = parse_url($appUrl, PHP_URL_PORT);
        $portSuffix = $port ? ':' . $port : '';
        $targetUrl = $scheme . '://' . $canonicalHost . $portSuffix . $request->getRequestUri();

        return new RedirectResponse($targetUrl, 301);
    }
}
