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
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [MeApiController::class, 'show']);

    Route::apiResource('facilities', FacilityApiController::class);

    Route::apiResource('reservations', ReservationApiController::class)->only(['index', 'show', 'store', 'destroy']);
    Route::post('reservations/{reservation}/decide', [ReservationApiController::class, 'decide']);

    Route::apiResource('appointments', AppointmentApiController::class);

    Route::apiResource('visitors', VisitorApiController::class)->only(['index', 'show', 'store']);
    Route::post('visitors/{visitor}/check-in', [VisitorApiController::class, 'checkIn']);
    Route::post('visitors/{visitor}/check-out', [VisitorApiController::class, 'checkOut']);

    Route::apiResource('documents', ArchiveDocumentApiController::class)->only(['index', 'show', 'store']);
    Route::post('documents/{document}/request-link', [ArchiveDocumentApiController::class, 'requestLink']);

    Route::apiResource('legal-records', LegalRecordApiController::class)->parameters(['legal-records' => 'legal']);

    Route::apiResource('contracts', ContractApiController::class)->only(['index', 'show', 'store', 'update']);
    Route::post('contracts/{contract}/submit-review', [ContractApiController::class, 'submitForReview']);
    Route::post('contracts/{contract}/legal-review', [ContractApiController::class, 'legalReview']);
    Route::post('contracts/{contract}/decide', [ContractApiController::class, 'decide']);

    Route::apiResource('users', UserApiController::class)->only(['index', 'store']);
});
