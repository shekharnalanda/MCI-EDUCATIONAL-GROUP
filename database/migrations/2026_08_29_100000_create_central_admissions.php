<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('central_admissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->nullable()->constrained('institutions')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('enquiry_id')->nullable()->constrained('enquiries')->nullOnDelete();
            $table->string('source_site')->nullable();
            $table->string('source_reference_id')->nullable();
            $table->string('application_reference')->nullable()->index();
            $table->string('applicant_name');
            $table->string('phone', 30)->nullable()->index();
            $table->string('email')->nullable()->index();
            $table->string('course_program')->nullable()->index();
            $table->string('status', 40)->default('new')->index();
            $table->string('payment_status', 40)->nullable()->index();
            $table->json('metadata')->nullable();
            $table->timestamp('submitted_at')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['institution_id','source_reference_id'], 'admissions_business_source_ref_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('central_admissions');
    }
};
