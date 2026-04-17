<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request): JsonResponse|RedirectResponse
    {
        $url = route('register.phone-verification.show');

        if ($request->wantsJson()) {
            return response()->json([
                'redirect' => $url,
            ], 201);
        }

        return redirect()->to($url);
    }
}
