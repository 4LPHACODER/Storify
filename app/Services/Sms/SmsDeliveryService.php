<?php

namespace App\Services\Sms;

use App\Contracts\SmsSenderContract;
use App\Models\Sms;

/**
 * Sends SMS payloads through the configured provider. Swap {@see SmsSenderContract}
 * in the container for Twilio, Vonage, etc.; this class stays unchanged.
 */
class SmsDeliveryService
{
    public function __construct(
        private SmsSenderContract $sender,
    ) {}

    public function deliver(Sms $sms): void
    {
        $this->sender->send($sms->phone_number, $sms->message);
    }
}
