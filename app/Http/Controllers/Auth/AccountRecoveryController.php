<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AccountRecoveryRequest;
use App\Services\AccountRecoveryService;
use Carbon\Carbon;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;
use JsonException;

class AccountRecoveryController extends Controller
{
    private const SESSION_KEY =
        'secure_account_recovery';

    private const PLACEHOLDER_LIFETIME_HOURS =
        24;

    public function __construct(
        private AccountRecoveryService $recovery
    ) {}

    public function requestForm(): View
    {
        return view(
            'auth.forgot-password'
        );
    }

    public function requestAdministrator(
        Request $request
    ): RedirectResponse {
        $data =
            $request->validate([
                'email' => [
                    'required',
                    'email',
                    'max:255',
                ],
            ]);

        $result =
            $this->recovery
                ->requestAdminRecovery(
                    $data['email'],
                    $request->ip(),
                    $request->userAgent()
                );

        /*
         * Store either a real encrypted claim or a synthetic
         * placeholder. Both produce the same public workflow so
         * the response does not disclose whether an account exists.
         */
        if ($result === null) {

            $this->storePlaceholder(
                $request
            );

        } else {

            $this->storeRecovery(
                $request,
                $result['request'],
                $result['claim_token']
            );
        }

        return redirect()
            ->route(
                'account-recovery.status'
            )
            ->with(
                'status',
                'Recovery request received. If the account is eligible, a System Administrator can review the request.'
            );
    }

    public function requestWithRecoveryCode(
        Request $request
    ): RedirectResponse {
        $data =
            $request->validate([
                'email' => [
                    'required',
                    'email',
                    'max:255',
                ],

                'recovery_code' => [
                    'required',
                    'string',
                    'max:255',
                ],
            ]);

        $result =
            $this->recovery
                ->requestWithRecoveryCode(
                    $data['email'],
                    $data['recovery_code'],
                    $request->ip(),
                    $request->userAgent()
                );

        if ($result === null) {

            return back()
                ->withInput(
                    $request->only(
                        'email'
                    )
                )
                ->withErrors([
                    'recovery_code' => 'The recovery information could not be verified.',
                ]);
        }

        $this->storeRecovery(
            $request,
            $result['request'],
            $result['claim_token']
        );

        return redirect()
            ->route(
                'account-recovery.reset'
            )
            ->with(
                'status',
                'Recovery code verified. Create a new password to continue.'
            );
    }

    public function status(
        Request $request
    ): View|RedirectResponse {
        $claim =
            $this->readClaim(
                $request
            );

        if ($claim === null) {

            return redirect()
                ->route(
                    'password.request'
                )
                ->withErrors([
                    'recovery' => 'Start a new account recovery request.',
                ]);
        }

        if (
            ($claim['placeholder'] ?? false)
            === true
        ) {

            $requestedAt =
                Carbon::parse(
                    $claim['requested_at']
                );

            $expired =
                $requestedAt
                    ->copy()
                    ->addHours(
                        self::PLACEHOLDER_LIFETIME_HOURS
                    )
                    ->isPast();

            return view(
                'auth.account-recovery-status',
                [
                    'reference' => $claim['reference'],

                    'recoveryStatus' => $expired
                            ? AccountRecoveryRequest::STATUS_EXPIRED
                            : AccountRecoveryRequest::STATUS_PENDING,

                    'canReset' => false,

                    'recoveryMethod' => AccountRecoveryRequest::METHOD_ADMIN,
                ]
            );
        }

        $recovery =
            $this->resolveRecovery(
                $request,
                $claim
            );

        if ($recovery === null) {

            return redirect()
                ->route(
                    'password.request'
                )
                ->withErrors([
                    'recovery' => 'The recovery request is no longer available. Start a new request.',
                ]);
        }

        $status =
            $recovery->status;

        if (
            $recovery->hasExpired()
            &&
            in_array(
                $status,
                [
                    AccountRecoveryRequest::STATUS_PENDING,
                    AccountRecoveryRequest::STATUS_APPROVED,
                ],
                true
            )
        ) {
            $status =
                AccountRecoveryRequest::STATUS_EXPIRED;
        }

        return view(
            'auth.account-recovery-status',
            [
                'reference' => $recovery->reference,

                'recoveryStatus' => $status,

                'canReset' => $recovery->canReset(),

                'recoveryMethod' => $recovery->recovery_method,
            ]
        );
    }

