<?php

namespace App\Services\Phone;

use Illuminate\Validation\ValidationException;

class PhilippineMobileNormalizer
{
    public function normalize(string $value): string
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

    public function assertValid(string $e164): void
    {
        if (! preg_match('/^\+639\d{9}$/', $e164)) {
            throw ValidationException::withMessages([
                'phone_number' => ['Enter a valid Philippine mobile number (e.g. 0917 123 4567 or +639171234567).'],
            ]);
        }
    }
}
