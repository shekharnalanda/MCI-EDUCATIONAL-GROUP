<?php

use App\Http\Controllers\Api\CentralEnquirySyncController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('throttle:60,1')->group(function () {
    Route::post('/enquiries', [CentralEnquirySyncController::class, 'store'])->name('api.v1.enquiries.store');
    Route::get('/health', fn () => response()->json(['ok' => true, 'service' => 'mci-central-api']));
});
