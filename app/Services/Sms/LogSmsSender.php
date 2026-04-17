<?php

namespace App\Services\Sms;

use App\Contracts\SmsSenderContract;
use Illuminate\Support\Facades\Log;

/**
 * Development-oriented sender that logs payloads. Swap the binding in AppServiceProvider
 * for a real provider (Twilio, Vonage, etc.) without changing jobs or controllers.
 */
class LogSmsSender implements SmsSenderContract
{
    public function send(string $phoneNumber, string $message): void
    {
        Log::info('SMS (stub)', [
            'to' => $phoneNumber,
            'body' => $message,
        ]);
    }
}
