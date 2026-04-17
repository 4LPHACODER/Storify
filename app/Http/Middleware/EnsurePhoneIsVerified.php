<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePhoneIsVerified
{
    /**
     * Require a verified phone for non-admin users who registered with a phone number.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if ($user->isAdmin()) {
            return $next($request);
        }

        if ($user->phone_number === null || $user->phone_verified_at) {
            return $next($request);
        }

        if ($request->routeIs([
            'register.phone-verification.*',
            'logout',
            'verification.notice',
            'verification.verify',
            'verification.send',
        ])) {
            return $next($request);
        }

        return redirect()->route('register.phone-verification.show');
    }
}
