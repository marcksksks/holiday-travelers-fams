<?php

use App\Http\Controllers\AdminAccountRecoveryController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Auth\AccountRecoveryController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\FacilityImportController;
use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\LegalRecordController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PrivacyRequestController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportExportController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RetentionController;
use App\Http\Controllers\RetentionPolicyController;
use App\Http\Controllers\SettingsController;
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

    // Secure Account Recovery.
    Route::get(
        '/forgot-password',
        [AccountRecoveryController::class, 'requestForm']
    )->name('password.request');

    Route::post(
        '/account-recovery/request',
        [AccountRecoveryController::class, 'requestAdministrator']
    )
        ->middleware('throttle:5,10')
        ->name('account-recovery.request');

    Route::post(
        '/account-recovery/recovery-code',
        [AccountRecoveryController::class, 'requestWithRecoveryCode']
    )
        ->middleware('throttle:8,10')
        ->name('account-recovery.recovery-code');

    Route::get(
        '/account-recovery/status',
        [AccountRecoveryController::class, 'status']
    )->name('account-recovery.status');

    Route::get(
        '/account-recovery/reset',
        [AccountRecoveryController::class, 'resetForm']
    )->name('account-recovery.reset');

    Route::post(
        '/account-recovery/reset',
        [AccountRecoveryController::class, 'reset']
    )
        ->middleware('throttle:10,10')
        ->name('account-recovery.update');

    /*
     * Legacy email reset endpoints remain available internally
     * for compatibility and existing security regression tests.
     * They are no longer exposed by the Forgot Password UI.
     */
    Route::post(
        '/forgot-password/email',
        [PasswordResetController::class, 'sendResetLink']
    )
        ->middleware('throttle:password-reset-link')
        ->name('password.email');

    Route::get(
        '/reset-password/{token}',
        [PasswordResetController::class, 'resetForm']
    )->name('password.reset');

    Route::post(
        '/reset-password',
        [PasswordResetController::class, 'reset']
    )->name('password.update');
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

    // Role-aware global header search.
    Route::get('/global-search', [GlobalSearchController::class, 'index'])
        ->middleware('throttle:120,1')
        ->name('global-search.index');

    Route::get('/change-password', [ChangePasswordController::class, 'edit'])->name('password.change');
    Route::put('/change-password', [ChangePasswordController::class, 'update'])->name('password.change.update');

    // Personal account settings - available to every authenticated user.
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');

    Route::post(
        '/settings/privacy-requests',
        [SettingsController::class, 'submitPrivacyRequest']
    )
        ->middleware('throttle:3,1')
        ->name('settings.privacy-requests.store');

    // Personal MFA management.
    Route::post('/settings/mfa/setup', [SettingsController::class, 'beginMfaSetup'])
        ->middleware('throttle:6,1')
        ->name('settings.mfa.setup');

    Route::post('/settings/mfa/confirm', [SettingsController::class, 'confirmMfa'])
        ->middleware('throttle:10,1')
        ->name('settings.mfa.confirm');

    Route::post('/settings/mfa/recovery-codes', [SettingsController::class, 'regenerateMfaRecoveryCodes'])
        ->middleware('throttle:6,1')
        ->name('settings.mfa.recovery.regenerate');

    Route::delete('/settings/mfa', [SettingsController::class, 'disableMfa'])
        ->middleware('throttle:6,1')
        ->name('settings.mfa.disable');

    // Facilities — everyone can browse; create/edit/archive gated by 'manageFacilities'.
    Route::get('/facilities', [FacilityController::class, 'index'])->name('facilities.index');

    Route::middleware(
        'role:admin_officer,sys_admin'
    )->group(function () {
        Route::get(
            '/facilities/import',
            [FacilityImportController::class, 'index']
        )->name('facilities.import.index');

        Route::post(
            '/facilities/import',
            [FacilityImportController::class, 'store']
        )
            ->middleware('throttle:10,1')
            ->name('facilities.import.store');

        Route::get(
            '/facilities/import/template',
            [FacilityImportController::class, 'template']
        )->name('facilities.import.template');
    });
    Route::get('/facilities/create', [FacilityController::class, 'create'])->name('facilities.create');
    Route::post('/facilities', [FacilityController::class, 'store'])->name('facilities.store');
    Route::get('/facilities/{facility}/edit', [FacilityController::class, 'edit'])->name('facilities.edit');
    Route::put('/facilities/{facility}', [FacilityController::class, 'update'])->name('facilities.update');
    Route::delete('/facilities/{facility}', [FacilityController::class, 'destroy'])->name('facilities.destroy');
    Route::patch('/facilities/{facility}/restore', [FacilityController::class, 'restore'])->name('facilities.restore');

    // Reservations — submit + admin/manager decision.
    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
    Route::put('/reservations/{reservation}', [ReservationController::class, 'update'])->name('reservations.resubmit');
    Route::post('/reservations/{reservation}/decide', [ReservationController::class, 'decide'])->name('reservations.decide');
    Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.cancel');

    // Appointments — everyone in the operational navigation may view; mutations are restricted.
    Route::middleware('role:employee,receptionist,admin_officer,manager,sys_admin')->get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::middleware('role:receptionist,admin_officer,manager,sys_admin')->group(function () {
        Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
        Route::put('/appointments/{appointment}', [AppointmentController::class, 'update'])->name('appointments.update');
        Route::patch('/appointments/{appointment}/status', [AppointmentController::class, 'status'])->name('appointments.status');
        Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('appointments.cancel');
    });

    // Visitor records — viewing is separate from operating the reception desk.
    Route::middleware('role:employee,receptionist,admin_officer,manager,sys_admin')->get('/visitors', [VisitorController::class, 'index'])->name('visitors.index');
    Route::middleware('role:receptionist,admin_officer,sys_admin')->group(function () {
        Route::post('/visitors', [VisitorController::class, 'store'])->name('visitors.store');
        Route::post('/visitors/{visitor}/check-in', [VisitorController::class, 'checkIn'])->name('visitors.check-in');
        Route::post('/visitors/{visitor}/check-out', [VisitorController::class, 'checkOut'])->name('visitors.check-out');
        Route::post('/visitors/{visitor}/decline', [VisitorController::class, 'decline'])->name('visitors.decline');
        Route::post('/visitors/{visitor}/sync-calendar', [VisitorController::class, 'syncCalendar'])
            ->middleware('throttle:calendar-sync')
            ->name('visitors.sync-calendar');
    });

    Route::middleware('role:receptionist,admin_officer,manager,sys_admin')
        ->post('/visitors/ai-assist', [VisitorController::class, 'aiAssist'])
        ->middleware('throttle:ai-assist')
        ->name('visitors.ai-assist');

    // Records & Archive
    Route::middleware('role:admin_officer,manager,legal_officer,sys_admin')->group(function () {
        Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
        Route::post('/documents/bulk-action', [DocumentController::class, 'bulkAction'])->name('documents.bulk-action');
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
        Route::get('/legal/{legal}/edit', [LegalRecordController::class, 'edit'])->name('legal.edit');
        Route::post('/legal', [LegalRecordController::class, 'store'])->name('legal.store');
        Route::put('/legal/{legal}', [LegalRecordController::class, 'update'])->name('legal.update');
        Route::post('/legal/{legal}/review', [LegalRecordController::class, 'review'])->name('legal.review');

        Route::get('/contracts', [ContractController::class, 'index'])->name('contracts.index');
        Route::get('/contracts/{contract}/edit', [ContractController::class, 'edit'])->name('contracts.edit');
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
    Route::middleware(
        'role:admin_officer,manager,sys_admin'
    )->group(function () {
        Route::get(
            '/reports',
            [ReportController::class, 'index']
        )->name('reports.index');

        Route::get(
            '/reports/export/pdf',
            [ReportExportController::class, 'pdf']
        )->name('reports.export.pdf');

        Route::get(
            '/reports/export/xlsx',
            [ReportExportController::class, 'xlsx']
        )->name('reports.export.xlsx');

        Route::get(
            '/reports/export/csv',
            [ReportExportController::class, 'csv']
        )->name('reports.export.csv');

        Route::get(
            '/reports/print',
            [ReportExportController::class, 'print']
        )->name('reports.print');
    });

    // Privacy request governance.
    Route::middleware(
        'role:manager,sys_admin'
    )
        ->prefix('privacy-requests')
        ->name('privacy-requests.')
        ->group(function () {
            Route::get(
                '/',
                [
                    PrivacyRequestController::class,
                    'index',
                ]
            )->name('index');

            Route::post(
                '/{privacyRequest}/start-review',
                [
                    PrivacyRequestController::class,
                    'startReview',
                ]
            )
                ->middleware('throttle:20,1')
                ->name('start-review');

            Route::post(
                '/{privacyRequest}/decision',
                [
                    PrivacyRequestController::class,
                    'decision',
                ]
            )
                ->middleware('throttle:20,1')
                ->name('decision');

            Route::post(
                '/{privacyRequest}/execute',
                [
                    PrivacyRequestController::class,
                    'execute',
                ]
            )
                ->middleware('throttle:5,1')
                ->name('execute');
        });

    // Audit trail
    Route::middleware('role:manager,sys_admin')
        ->get('/audit-trail', [AuditLogController::class, 'index'])->name('audit-trail.index');

    // User management (sys_admin only)
    Route::middleware('role:sys_admin')->group(function () {
        Route::get(
            '/account-recovery-requests',
            [AdminAccountRecoveryController::class, 'index']
        )->name('account-recovery.admin.index');

        Route::post(
            '/account-recovery-requests/{recovery}/approve',
            [AdminAccountRecoveryController::class, 'approve']
        )
            ->middleware('throttle:20,1')
            ->name('account-recovery.admin.approve');

        Route::post(
            '/account-recovery-requests/{recovery}/reject',
            [AdminAccountRecoveryController::class, 'reject']
        )
            ->middleware('throttle:20,1')
            ->name('account-recovery.admin.reject');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::post('/users/{user}/role', [UserController::class, 'setRole'])->name('users.set-role');
        Route::post('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
    });

    // In-app notifications (bell dropdown)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    Route::get(
        '/notifications/{notification}/open',
        [NotificationController::class, 'open']
    )->name('notifications.open');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
});
