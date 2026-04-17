<?php

namespace App\Http\Requests\Api;

use App\Models\Sms;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSmsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'phone_number' => ['sometimes', 'string', 'max:32', 'regex:/^\+?[0-9]{7,20}$/'],
            'message' => ['sometimes', 'string', 'max:2000'],
            'status' => ['sometimes', 'string', Rule::in(Sms::validStatuses())],
        ];
    }

    public function after(): array
    {
        return [
            function ($validator): void {
                if ($this->safe()->only(['phone_number', 'message', 'status']) === []) {
                    $validator->errors()->add('payload', 'Provide at least one updatable field.');
                }
            },
        ];
    }
}
