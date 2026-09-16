<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $rows = DB::table('institutions')
            ->whereIn('slug', ['mci-test-serise', 'mci-test-series'])
            ->orderByRaw('logo IS NULL')
            ->orderBy('id')
            ->get();

        $primary = $rows->first();
        if (! $primary) {
            return;
        }

        DB::table('institutions')->where('id', $primary->id)->update([
            'name' => 'MCI Test Series',
            'website_url' => 'https://test.mciedu.com',
            'short_description' => 'Bilingual online tests, practice, results and competitive-exam preparation.',
            'description' => 'Bilingual online tests, practice, results and competitive-exam preparation.',
            'display_order' => 3,
            'is_active' => true,
            'updated_at' => now(),
        ]);

        DB::table('institutions')
            ->whereIn('slug', ['mci-test-serise', 'mci-test-series'])
            ->where('id', '!=', $primary->id)
            ->update(['is_active' => false, 'updated_at' => now()]);
    }

    public function down(): void
    {
        // Do not reactivate duplicate public cards during rollback.
    }
};
