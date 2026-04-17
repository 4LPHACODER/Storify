<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyPhoneOtpRequest;
use App\Services\Auth\RegistrationOtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PhoneVerificationController extends Controller
{
    public function show(Request $request): Response|RedirectResponse
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->route('login');
        }

        if ($user->phone_verified_at || $user->phone_number === null) {
            return redirect()->intended(route('dashboard'));
        }

        return Inertia::render('auth/VerifyPhoneOtp', [
            'phoneLastFour' => substr((string) preg_replace('/\D+/', '', $user->phone_number ?? ''), -4) ?: null,
            'status' => $request->session()->get('status'),
        ]);
    }

    public function store(VerifyPhoneOtpRequest $request, RegistrationOtpService $otpService): RedirectResponse
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->route('login');
        }

        if (! $otpService->verify($user, $request->validated('otp_code'))) {
            throw ValidationException::withMessages([
                'otp_code' => __('Invalid or expired verification code.'),
            ]);
        }

        return redirect()->intended(route('dashboard'))->with('status', __('Your phone number has been verified.'));
    }

    public function resend(Request $request, RegistrationOtpService $otpService): RedirectResponse
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->route('login');
        }

        if ($user->phone_verified_at) {
            return redirect()->route('dashboard');
        }

        $otpService->resend($user);

        return back()->with('status', __('A new verification code has been sent to your phone.'));
    }
}
