<?php

namespace App\Services\Sms;

use App\Jobs\SendSmsJob;
use App\Models\Sms;

/**
 * Queues outbound SMS. Controllers call this instead of invoking providers or jobs directly.
 */
class SmsSendDispatcher
{
    public function dispatch(Sms $sms): void
    {
        if ($sms->status === 'sent') {
            return;
        }

        $sms->update(['status' => 'queued']);

        SendSmsJob::dispatch($sms)->afterCommit();
    }
}
