<?php

namespace App\Jobs;

use App\Models\Sms;
use App\Services\Sms\SmsDeliveryService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendSmsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Retry a few times for transient provider or network errors.
     */
    public int $tries = 3;

    public int $timeout = 60;

    /**
     * @var array<int, int>
     */
    public array $backoff = [10, 30, 60];

    public function __construct(
        public Sms $sms,
    ) {}

    public function handle(SmsDeliveryService $delivery): void
    {
        $this->sms->refresh();

        Log::info('SMS job started', [
            'sms_id' => $this->sms->id,
            'status' => $this->sms->status,
            'attempt' => $this->attempts(),
        ]);

        if ($this->sms->status === Sms::STATUS_SENT) {
            Log::info('SMS job skipped because message is already sent', [
                'sms_id' => $this->sms->id,
            ]);

            return;
        }

        if (! in_array($this->sms->status, [Sms::STATUS_PENDING, Sms::STATUS_QUEUED, Sms::STATUS_SENDING], true)) {
            Log::warning('SMS job skipped due to unsupported status', [
                'sms_id' => $this->sms->id,
                'status' => $this->sms->status,
            ]);

            return;
        }

        if ($this->sms->status !== Sms::STATUS_SENDING) {
            $this->sms->update(['status' => Sms::STATUS_SENDING]);

            Log::info('SMS status updated to sending', [
                'sms_id' => $this->sms->id,
            ]);
        } else {
            Log::warning('SMS retry started while status is already sending', [
                'sms_id' => $this->sms->id,
                'attempt' => $this->attempts(),
            ]);
        }

        try {
            $delivery->deliver($this->sms);

            $this->sms->update(['status' => Sms::STATUS_SENT]);

            Log::info('SMS delivered successfully', [
                'sms_id' => $this->sms->id,
                'status' => $this->sms->status,
            ]);
        } catch (Throwable $e) {
            Log::warning('SMS send attempt failed (may retry)', [
                'sms_id' => $this->sms->id,
                'status' => $this->sms->status,
                'attempt' => $this->attempts(),
                'exception' => $e->getMessage(),
            ]);

            report($e);

            throw $e;
        }
    }

    public function failed(?Throwable $exception): void
    {
        $this->sms->refresh();

        if ($this->sms->status === Sms::STATUS_SENT) {
            return;
        }

        $this->sms->update(['status' => Sms::STATUS_FAILED]);

        Log::error('SMS job failed after all attempts', [
            'sms_id' => $this->sms->id,
            'status' => $this->sms->status,
            'exception' => $exception?->getMessage(),
        ]);
    }
}
