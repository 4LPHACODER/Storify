<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsCustomer
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], HttpResponse::HTTP_UNAUTHORIZED);
            }

            return redirect()->guest(Route::has('login') ? route('login') : '/login');
        }

        if (! $user->isCustomer()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden.'], HttpResponse::HTTP_FORBIDDEN);
            }

            abort(HttpResponse::HTTP_FORBIDDEN, 'Access denied.');
        }

        return $next($request);
    }
}
