<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendance_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained('institutions')->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('device_code', 80)->unique();
            $table->string('serial_number', 120)->nullable()->index();
            $table->string('location')->nullable();
            $table->string('token_hash', 64)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->dateTime('last_seen_at')->nullable()->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['institution_id', 'is_active']);
        });

        Schema::create('attendance_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained('institutions')->cascadeOnDelete();
            $table->uuid('attendance_code')->unique();
            $table->string('admission_number', 100)->nullable();
            $table->string('roll_number', 100)->nullable();
            $table->string('name', 150);
            $table->string('course_class', 150)->nullable()->index();
            $table->string('batch_section', 100)->nullable()->index();
            $table->string('photo_path')->nullable();
            $table->string('mobile', 30)->nullable();
            $table->string('status', 30)->default('active')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['institution_id', 'admission_number'], 'attendance_students_institution_admission_unique');
            $table->index(['institution_id', 'status', 'name']);
        });

        Schema::create('iris_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_student_id')->constrained('attendance_students')->cascadeOnDelete();
            $table->string('eye', 20)->default('unknown');
            $table->longText('template_data');
            $table->string('template_hash', 64)->index();
            $table->decimal('quality_score', 8, 3)->nullable();
            $table->string('sdk_version', 80)->nullable();
            $table->foreignId('enrolled_device_id')->nullable()->constrained('attendance_devices')->nullOnDelete();
            $table->dateTime('enrolled_at');
            $table->dateTime('revoked_at')->nullable()->index();
            $table->timestamps();
            $table->unique(['attendance_student_id', 'eye'], 'iris_templates_student_eye_unique');
        });

        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained('institutions')->cascadeOnDelete();
            $table->foreignId('attendance_student_id')->constrained('attendance_students')->cascadeOnDelete();
            $table->foreignId('attendance_device_id')->nullable()->constrained('attendance_devices')->nullOnDelete();
            $table->uuid('event_uuid')->unique();
            $table->date('attendance_date')->index();
            $table->string('session_key', 40)->default('daily');
            $table->dateTime('captured_at')->index();
            $table->dateTime('received_at')->index();
            $table->string('method', 20)->default('iris');
            $table->string('status', 30)->default('present')->index();
            $table->decimal('match_score', 10, 4)->nullable();
            $table->decimal('quality_score', 8, 3)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(
                ['institution_id', 'attendance_student_id', 'attendance_date', 'session_key'],
                'attendance_records_student_day_session_unique'
            );
            $table->index(['institution_id', 'attendance_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
        Schema::dropIfExists('iris_templates');
        Schema::dropIfExists('attendance_students');
        Schema::dropIfExists('attendance_devices');
    }
};
