<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailOtpVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        if ($user->email_verified_at) {
            return $next($request);
        }

        if ($request->routeIs('otp.verify.show', 'otp.verify', 'otp.resend', 'logout')) {
            return $next($request);
        }

        return redirect()->route('otp.verify.show');
    }
}
