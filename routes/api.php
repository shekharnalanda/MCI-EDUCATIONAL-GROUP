<?php

use App\Http\Controllers\Api\CentralEnquirySyncController;
use App\Http\Controllers\Api\IrisAttendanceController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('throttle:60,1')->group(function () {
    Route::post('/enquiries', [CentralEnquirySyncController::class, 'store'])->name('api.v1.enquiries.store');
    Route::post('/admissions', [CentralEnquirySyncController::class, 'admission'])->name('api.v1.admissions.store');
    Route::get('/health', fn () => response()->json(['ok' => true, 'service' => 'mci-central-api']));

    Route::prefix('iris')->middleware('throttle:180,1')->group(function () {
        Route::post('/heartbeat', [IrisAttendanceController::class, 'heartbeat'])->name('api.v1.iris.heartbeat');
        Route::get('/roster', [IrisAttendanceController::class, 'roster'])->name('api.v1.iris.roster');
        Route::post('/enrollments', [IrisAttendanceController::class, 'enroll'])->name('api.v1.iris.enrollments.store');
        Route::post('/attendance', [IrisAttendanceController::class, 'mark'])->name('api.v1.iris.attendance.store');
    });
});
