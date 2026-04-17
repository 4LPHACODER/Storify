<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreSmsRequest;
use App\Http\Requests\Api\UpdateSmsRequest;
use App\Models\Sms;
use App\Services\Phone\PhilippineMobileNormalizer;
use App\Services\Sms\SmsSendDispatcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class SmsController extends Controller
{
    public function __construct(
        protected SmsSendDispatcher $smsSendDispatcher,
        protected PhilippineMobileNormalizer $phoneNormalizer,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['sometimes', 'string', Rule::in(Sms::validStatuses())],
        ]);

        $messages = $request->user()
            ->sms()
            ->latest()
            ->when(
                $validated['status'] ?? null,
                fn ($query, $status) => $query->where('status', $status),
            )
            ->get();

        return response()->json($messages);
    }

    public function poll(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['sometimes', 'string', Rule::in(Sms::validStatuses())],
        ]);

        $status = $validated['status'] ?? Sms::STATUS_QUEUED;

        $messages = $request->user()
            ->sms()
            ->where('status', $status)
            ->latest()
            ->get();

        return response()->json([
            'status' => $status,
            'count' => $messages->count(),
            'data' => $messages,
        ]);
    }

    public function show(Sms $sms): JsonResponse
    {
        return response()->json($sms);
    }

    public function store(StoreSmsRequest $request): JsonResponse
    {
        $phone = $this->phoneNormalizer->normalize($request->validated('phone_number'));
        $this->phoneNormalizer->assertValid($phone);

        $sms = $request->user()->sms()->create([
            'phone_number' => $phone,
            'message' => $request->validated('message'),
            'status' => Sms::STATUS_PENDING,
        ]);

        Log::info('SMS record created', [
            'sms_id' => $sms->id,
            'user_id' => $request->user()->id,
            'status' => $sms->status,
        ]);

        $this->smsSendDispatcher->dispatch($sms);

        return response()->json($sms->fresh(), 201);
    }

    public function update(UpdateSmsRequest $request, Sms $sms): JsonResponse
    {
        $payload = $request->validated();

        if (isset($payload['phone_number'])) {
            $normalizedPhone = $this->phoneNormalizer->normalize($payload['phone_number']);
            $this->phoneNormalizer->assertValid($normalizedPhone);
            $payload['phone_number'] = $normalizedPhone;
        }

        $sms->update($payload);

        Log::info('SMS record updated via API', [
            'sms_id' => $sms->id,
            'user_id' => $request->user()?->id,
            'status' => $sms->status,
        ]);

        return response()->json($sms->fresh());
    }
}
