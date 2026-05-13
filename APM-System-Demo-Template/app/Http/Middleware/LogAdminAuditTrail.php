<?php

namespace App\Http\Middleware;

use App\Models\AdminAuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogAdminAuditTrail
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!$request->user() || !$request->is('admin/*') || $request->isMethod('GET') || $request->isMethod('HEAD')) {
            return $response;
        }

        AdminAuditLog::create([
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

    private function resolveModule(Request $request): string
    {
        $routeName = (string) $request->route()?->getName();
        if ($routeName === '') {
            return 'admin';
        }

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
