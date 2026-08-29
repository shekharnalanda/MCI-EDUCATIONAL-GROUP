<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
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
