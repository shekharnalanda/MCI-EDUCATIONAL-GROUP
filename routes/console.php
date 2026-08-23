<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('mci:status', function () {
    $this->info('MCI Educational Group application is ready.');
})->purpose('Check the MCI application console');
