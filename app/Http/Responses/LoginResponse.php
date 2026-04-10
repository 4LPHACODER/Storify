<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    /**
     * Create an authenticated response.
     */
    public function toResponse($request): JsonResponse|RedirectResponse
    {
        $redirectTo = route('dashboard');

        if ($request->wantsJson()) {
            return response()->json([
                'two_factor' => false,
                'redirect' => $redirectTo,
            ]);
        }

        return redirect()->intended($redirectTo);
    }
}
