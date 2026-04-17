<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\RegistrationOtpService;
use App\Services\Phone\PhilippineMobileNormalizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(
        Request $request,
        PhilippineMobileNormalizer $phoneNormalizer,
        RegistrationOtpService $registrationOtpService,
    ): JsonResponse {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone_number' => ['required', 'string', 'max:32', 'regex:/^[\d\s\+\-\(\)]+$/'],
            'password' => ['required', 'string', Password::defaults()],
        ]);

        $phone = $phoneNormalizer->normalize($validated['phone_number']);
        $phoneNormalizer->assertValid($phone);

        if (User::query()->where('phone_number', $phone)->exists()) {
            throw ValidationException::withMessages([
                'phone_number' => [__('This phone number is already registered.')],
            ]);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $phone,
            'password' => $validated['password'],
            'role' => 'customer',
        ]);

        $registrationOtpService->issueAndQueueSignupSms($user);

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful. OTP sent to phone number.',
            'user' => $user->fresh(),
            'token' => $token,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'message' => 'Logged in successfully.',
            'token' => $token,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }
}
