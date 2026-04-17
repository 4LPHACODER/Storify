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

        if ($this->sms->status === 'sent') {
            return;
        }

        $this->sms->update(['status' => 'sending']);

        try {
            $delivery->deliver($this->sms);
            $this->sms->update(['status' => 'sent']);
        } catch (Throwable $e) {
            Log::warning('SMS send attempt failed (may retry)', [
                'sms_id' => $this->sms->id,
                'exception' => $e->getMessage(),
            ]);

            report($e);

            throw $e;
        }
    }

    public function failed(?Throwable $exception): void
    {
        $this->sms->refresh();

        if ($this->sms->status === 'sent') {
            return;
        }

        $this->sms->update(['status' => 'failed']);

        Log::error('SMS job failed after all attempts', [
            'sms_id' => $this->sms->id,
            'exception' => $exception?->getMessage(),
        ]);
    }
}
