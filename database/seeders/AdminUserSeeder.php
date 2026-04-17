<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@storify.com'],
            [
                'name' => 'Admin',
                'phone_number' => '+639123456700',
                'password' => Hash::make('AdminStrongPass#8472'),
                'email_verified_at' => now(),
            ]
        );

        // optional: clear old tokens first
        $admin->tokens()->delete();

        $token = $admin->createToken('admin-token')->plainTextToken;

        $this->command->info('Admin token: ' . $token);
    }
}