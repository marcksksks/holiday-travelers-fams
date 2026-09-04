<?php

use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\LegalRecordController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\RetentionController;
use App\Http\Controllers\RetentionPolicyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VisitorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);

    Route::get('/forgot-password', [PasswordResetController::class, 'requestForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'resetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated routes — same page set as src/App.jsx's <Routes>
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/change-password', [ChangePasswordController::class, 'edit'])->name('password.change');
    Route::put('/change-password', [ChangePasswordController::class, 'update'])->name('password.change.update');

    // Personal account settings - available to every authenticated user.
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');

    // Facilities — everyone can browse; create/edit/archive gated by 'manageFacilities'.
    Route::get('/facilities', [FacilityController::class, 'index'])->name('facilities.index');
    Route::get('/facilities/create', [FacilityController::class, 'create'])->name('facilities.create');
    Route::post('/facilities', [FacilityController::class, 'store'])->name('facilities.store');
    Route::get('/facilities/{facility}/edit', [FacilityController::class, 'edit'])->name('facilities.edit');
    Route::put('/facilities/{facility}', [FacilityController::class, 'update'])->name('facilities.update');
    Route::delete('/facilities/{facility}', [FacilityController::class, 'destroy'])->name('facilities.destroy');

    // Reservations — submit + admin/manager decision.
    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
    Route::post('/reservations/{reservation}/decide', [ReservationController::class, 'decide'])->name('reservations.decide');
    Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.cancel');

    // Appointments — everyone in the operational navigation may view; mutations are restricted.
    Route::middleware('role:employee,receptionist,admin_officer,manager,sys_admin')->get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::middleware('role:receptionist,admin_officer,manager,sys_admin')->group(function () {
        Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
        Route::put('/appointments/{appointment}', [AppointmentController::class, 'update'])->name('appointments.update');
        Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('appointments.cancel');
    });

    // Visitor records — viewing is separate from operating the reception desk.
    Route::middleware('role:employee,receptionist,admin_officer,manager,sys_admin')->get('/visitors', [VisitorController::class, 'index'])->name('visitors.index');
    Route::middleware('role:receptionist,admin_officer,sys_admin')->group(function () {
        Route::post('/visitors', [VisitorController::class, 'store'])->name('visitors.store');
        Route::post('/visitors/{visitor}/check-in', [VisitorController::class, 'checkIn'])->name('visitors.check-in');
        Route::post('/visitors/{visitor}/check-out', [VisitorController::class, 'checkOut'])->name('visitors.check-out');
        Route::post('/visitors/{visitor}/decline', [VisitorController::class, 'decline'])->name('visitors.decline');
        Route::post('/visitors/{visitor}/sync-calendar', [VisitorController::class, 'syncCalendar'])->name('visitors.sync-calendar');
        Route::post('/visitors/ai-assist', [VisitorController::class, 'aiAssist'])->name('visitors.ai-assist');
    });

    // Records & Archive
    Route::middleware('role:admin_officer,manager,legal_officer,sys_admin')->group(function () {
        Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
        Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('documents.show');
        Route::get('/documents/{document}/edit', [DocumentController::class, 'edit'])->name('documents.edit');
        Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
        Route::put('/documents/{document}', [DocumentController::class, 'update'])->name('documents.update');
        Route::post('/documents/{document}/move', [DocumentController::class, 'move'])->name('documents.move');
        Route::post('/documents/{document}/version', [DocumentController::class, 'uploadVersion'])->name('documents.version');
        Route::post('/documents/{document}/archive', [DocumentController::class, 'archive'])->name('documents.archive');
        Route::post('/documents/{document}/restore', [DocumentController::class, 'restore'])->name('documents.restore');
        Route::post('/documents/{document}/request-link', [DocumentController::class, 'requestLink'])->name('documents.request-link');

        Route::get('/legal', [LegalRecordController::class, 'index'])->name('legal.index');
        Route::post('/legal', [LegalRecordController::class, 'store'])->name('legal.store');
        Route::put('/legal/{legal}', [LegalRecordController::class, 'update'])->name('legal.update');
        Route::post('/legal/{legal}/review', [LegalRecordController::class, 'review'])->name('legal.review');

        Route::get('/contracts', [ContractController::class, 'index'])->name('contracts.index');
        Route::post('/contracts', [ContractController::class, 'store'])->name('contracts.store');
        Route::put('/contracts/{contract}', [ContractController::class, 'update'])->name('contracts.update');
        Route::post('/contracts/{contract}/submit-review', [ContractController::class, 'submitForReview'])->name('contracts.submit-review');
        Route::post('/contracts/{contract}/legal-review', [ContractController::class, 'legalReview'])->name('contracts.legal-review');
        Route::post('/contracts/{contract}/decide', [ContractController::class, 'decide'])->name('contracts.decide');
        Route::post('/contracts/{contract}/renew', [ContractController::class, 'renew'])->name('contracts.renew');

        Route::get('/retention', [RetentionController::class, 'index'])->name('retention.index');
        Route::post('/retention', [RetentionController::class, 'store'])->name('retention.store');
        Route::put('/retention/{retention}', [RetentionController::class, 'update'])->name('retention.update');

        Route::get(
            '/retention/{retention}/review',
            [RetentionController::class, 'review']
        )->name('retention.review');

        Route::post(
            '/retention/{retention}/decision',
            [RetentionController::class, 'decision']
        )->name('retention.decision');

        Route::get(
            '/retention/{retention}/disposition',
            [RetentionController::class, 'dispositionReview']
        )->name('retention.disposition');

        Route::post(
            '/retention/{retention}/disposition/approve',
            [RetentionController::class, 'approveDisposal']
        )->name('retention.disposition.approve');

        Route::post(
            '/retention/{retention}/disposition/reject',
            [RetentionController::class, 'rejectDisposal']
        )->name('retention.disposition.reject');

        Route::post(
            '/documents/{document}/retention',
            [RetentionController::class, 'assignDocument']
        )->name('documents.retention.assign');
        Route::post('/retention-policies', [RetentionPolicyController::class, 'store'])->name('retention-policies.store');
        Route::put('/retention-policies/{policy}', [RetentionPolicyController::class, 'update'])->name('retention-policies.update');
    });

    // Signed, private document downloads — validated inside the controller via hasValidSignature().
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');

    // Reports
    Route::middleware('role:admin_officer,manager,sys_admin')
        ->get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Audit trail
    Route::middleware('role:manager,sys_admin')
        ->get('/audit-trail', [AuditLogController::class, 'index'])->name('audit-trail.index');

    // User management (sys_admin only)
    Route::middleware('role:sys_admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::post('/users/{user}/role', [UserController::class, 'setRole'])->name('users.set-role');
        Route::post('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
    });

    // In-app notifications (bell dropdown)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
});
