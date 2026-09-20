<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditService;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use JsonException;

class SettingsController extends Controller
{
    private const MFA_SETUP_SESSION_KEY =
        'mfa_setup_authorized_at';

    private const MFA_SETUP_AUTHORIZATION_SECONDS =
        600;

    public function __construct(
        private AuditService $audit
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        $aiProvider =
            config(
                'services.ai_assist.provider',
                'none'
            );

        $aiModel =
            config(
                'services.ai_assist.model'
            );

        $aiConfigured =
            $aiProvider !== 'none'
            && filled(
                config(
                    'services.ai_assist.api_key'
                )
            )
            && filled(
                config(
                    'services.ai_assist.endpoint'
                )
            );

        $twoFactor =
            $user
                ->twoFactorAuth()
                ->first();

        $mfaEnabled =
            $user->hasTwoFactorEnabled();

        $mfaRequired =
            $user->requiresMandatoryMfa();

        $mfaSetupPending =
            $twoFactor !== null
            && ! $mfaEnabled;

        $mfaSetupAuthorized =
            $mfaSetupPending
            && $this->hasFreshMfaSetupAuthorization(
                $request
            );

        /*
         * Do not expose the shared secret after the short
         * password-confirmed setup window expires.
         */
        $mfaQrCode =
            $mfaSetupAuthorized
                ? $twoFactor->toQr()
                : null;

        $mfaSecret =
            $mfaSetupAuthorized
                ? $twoFactor->toString()
                : null;

        $mfaRecoveryCodes =
            $this->decryptRecoveryCodes(
                $request
                    ->session()
                    ->get(
                        'mfa_recovery_codes'
                    )
            );

        return view('settings.index', [
            'user' => $user,

            'aiProvider' => $aiProvider,

            'aiModel' => $aiModel,

            'aiConfigured' => $aiConfigured,

            'mfaEnabled' => $mfaEnabled,

            'mfaRequired' => $mfaRequired,

            'mfaSetupPending' => $mfaSetupPending,

            'mfaSetupAuthorized' => $mfaSetupAuthorized,

            'mfaQrCode' => $mfaQrCode,

            'mfaSecret' => $mfaSecret,

            'mfaRecoveryCodes' => $mfaRecoveryCodes,
        ]);
    }

