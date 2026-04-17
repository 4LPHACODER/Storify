<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreSmsRequest;
use App\Models\Sms;
use App\Services\Phone\PhilippineMobileNormalizer;
use App\Services\Sms\SmsSendDispatcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    public function __construct(
        protected SmsSendDispatcher $smsSendDispatcher,
        protected PhilippineMobileNormalizer $phoneNormalizer,
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
        $phone = $this->phoneNormalizer->normalize($request->validated('phone_number'));
        $this->phoneNormalizer->assertValid($phone);

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
}
