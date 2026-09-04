<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Schema;

Artisan::command('mci:status', function () {
    $this->info('MCI Educational Group application is ready.');
})->purpose('Check the MCI application console');

Artisan::command('mci:central-audit', function () {
    $checks = [];
    foreach (['institutions','users','customers','enquiries','reply_templates','auto_reply_rules','communication_logs','follow_ups','audit_logs','central_admissions'] as $table) {
        $checks['table:'.$table] = Schema::hasTable($table);
    }
    foreach (['api.v1.enquiries.store','api.v1.admissions.store','admin.enquiries.index','admin.admissions.index','admin.auto-replies.index','admin.users.index','admin.reports.index','admin.audit.index'] as $route) {
        $checks['route:'.$route] = Route::has($route);
    }
    $checks['mail:from'] = (bool) config('mail.from.address');
    $failed = 0;
    foreach ($checks as $name => $ok) {
        $ok ? $this->info('PASS '.$name) : $this->error('FAIL '.$name);
        if (!$ok) $failed++;
    }
    $this->newLine();
    $this->line('CENTRAL AUDIT: '.(count($checks)-$failed).' PASS, '.$failed.' FAIL');
    return $failed ? 1 : 0;
})->purpose('Audit MCI Central Master Admin production readiness');

Artisan::command('mci:monitor {--notify : Send an email only when a problem is detected}', function () {
    $checks = [];

    foreach (['institutions','enquiries','communication_logs','follow_ups','central_admissions'] as $table) {
        $checks['table:'.$table] = Schema::hasTable($table);
    }

    $expectedBusinesses = [
        'Micro Computer Institute',
        'C-Net Computer Institute',
        'C-Net Pathshala',
        'C-Net Library',
        'C-Net Web Services',
        'C-Net Store',
    ];

    if (Schema::hasTable('institutions')) {
        foreach ($expectedBusinesses as $business) {
            $row = DB::table('institutions')->where('name', $business)->first();
            $checks['business:'.$business] = (bool) ($row
                && (bool) $row->sync_enabled
                && !empty($row->api_token_hash)
                && (bool) $row->auto_reply_enabled);
        }
    }

    $failedCommunications = 0;
    if (Schema::hasTable('communication_logs')) {
        $failedCommunications = DB::table('communication_logs')
            ->where('delivery_status', 'failed')
            ->where('created_at', '>=', now()->subDay())
            ->count();
        $checks['communications:last24h_failed=0'] = $failedCommunications === 0;
    }

    $failedSyncs = 0;
    if (Schema::hasTable('enquiries') && Schema::hasColumn('enquiries', 'sync_status')) {
        $failedSyncs = DB::table('enquiries')
            ->whereIn('sync_status', ['failed', 'error'])
            ->where('created_at', '>=', now()->subDay())
            ->count();
        $checks['enquiries:last24h_sync_failed=0'] = $failedSyncs === 0;
    }

    $overdueFollowUps = 0;
    if (Schema::hasTable('follow_ups')) {
        $overdueFollowUps = DB::table('follow_ups')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->where('scheduled_at', '<', now())
            ->count();
        $checks['followups:overdue=0'] = $overdueFollowUps === 0;
    }

    $checks['mail:configured'] = (bool) config('mail.from.address') && (bool) config('mail.default');
    $checks['route:admin.login'] = Route::has('admin.login');
    $checks['route:api.enquiries'] = Route::has('api.v1.enquiries.store');
    $checks['route:api.admissions'] = Route::has('api.v1.admissions.store');

    $failed = [];
    foreach ($checks as $name => $ok) {
        if ($ok) {
            $this->info('PASS '.$name);
        } else {
            $this->error('FAIL '.$name);
            $failed[] = $name;
        }
    }

    $this->newLine();
    $this->line('MONITOR SUMMARY: '.(count($checks)-count($failed)).' PASS, '.count($failed).' FAIL');

    if ($failed && $this->option('notify')) {
        $recipient = config('mail.from.address');
        try {
            Mail::raw(
                "MCI Central production monitor detected problems.\n\n".
                "Failed checks:\n- ".implode("\n- ", $failed)."\n\n".
                "Failed communications (24h): {$failedCommunications}\n".
                "Failed enquiry syncs (24h): {$failedSyncs}\n".
                "Overdue follow-ups: {$overdueFollowUps}\n".
                "Checked at: ".now()->toDateTimeString(),
                function ($message) use ($recipient) {
                    $message->to($recipient)->subject('MCI Central Monitor Alert');
                }
            );
            $this->warn('ALERT_EMAIL=SENT');
        } catch (Throwable $e) {
            $this->error('ALERT_EMAIL=FAILED: '.$e->getMessage());
        }
    }

    return $failed ? 1 : 0;
})->purpose('Monitor Central Master health, communication delivery, sync failures and overdue follow-ups');