    public function updateProfile(
        Request $request
    ): RedirectResponse {
        $user = $request->user();

        $data = $request->validate([
            'full_name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique(
                    'users',
                    'email'
                )->ignore(
                    $user->id
                ),
            ],

            'department' => [
                'nullable',
                'string',
                'max:255',
            ],

            'job_title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:50',
            ],
        ]);

        $user->update($data);

        $this->audit->log(
            $user,
            'update',
            'users',
            "User - {$user->full_name}",
            (string) $user->id,
            'Updated own profile settings'
        );

        return back()->with(
            'status',
            'Profile settings updated successfully.'
        );
    }

    public function beginMfaSetup(
        Request $request
    ): RedirectResponse {
        $request->validate([
            'current_password' => [
                'required',
                'current_password',
            ],
        ]);

        /** @var User $user */
        $user = $request->user();

        /*
         * Never call createTwoFactorAuth() on an already enabled
         * account because doing so intentionally rotates and
         * invalidates the existing MFA secret.
         */
        if ($user->hasTwoFactorEnabled()) {
            return back()->withErrors([
                'mfa' => 'Two-factor authentication is already enabled.',
            ]);
        }

        $user->createTwoFactorAuth();

        $request
            ->session()
            ->put(
                self::MFA_SETUP_SESSION_KEY,
                now()->timestamp
            );

        $this->audit->log(
            $user,
            'update',
            'users',
            "User - {$user->full_name}",
            (string) $user->id,
            'Started own two-factor authentication setup'
        );

        return redirect()
            ->route('settings.index')
            ->with(
                'status',
                'Two-factor authentication setup started. Scan the QR code and confirm the generated code.'
            );
    }

    public function confirmMfa(
        Request $request
    ): RedirectResponse {
        $digits =
            (int) config(
                'two-factor.totp.digits',
                6
            );

        $data =
            $request->validate([
                '2fa_code' => [
                    'required',
                    'digits:'.$digits,
                ],
            ]);

        /** @var User $user */
        $user = $request->user();

        if ($user->hasTwoFactorEnabled()) {
            $request
                ->session()
                ->forget(
                    self::MFA_SETUP_SESSION_KEY
                );

            return redirect()
                ->route('settings.index')
                ->with(
                    'status',
                    'Two-factor authentication is already enabled.'
                );
        }

        $twoFactor =
            $user
                ->twoFactorAuth()
                ->first();

        if ($twoFactor === null) {
            throw ValidationException::withMessages([
                '2fa_code' => 'Start two-factor authentication setup before confirming a code.',
            ]);
        }

        if (
            ! $this->hasFreshMfaSetupAuthorization(
                $request
            )
        ) {
            throw ValidationException::withMessages([
                '2fa_code' => 'Your MFA setup session has expired. Enter your current password and restart setup.',
            ]);
        }

        if (
            ! $user->confirmTwoFactorAuth(
                $data['2fa_code']
            )
        ) {
            throw ValidationException::withMessages([
                '2fa_code' => 'The authentication code is invalid or has expired.',
            ]);
        }

        $request
            ->session()
            ->forget(
                self::MFA_SETUP_SESSION_KEY
            );

        $this->flashRecoveryCodes(
            $request,
            $user
                ->getRecoveryCodes()
                ->pluck('code')
                ->values()
                ->all()
        );

        $this->audit->log(
            $user,
            'update',
            'users',
            "User - {$user->full_name}",
            (string) $user->id,
            'Enabled two-factor authentication'
        );

        return redirect()
            ->route('settings.index')
            ->with(
                'status',
                'Two-factor authentication enabled successfully. Save your recovery codes now.'
            );
    }

    public function regenerateMfaRecoveryCodes(
        Request $request
    ): RedirectResponse {
        $request->validate([
            'current_password' => [
                'required',
                'current_password',
            ],
        ]);

        /** @var User $user */
        $user = $request->user();

        if (! $user->hasTwoFactorEnabled()) {
            throw ValidationException::withMessages([
                'mfa' => 'Enable two-factor authentication before generating recovery codes.',
            ]);
        }

        $codes =
            $user
                ->generateRecoveryCodes()
                ->pluck('code')
                ->values()
                ->all();

        $this->flashRecoveryCodes(
            $request,
            $codes
        );

        $this->audit->log(
            $user,
            'update',
            'users',
            "User - {$user->full_name}",
            (string) $user->id,
            'Regenerated own MFA recovery codes'
        );

        return redirect()
            ->route('settings.index')
            ->with(
                'status',
                'New recovery codes generated. Previous recovery codes are no longer valid.'
            );
    }

    public function disableMfa(
        Request $request
    ): RedirectResponse {
        $request->validate([
            'current_password' => [
                'required',
                'current_password',
            ],
        ]);

        /** @var User $user */
        $user = $request->user();

        if ($user->requiresMandatoryMfa()) {
            throw ValidationException::withMessages([
                'mfa' => 'Two-factor authentication is required for your role and cannot be disabled.',
            ]);
        }

        $twoFactor =
            $user
                ->twoFactorAuth()
                ->first();

        if ($twoFactor === null) {
            $request
                ->session()
                ->forget([
                    self::MFA_SETUP_SESSION_KEY,
                    'mfa_recovery_codes',
                ]);

            return redirect()
                ->route('settings.index')
                ->with(
                    'status',
                    'Two-factor authentication is already disabled.'
                );
        }

        $wasEnabled =
            $user->hasTwoFactorEnabled();

        $user->disableTwoFactorAuth();

        $request
            ->session()
            ->forget([
                self::MFA_SETUP_SESSION_KEY,
                'mfa_recovery_codes',
            ]);

        $this->audit->log(
            $user,
            'update',
            'users',
            "User - {$user->full_name}",
            (string) $user->id,
            $wasEnabled
                ? 'Disabled own two-factor authentication'
                : 'Cancelled own two-factor authentication setup'
        );

        return redirect()
            ->route('settings.index')
            ->with(
                'status',
                $wasEnabled
                    ? 'Two-factor authentication disabled successfully.'
                    : 'Two-factor authentication setup cancelled.'
            );
    }

    private function hasFreshMfaSetupAuthorization(
        Request $request
    ): bool {
        $authorizedAt =
            $request
                ->session()
                ->get(
                    self::MFA_SETUP_SESSION_KEY
                );

        if (! is_numeric($authorizedAt)) {
            return false;
        }

        return (int) $authorizedAt
            >= now()
                ->subSeconds(
                    self::MFA_SETUP_AUTHORIZATION_SECONDS
                )
                ->timestamp;
    }

    private function flashRecoveryCodes(
        Request $request,
        array $codes
    ): void {
        try {
            $payload =
                json_encode(
                    array_values($codes),
                    JSON_THROW_ON_ERROR
                );
        } catch (JsonException) {
            throw ValidationException::withMessages([
                'mfa' => 'Recovery codes could not be prepared for display.',
            ]);
        }

        /*
         * Database-backed sessions should not contain recovery
         * codes in plaintext. Store the one-time display payload
         * encrypted inside the flash session.
         */
        $request
            ->session()
            ->flash(
                'mfa_recovery_codes',
                Crypt::encryptString(
                    $payload
                )
            );
    }

    private function decryptRecoveryCodes(
        mixed $payload
    ): array {
        if (
            ! is_string($payload)
            || $payload === ''
        ) {
            return [];
        }

        try {
            $decoded =
                json_decode(
                    Crypt::decryptString(
                        $payload
                    ),
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                );
        } catch (
            DecryptException|
            JsonException
        ) {
            return [];
        }

        if (! is_array($decoded)) {
            return [];
        }

        return array_values(
            array_filter(
                $decoded,
                static fn ($code): bool => is_string($code)
                    && $code !== ''
            )
        );
    }
}
