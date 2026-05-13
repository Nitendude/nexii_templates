<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasAccess
{
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        $permissions = collect($permissions)
            ->flatMap(fn ($permissionGroup) => preg_split('/[|,]/', $permissionGroup) ?: [])
            ->map(fn ($permission) => trim((string) $permission))
            ->filter()
            ->values()
            ->all();

        $hasAccess = $user && !empty($permissions)
            ? collect($permissions)->contains(fn ($permission) => $user->hasAccess($permission))
            : false;

        if (!$hasAccess) {
            abort(403);
        }

        return $next($request);
    }
}