    public function resetForm(
        Request $request
    ): View|RedirectResponse {
        $claim =
            $this->readClaim(
                $request
            );

        if (
            $claim === null
            ||
            ($claim['placeholder'] ?? false)
            === true
        ) {

            return redirect()
                ->route(
                    'account-recovery.status'
                )
                ->withErrors([
                    'recovery' => 'This recovery request has not been authorized.',
                ]);
        }

        $recovery =
            $this->resolveRecovery(
                $request,
                $claim
            );

        if (
            $recovery === null
            ||
            ! $recovery->canReset()
        ) {

            return redirect()
                ->route(
                    'account-recovery.status'
                )
                ->withErrors([
                    'recovery' => 'This recovery authorization is invalid or has expired.',
                ]);
        }

        return view(
            'auth.account-recovery-reset',
            [
                'reference' => $recovery->reference,

                'recoveryMethod' => $recovery->recovery_method,
            ]
        );
    }

    public function reset(
        Request $request
    ): RedirectResponse {
        $data =
            $request->validate([
                'password' => [
                    'required',
                    'confirmed',
                    PasswordRule::defaults(),
                ],
            ]);

        $claim =
            $this->readClaim(
                $request
            );

        if (
            $claim === null
            ||
            ($claim['placeholder'] ?? false)
            === true
        ) {

            return redirect()
                ->route(
                    'password.request'
                )
                ->withErrors([
                    'recovery' => 'The recovery authorization is invalid or has expired.',
                ]);
        }

        $recovery =
            $this->resolveRecovery(
                $request,
                $claim
            );

        if ($recovery === null) {

            return redirect()
                ->route(
                    'password.request'
                )
                ->withErrors([
                    'recovery' => 'The recovery authorization is invalid or has expired.',
                ]);
        }

        $this->recovery
            ->complete(
                $recovery,
                $claim['claim_token'],
                $data['password']
            );

        $request
            ->session()
            ->forget(
                self::SESSION_KEY
            );

        return redirect()
            ->route(
                'login'
            )
            ->with(
                'status',
                'Password recovered successfully. Sign in with your new password.'
            );
    }

    private function storeRecovery(
        Request $request,
        AccountRecoveryRequest $recovery,
        string $claimToken
    ): void {
        $this->storeClaim(
            $request,
            [
                'placeholder' => false,

                'request_id' => $recovery->id,

                'reference' => $recovery->reference,

                'claim_token' => $claimToken,

                'requested_at' => now()->toIso8601String(),
            ]
        );
    }

    private function storePlaceholder(
        Request $request
    ): void {
        $this->storeClaim(
            $request,
            [
                'placeholder' => true,

                'reference' => 'REC-'.
                    now()->format('Ymd').
                    '-'.
                    Str::upper(
                        Str::random(8)
                    ),

                'requested_at' => now()->toIso8601String(),
            ]
        );
    }

    private function storeClaim(
        Request $request,
        array $payload
    ): void {
        try {

            $json =
                json_encode(
                    $payload,
                    JSON_THROW_ON_ERROR
                );

        } catch (JsonException) {

            abort(
                500,
                'Recovery session could not be prepared.'
            );
        }

        $request
            ->session()
            ->put(
                self::SESSION_KEY,
                Crypt::encryptString(
                    $json
                )
            );
    }

    private function readClaim(
        Request $request
    ): ?array {
        $encrypted =
            $request
                ->session()
                ->get(
                    self::SESSION_KEY
                );

        if (
            ! is_string($encrypted)
            ||
            $encrypted === ''
        ) {
            return null;
        }

        try {

            $payload =
                json_decode(
                    Crypt::decryptString(
                        $encrypted
                    ),
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                );

        } catch (
            DecryptException|
            JsonException
        ) {

            $request
                ->session()
                ->forget(
                    self::SESSION_KEY
                );

            return null;
        }

        return is_array($payload)
            ? $payload
            : null;
    }

    private function resolveRecovery(
        Request $request,
        array $claim
    ): ?AccountRecoveryRequest {
        $requestId =
            $claim['request_id']
            ?? null;

        $claimToken =
            $claim['claim_token']
            ?? null;

        if (
            ! is_numeric($requestId)
            ||
            ! is_string($claimToken)
            ||
            $claimToken === ''
        ) {
            return null;
        }

        $recovery =
            AccountRecoveryRequest::query()
                ->find(
                    (int) $requestId
                );

        if ($recovery === null) {
            return null;
        }

        if (
            ! $this->recovery
                ->verifyClaimToken(
                    $recovery,
                    $claimToken
                )
        ) {

            $request
                ->session()
                ->forget(
                    self::SESSION_KEY
                );

            return null;
        }

        return $recovery;
    }
}
