<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreSmsRequest;
use App\Models\Sms;
use App\Services\Sms\SmsSendDispatcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SmsController extends Controller
{
    public function __construct(
        protected SmsSendDispatcher $smsSendDispatcher,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $messages = $request->user()
            ->sms()
            ->whereIn('status', ['pending', 'queued', 'sending', 'failed'])
            ->latest()
            ->get();

        return response()->json($messages);
    }

    public function store(StoreSmsRequest $request): JsonResponse
    {
        $phone = $this->normalizePhilippineMobileNumber($request->validated('phone_number'));
        $this->assertPhilippineMobile($phone);

        $sms = $request->user()->sms()->create([
            'phone_number' => $phone,
            'message' => $request->validated('message'),
            'status' => 'pending',
        ]);

        $this->smsSendDispatcher->dispatch($sms);

        return response()->json($sms->fresh(), 201);
    }

    public function update(Sms $sms): JsonResponse
    {
        $this->smsSendDispatcher->dispatch($sms);

        return response()->json($sms->fresh());
    }

    /**
     * Normalize input to Philippine mobile E.164: +639XXXXXXXXX (10 digits after +63, starting with 9).
     * Accepts common forms: 09XXXXXXXXX, 9XXXXXXXXX, 639XXXXXXXXX, +63 9XX XXX XXXX, etc.
     */
    private function normalizePhilippineMobileNumber(string $value): string
    {
        $digits = preg_replace('/\D+/', '', $value) ?? '';

        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '63')) {
            return '+'.$digits;
        }

        if (str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        if (strlen($digits) === 10 && str_starts_with($digits, '9')) {
            return '+63'.$digits;
        }

        return '+'.$digits;
    }

    private function assertPhilippineMobile(string $e164): void
    {
        if (! preg_match('/^\+639\d{9}$/', $e164)) {
            throw ValidationException::withMessages([
                'phone_number' => ['Enter a valid Philippine mobile number (e.g. 0917 123 4567 or +639171234567).'],
            ]);
        }
    }
}
