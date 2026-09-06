<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_devices', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance_devices','activation_code_hash')) {
                $table->string('activation_code_hash',64)
                    ->nullable()->after('token_hash');
            }

            if (!Schema::hasColumn('attendance_devices','activation_expires_at')) {
                $table->timestamp('activation_expires_at')
                    ->nullable()->after('activation_code_hash');
            }

            if (!Schema::hasColumn('attendance_devices','activated_at')) {
                $table->timestamp('activated_at')
                    ->nullable()->after('activation_expires_at');
            }

            if (!Schema::hasColumn('attendance_devices','installation_id')) {
                $table->string('installation_id',100)
                    ->nullable()->index()->after('activated_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendance_devices', function (Blueprint $table) {
            foreach ([
                'installation_id',
                'activated_at',
                'activation_expires_at',
                'activation_code_hash',
            ] as $column) {
                if (Schema::hasColumn('attendance_devices',$column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
