<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->string('sender_name')->nullable()->after('website_url');
            $table->string('sender_email')->nullable()->after('sender_name');
            $table->string('reply_to_email')->nullable()->after('sender_email');
            $table->string('phone', 30)->nullable()->after('reply_to_email');
            $table->boolean('auto_reply_enabled')->default(false)->after('is_active');
            $table->boolean('sync_enabled')->default(true)->after('auto_reply_enabled');
            $table->string('api_token_hash', 64)->nullable()->after('sync_enabled');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 40)->default('master_admin')->after('password');
            $table->foreignId('institution_id')->nullable()->after('role')->constrained('institutions')->nullOnDelete();
            $table->boolean('is_active')->default(true)->after('institution_id');
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('mobile', 30)->nullable()->index();
            $table->string('email')->nullable()->index();
            $table->string('preferred_language', 20)->nullable();
            $table->foreignId('first_institution_id')->nullable()->constrained('institutions')->nullOnDelete();
            $table->string('status', 30)->default('active')->index();
            $table->timestamp('last_activity_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::table('enquiries', function (Blueprint $table) {
            $table->foreignId('institution_id')->nullable()->after('id')->constrained('institutions')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->after('institution_id')->constrained('customers')->nullOnDelete();
            $table->string('source_site')->nullable()->after('status');
            $table->string('source_reference_id')->nullable()->after('source_site');
            $table->string('category', 80)->nullable()->after('source_reference_id')->index();
            $table->string('course_service')->nullable()->after('category');
            $table->string('priority', 20)->default('normal')->after('course_service')->index();
            $table->string('auto_reply_status', 30)->default('pending')->after('priority')->index();
            $table->foreignId('assigned_user_id')->nullable()->after('auto_reply_status')->constrained('users')->nullOnDelete();
            $table->timestamp('received_at')->nullable()->after('assigned_user_id')->index();
            $table->timestamp('last_replied_at')->nullable()->after('received_at');
            $table->timestamp('next_follow_up_at')->nullable()->after('last_replied_at')->index();
            $table->string('sync_status', 30)->default('local')->after('next_follow_up_at')->index();
            $table->softDeletes();
            $table->index(['institution_id', 'status', 'created_at'], 'enquiries_business_status_created_idx');
            $table->unique(['institution_id', 'source_reference_id'], 'enquiries_business_source_ref_unique');
        });

        Schema::create('reply_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->nullable()->constrained('institutions')->cascadeOnDelete();
            $table->string('name');
            $table->string('category', 80)->index();
            $table->string('language', 20)->default('en');
            $table->string('subject')->nullable();
            $table->longText('body');
            $table->json('placeholders')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->string('status', 20)->default('draft')->index();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('auto_reply_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->nullable()->constrained('institutions')->cascadeOnDelete();
            $table->foreignId('reply_template_id')->nullable()->constrained('reply_templates')->nullOnDelete();
            $table->string('name');
            $table->string('category', 80)->index();
            $table->json('keywords')->nullable();
            $table->json('conditions')->nullable();
            $table->unsignedInteger('priority')->default(100)->index();
            $table->boolean('auto_send')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('fallback_action', 40)->default('manual_review');
            $table->timestamps();
        });

        Schema::create('communication_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enquiry_id')->nullable()->constrained('enquiries')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('institution_id')->nullable()->constrained('institutions')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('auto_reply_rule_id')->nullable()->constrained('auto_reply_rules')->nullOnDelete();
            $table->string('channel', 20)->default('email')->index();
            $table->string('direction', 20)->default('outgoing')->index();
            $table->string('reply_type', 20)->default('manual')->index();
            $table->string('subject')->nullable();
            $table->longText('message_body');
            $table->string('sender')->nullable();
            $table->string('recipient')->nullable();
            $table->string('delivery_status', 30)->default('pending')->index();
            $table->string('provider_reference')->nullable();
            $table->text('failed_reason')->nullable();
            $table->timestamp('sent_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enquiry_id')->constrained('enquiries')->cascadeOnDelete();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('scheduled_at')->index();
            $table->timestamp('completed_at')->nullable();
            $table->string('status', 30)->default('pending')->index();
            $table->text('note')->nullable();
            $table->string('outcome')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('institution_id')->nullable()->constrained('institutions')->nullOnDelete();
            $table->string('action', 80)->index();
            $table->string('auditable_type')->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->index(['auditable_type', 'auditable_id'], 'audit_logs_auditable_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('follow_ups');
        Schema::dropIfExists('communication_logs');
        Schema::dropIfExists('auto_reply_rules');
        Schema::dropIfExists('reply_templates');

        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropUnique('enquiries_business_source_ref_unique');
            $table->dropIndex('enquiries_business_status_created_idx');
            $table->dropConstrainedForeignId('assigned_user_id');
            $table->dropConstrainedForeignId('customer_id');
            $table->dropConstrainedForeignId('institution_id');
            $table->dropColumn(['source_site','source_reference_id','category','course_service','priority','auto_reply_status','received_at','last_replied_at','next_follow_up_at','sync_status','deleted_at']);
        });

        Schema::dropIfExists('customers');

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('institution_id');
            $table->dropColumn(['role', 'is_active']);
        });

        Schema::table('institutions', function (Blueprint $table) {
            $table->dropColumn(['sender_name','sender_email','reply_to_email','phone','auto_reply_enabled','sync_enabled','api_token_hash']);
        });
    }
};
