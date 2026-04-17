<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Services\Phone\PhilippineMobileNormalizer;
use App\Services\Sms\SmsSendDispatcher;
use Illuminate\Support\Facades\Hash;

class RegistrationOtpService
{
    public function __construct(
        private PhilippineMobileNormalizer $normalizer,
        private SmsSendDispatcher $smsSendDispatcher,
    ) {}

    /**
     * Generate OTP, persist hash + expiry, create SMS row, queue send job.
     */
    public function issueAndQueueSignupSms(User $user): void
    {
        $plain = str_pad((string) random_int(0, 999_999), 6, '0', STR_PAD_LEFT);

        $user->forceFill([
            'otp_code_hash' => Hash::make($plain),
            'otp_expires_at' => now()->addMinutes(15),
        ])->save();

        $phone = $this->normalizer->normalize((string) $user->phone_number);
        $this->normalizer->assertValid($phone);

        $message = "Your Storify verification code is: {$plain}. Do not share this code. It expires in 15 minutes.";

        $sms = $user->sms()->create([
            'phone_number' => $phone,
            'message' => $message,
            'status' => 'pending',
        ]);

        $this->smsSendDispatcher->dispatch($sms);
    }

    public function resend(User $user): void
    {
        if ($user->phone_verified_at) {
            return;
        }

        if ($user->phone_number === null) {
            return;
        }

        $this->issueAndQueueSignupSms($user);
    }

    public function verify(User $user, string $code): bool
    {
        if ($user->phone_verified_at) {
            return true;
        }

        if ($user->otp_code_hash === null || $user->otp_expires_at === null) {
            return false;
        }

        if ($user->otp_expires_at->isPast()) {
            return false;
        }

        if (! Hash::check($code, $user->otp_code_hash)) {
            return false;
        }

        $user->forceFill([
            'phone_verified_at' => now(),
            'otp_code_hash' => null,
            'otp_expires_at' => null,
        ])->save();

        return true;
    }
}
