<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\RegistrationOtpService;
use App\Services\Phone\PhilippineMobileNormalizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PhoneVerificationController extends Controller
{
    public function __construct(
        private PhilippineMobileNormalizer $phoneNormalizer,
        private RegistrationOtpService $registrationOtpService,
    ) {}

    /**
     * Verify phone OTP using phone_number + otp or otp_code (no session/CSRF).
     */
    public function verify(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone_number' => ['required', 'string', 'max:32', 'regex:/^[\d\s\+\-\(\)]+$/'],
            'otp_code' => ['sometimes', 'string', 'size:6', 'regex:/^[0-9]+$/'],
            'otp' => ['sometimes', 'string', 'size:6', 'regex:/^[0-9]+$/'],
        ]);

        $code = $validated['otp_code'] ?? $validated['otp'] ?? null;

        if ($code === null || $code === '') {
            throw ValidationException::withMessages([
                'otp_code' => [__('Provide either otp or otp_code (6 digits).')],
            ]);
        }

        $phone = $this->phoneNormalizer->normalize($validated['phone_number']);
        $this->phoneNormalizer->assertValid($phone);

        $user = User::query()->where('phone_number', $phone)->first();

        if ($user === null || ! $this->registrationOtpService->verify($user, $code)) {
            throw ValidationException::withMessages([
                'otp_code' => [__('Invalid or expired verification code.')],
            ]);
        }

        return response()->json([
            'message' => __('Your phone number has been verified.'),
            'user' => $user->fresh(),
        ]);
    }

    /**
     * Resend OTP SMS for the given phone number (queues via existing SMS flow).
     */
    public function resend(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone_number' => ['required', 'string', 'max:32', 'regex:/^[\d\s\+\-\(\)]+$/'],
        ]);

        $phone = $this->phoneNormalizer->normalize($validated['phone_number']);
        $this->phoneNormalizer->assertValid($phone);

        $user = User::query()->where('phone_number', $phone)->first();

        if ($user === null) {
            return response()->json([
                'message' => __('If an account exists for this number, a verification code has been sent.'),
            ], 200);
        }

        if ($user->phone_verified_at) {
            return response()->json([
                'message' => __('This phone number is already verified.'),
                'user' => $user,
            ], 200);
        }

        $this->registrationOtpService->resend($user);

        return response()->json([
            'message' => __('A new verification code has been sent to your phone.'),
        ], 200);
    }
}
