<?php

namespace App\Services\Sms;

use App\Jobs\SendSmsJob;
use App\Models\Sms;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Queues outbound SMS. Controllers call this instead of invoking providers or jobs directly.
 */
class SmsSendDispatcher
{
    public function dispatch(Sms $sms): bool
    {
        $dispatched = false;

        DB::transaction(function () use ($sms, &$dispatched): void {
            $locked = Sms::query()->whereKey($sms->id)->lockForUpdate()->first();

            if (! $locked) {
                return;
            }

            if (! in_array($locked->status, [Sms::STATUS_PENDING, Sms::STATUS_FAILED], true)) {
                Log::info('SMS dispatch skipped due to current status', [
                    'sms_id' => $locked->id,
                    'status' => $locked->status,
                ]);

                return;
            }

            SendSmsJob::dispatch($locked)->afterCommit();
            $locked->update(['status' => Sms::STATUS_QUEUED]);

            Log::info('SMS queued for delivery', [
                'sms_id' => $locked->id,
                'status' => $locked->status,
            ]);

            $dispatched = true;
        });

        return $dispatched;
    }
}
