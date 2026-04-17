<?php

use App\Models\Sms;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('sms')->where('status', 'queue')->update(['status' => Sms::STATUS_QUEUED]);
        DB::table('sms')->where('status', 'processing')->update(['status' => Sms::STATUS_SENDING]);
        DB::table('sms')->where('status', 'delivered')->update(['status' => Sms::STATUS_SENT]);
        DB::table('sms')->where('status', 'error')->update(['status' => Sms::STATUS_FAILED]);

        DB::table('sms')
            ->whereNotIn('status', Sms::validStatuses())
            ->update(['status' => Sms::STATUS_PENDING]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration normalizes data and is intentionally irreversible.
    }
};
