<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('products as target')
            ->select('target.id', 'source.image', 'source.image_link')
            ->join('products as source', function ($join): void {
                $join->on('source.name', '=', 'target.name')
                    ->where('source.id', '<>', DB::raw('target.id'))
                    ->where(function ($query): void {
                        $query->whereNotNull('source.image')
                            ->where('source.image', '!=', '')
                            ->orWhere(function ($orQuery): void {
                                $orQuery->whereNotNull('source.image_link')
                                    ->where('source.image_link', '!=', '');
                            });
                    });
            })
            ->whereIn('target.name', [
                'Wireless Mouse Pro',
                'Gaming Keyboard X',
                '27 inch Monitor 2K',
                'USB-C Hub 8 in 1',
            ])
            ->where(function ($query): void {
                $query->whereNull('target.image')
                    ->orWhere('target.image', '');
            })
            ->where(function ($query): void {
                $query->whereNull('target.image_link')
                    ->orWhere('target.image_link', '');
            })
            ->orderBy('source.id')
            ->get()
            ->each(function ($row): void {
                DB::table('products')
                    ->where('id', $row->id)
                    ->update([
                        'image' => $row->image,
                        'image_link' => $row->image_link,
                        'updated_at' => now(),
                    ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Data backfill only; no destructive rollback to avoid removing valid image mappings.
    }
};
