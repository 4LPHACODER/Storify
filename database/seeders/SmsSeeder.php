<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SmsSeeder extends Seeder
{
    /**
     * Seed realistic SMS rows for API and UI testing (GET list, PUT updates).
     */
    public function run(): void
    {
        $user = User::query()->firstOrCreate(
            ['email' => 'sms-demo@example.com'],
            [
                'name' => 'SMS Demo Customer',
                'username' => 'sms_demo',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'email_verified_at' => now(),
            ],
        );

        $user->sms()->delete();

        $now = now();

        $rows = [
            [
                'phone_number' => '09567042788',
                'message' => 'Storify: Your order #48291 has shipped via Express. Track: storify.example/track/48291',
                'status' => 'sent',
                'created_at' => $now->copy()->subDays(5)->subHours(2),
                'updated_at' => $now->copy()->subDays(5),
            ],
            [
                'phone_number' => '09567042788',
                'message' => 'Reminder: Your subscription renews tomorrow ($9.99). Reply HELP for support.',
                'status' => 'pending',
                'created_at' => $now->copy()->subDays(4)->subMinutes(12),
                'updated_at' => $now->copy()->subDays(4)->subMinutes(12),
            ],
            [
                'phone_number' => '09567042788',
                'message' => 'Your verification code is 847291. Do not share this code. Valid for 10 minutes.',
                'status' => 'sent',
                'created_at' => $now->copy()->subDays(3)->subHours(8),
                'updated_at' => $now->copy()->subDays(3)->subHours(8)->addMinutes(1),
            ],
            [
                'phone_number' => '09567042788',
                'message' => 'Hi Alex — your click-and-collect order is ready at Southbank. Pick up by 6pm today.',
                'status' => 'failed',
                'created_at' => $now->copy()->subDays(3),
                'updated_at' => $now->copy()->subDays(2)->subHours(18),
            ],
            [
                'phone_number' => '09567042788',
                'message' => 'Promo: 15% off winter jackets this weekend. Code WINTER15. Opt out: STOP',
                'status' => 'pending',
                'created_at' => $now->copy()->subDays(2)->subHours(4),
                'updated_at' => $now->copy()->subDays(2)->subHours(4),
            ],
            [
                'phone_number' => '09567042788',
                'message' => 'Payment received ¥4,980 for invoice INV-2026-044. Thank you for your business.',
                'status' => 'sent',
                'created_at' => $now->copy()->subDays(2),
                'updated_at' => $now->copy()->subDays(2)->addMinutes(3),
            ],
            [
                'phone_number' => '09567042788',
                'message' => 'Delivery update: your package is delayed due to weather. New ETA: Friday.',
                'status' => 'failed',
                'created_at' => $now->copy()->subDay()->subHours(6),
                'updated_at' => $now->copy()->subDay()->subHours(5),
            ],
            [
                'phone_number' => '09567042788',
                'message' => 'Your return label is ready. Print: storify.example/returns/R-88312',
                'status' => 'queued',
                'created_at' => $now->copy()->subDay()->subHours(1),
                'updated_at' => $now->copy()->subDay()->subHours(1),
            ],
            [
                'phone_number' => '09567042788',
                'message' => 'Security alert: new login to your Storify account from Chrome on Windows. Not you? Reset password.',
                'status' => 'sent',
                'created_at' => $now->copy()->subHours(20),
                'updated_at' => $now->copy()->subHours(19)->subMinutes(45),
            ],
            [
                'phone_number' => '09567042788',
                'message' => 'Flash sale ends in 2 hours — up to 40% off electronics. Shop now: storify.example/sale',
                'status' => 'pending',
                'created_at' => $now->copy()->subHours(14),
                'updated_at' => $now->copy()->subHours(14),
            ],
            [
                'phone_number' => '09567042788',
                'message' => 'Tu pedido #7721 está en camino. Llegada estimada: mañana antes de las 3 PM.',
                'status' => 'sending',
                'created_at' => $now->copy()->subHours(10),
                'updated_at' => $now->copy()->subHours(10),
            ],
            [
                'phone_number' => '09567042788',
                'message' => 'Appointment confirmed: Tech support callback today 4:30 PM SGT. We will call this number.',
                'status' => 'sent',
                'created_at' => $now->copy()->subHours(6),
                'updated_at' => $now->copy()->subHours(6)->addMinutes(2),
            ],
            [
                'phone_number' => '09567042788',
                'message' => 'Refund of $42.18 has been initiated to your original payment method. Allow 5–7 business days.',
                'status' => 'failed',
                'created_at' => $now->copy()->subHours(3),
                'updated_at' => $now->copy()->subHours(2),
            ],
            [
                'phone_number' => '09567042788',
                'message' => 'Low stock alert: “Ceramic Mug — Slate” is back in stock. 12 units left.',
                'status' => 'pending',
                'created_at' => $now->copy()->subHour(),
                'updated_at' => $now->copy()->subHour(),
            ],
        ];

        foreach ($rows as $row) {
            $user->sms()->create($row);
        }

        if ($this->command !== null) {
            $this->command->info('Seeded '.count($rows).' SMS records for '.$user->email.' (password: password).');
        }
    }
}
