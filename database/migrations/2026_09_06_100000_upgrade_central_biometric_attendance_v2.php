<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_branches', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('institution_id')
                ->constrained('institutions')
                ->cascadeOnDelete();

            $table->string('name', 150);
            $table->string('code', 80);
            $table->string('address')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(
                ['institution_id', 'code'],
                'attendance_branch_institution_code_unique'
            );
        });

        Schema::table('attendance_students', function (Blueprint $table): void {
            $table->foreignId('attendance_branch_id')
                ->nullable()
                ->after('institution_id')
                ->constrained('attendance_branches')
                ->nullOnDelete();

            $table->string('person_type', 30)
                ->default('student')
                ->after('attendance_code')
                ->index();

            $table->string('employee_number', 100)
                ->nullable()
                ->after('roll_number');

            $table->index(
                ['institution_id', 'attendance_branch_id', 'person_type', 'status'],
                'attendance_people_scope_idx'
            );
        });

        Schema::table('attendance_devices', function (Blueprint $table): void {
            $table->foreignId('attendance_branch_id')
                ->nullable()
                ->after('institution_id')
                ->constrained('attendance_branches')
                ->nullOnDelete();

            $table->index(
                ['institution_id', 'attendance_branch_id', 'is_active'],
                'attendance_device_branch_active_idx'
            );
        });

        Schema::table('attendance_records', function (Blueprint $table): void {
            $table->foreignId('attendance_branch_id')
                ->nullable()
                ->after('institution_id')
                ->constrained('attendance_branches')
                ->nullOnDelete();

            $table->dateTime('checked_in_at')
                ->nullable()
                ->after('captured_at');

            $table->dateTime('checked_out_at')
                ->nullable()
                ->after('checked_in_at');

            $table->string('checkout_source', 40)
                ->nullable()
                ->after('checked_out_at');

            $table->unsignedInteger('minutes_completed')
                ->default(0)
                ->after('checkout_source');

            $table->index(
                ['institution_id', 'attendance_branch_id', 'attendance_date'],
                'attendance_record_branch_date_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::table('attendance_records', function (Blueprint $table): void {
            $table->dropIndex('attendance_record_branch_date_idx');
            $table->dropConstrainedForeignId('attendance_branch_id');
            $table->dropColumn([
                'checked_in_at',
                'checked_out_at',
                'checkout_source',
                'minutes_completed',
            ]);
        });

        Schema::table('attendance_devices', function (Blueprint $table): void {
            $table->dropIndex('attendance_device_branch_active_idx');
            $table->dropConstrainedForeignId('attendance_branch_id');
        });

        Schema::table('attendance_students', function (Blueprint $table): void {
            $table->dropIndex('attendance_people_scope_idx');
            $table->dropConstrainedForeignId('attendance_branch_id');
            $table->dropColumn([
                'person_type',
                'employee_number',
            ]);
        });

        Schema::dropIfExists('attendance_branches');
    }
};
