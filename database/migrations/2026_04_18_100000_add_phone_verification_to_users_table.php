<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'phone_number')) {
                $table->string('phone_number', 32)->nullable()->unique()->after('email');
            }

            if (! Schema::hasColumn('users', 'otp_code_hash')) {
                $table->string('otp_code_hash')->nullable();
            }

            if (! Schema::hasColumn('users', 'otp_expires_at')) {
                $table->timestamp('otp_expires_at')->nullable();
            }

            if (! Schema::hasColumn('users', 'phone_verified_at')) {
                $table->timestamp('phone_verified_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $drop = array_filter([
                Schema::hasColumn('users', 'phone_verified_at') ? 'phone_verified_at' : null,
                Schema::hasColumn('users', 'otp_expires_at') ? 'otp_expires_at' : null,
                Schema::hasColumn('users', 'otp_code_hash') ? 'otp_code_hash' : null,
            ]);

            if ($drop !== []) {
                $table->dropColumn($drop);
            }
        });
    }
};
