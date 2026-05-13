<?php

namespace App\Http\Middleware;

use App\Models\UserActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!$request->user() || !$this->shouldLogRequest($request, $response)) {
            return $response;
        }

        UserActivityLog::create([
            'user_id' => $request->user()->id,
            'method' => strtoupper($request->method()),
            'route_name' => $request->route()?->getName(),
            'module' => $this->resolveModule($request),
            'url' => $request->fullUrl(),
            'ip_address' => $request->ip(),
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 1000),
            'response_status' => $response->getStatusCode(),
            'request_payload' => $this->sanitizePayload($request->except(['_token', '_method'])),
            'route_parameters' => $this->sanitizePayload($request->route()?->parametersWithoutNulls() ?? []),
        ]);

        return $response;
    }

    private function shouldLogRequest(Request $request, Response $response): bool
    {
        $routeName = (string) $request->route()?->getName();

        if ($routeName === '' || $request->isMethod('HEAD')) {
            return false;
        }

        if (in_array($routeName, ['notifications.feed', 'live.approvals-stamp'], true)) {
            return false;
        }

        if ($request->isMethod('GET')) {
            $contentType = strtolower((string) $response->headers->get('content-type'));

            return str_contains($contentType, 'text/html');
        }

        return true;
    }

    private function resolveModule(Request $request): string
    {
        $routeName = (string) $request->route()?->getName();
        $parts = explode('.', $routeName);

        return implode('.', array_slice($parts, 0, min(3, count($parts))));
    }

    private function sanitizePayload(mixed $value): mixed
    {
        if (is_array($value)) {
            $sanitized = [];
            foreach ($value as $key => $item) {
                if ($this->isSensitiveKey((string) $key)) {
                    $sanitized[$key] = '[REDACTED]';
                    continue;
                }

                $sanitized[$key] = $this->sanitizePayload($item);
            }

            return $sanitized;
        }

        if ($value instanceof \Illuminate\Http\UploadedFile) {
            return [
                'filename' => $value->getClientOriginalName(),
                'size' => $value->getSize(),
                'mime_type' => $value->getClientMimeType(),
            ];
        }

        if (is_string($value)) {
            return mb_substr($value, 0, 500);
        }

        return $value;
    }

    private function isSensitiveKey(string $key): bool
    {
        $key = strtolower($key);

        return str_contains($key, 'password')
            || str_contains($key, 'token')
            || str_contains($key, 'secret');
    }
}
