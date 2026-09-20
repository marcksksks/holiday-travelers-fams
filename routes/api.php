<?php

use App\Http\Controllers\Api\AppointmentApiController;
use App\Http\Controllers\Api\ArchiveDocumentApiController;
use App\Http\Controllers\Api\ContractApiController;
use App\Http\Controllers\Api\FacilityApiController;
use App\Http\Controllers\Api\LegalRecordApiController;
use App\Http\Controllers\Api\MeApiController;
use App\Http\Controllers\Api\ReservationApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\VisitorApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| REST API (Sanctum token or SPA-cookie authenticated)
|--------------------------------------------------------------------------
| Every route below requires auth:sanctum. Role/permission checks happen
| inside each controller via Gate abilities defined in Rbac::PERMISSIONS.
*/
Route::middleware(['auth:sanctum', 'privileged.mfa'])->name('api.')->group(function () {
    Route::get('/me', [MeApiController::class, 'show'])->name('me');

    Route::apiResource('facilities', FacilityApiController::class);
    Route::patch('facilities/{facility}/restore', [FacilityApiController::class, 'restore'])->name('facilities.restore');

    Route::apiResource('reservations', ReservationApiController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
    Route::post('reservations/{reservation}/decide', [ReservationApiController::class, 'decide'])->name('reservations.decide');

    Route::apiResource('appointments', AppointmentApiController::class);

    Route::apiResource('visitors', VisitorApiController::class)->only(['index', 'show', 'store']);
    Route::post('visitors/{visitor}/check-in', [VisitorApiController::class, 'checkIn'])->name('visitors.check-in');
    Route::post('visitors/{visitor}/check-out', [VisitorApiController::class, 'checkOut'])->name('visitors.check-out');

    Route::apiResource('documents', ArchiveDocumentApiController::class)->only(['index', 'show', 'store']);
    Route::post('documents/{document}/request-link', [ArchiveDocumentApiController::class, 'requestLink'])->name('documents.request-link');

    Route::apiResource(
        'legal-records',
        LegalRecordApiController::class
    )
        ->only([
            'index',
            'show',
            'store',
            'update',
        ])
        ->parameters([
            'legal-records' => 'legal',
        ]);

    Route::apiResource('contracts', ContractApiController::class)->only(['index', 'show', 'store', 'update']);
    Route::post('contracts/{contract}/submit-review', [ContractApiController::class, 'submitForReview'])->name('contracts.submit-review');
    Route::post('contracts/{contract}/legal-review', [ContractApiController::class, 'legalReview'])->name('contracts.legal-review');
    Route::post('contracts/{contract}/decide', [ContractApiController::class, 'decide'])->name('contracts.decide');

    Route::apiResource('users', UserApiController::class)->only(['index', 'store']);
});
