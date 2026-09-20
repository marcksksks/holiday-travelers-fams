<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laragear\TwoFactor\Facades\Auth2FA;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->has('2fa_code')) {
            return [
                '2fa_code' => [
                    'required',
                    'string',
                    'min:6',
                    'max:64',
                ],
            ];
        }

        return [
            'email' => [
                'required',
                'email',
            ],
            'password' => [
                'required',
                'string',
            ],
        ];
    }

    public function authenticate(): void
    {
        $isTwoFactorAttempt =
            $this->has('2fa_code');

        /*
         * A second-factor submission is only valid after Laragear has
         * stored the encrypted first-factor credentials in the session.
         *
         * Never allow a standalone 2FA code to trigger an authentication
         * attempt without the original email/password challenge.
         */
        if (
            $isTwoFactorAttempt
            && ! $this->session()->has(
                config(
                    'two-factor.login.key',
                    '_2fa_login'
                )
            )
        ) {
            throw ValidationException::withMessages([
                '2fa_code' =>
                    'Your two-factor login session has expired. Please sign in again.',
            ]);
        }

        if ($isTwoFactorAttempt) {
            $this->ensureTwoFactorIsNotRateLimited();
        } else {
            $this->ensureIsNotRateLimited();
        }

        /*
         * Never accept replacement email/password values during the
         * second-factor request. Laragear securely restores the original
         * credentials from the encrypted login session.
         */
        $credentials =
            $isTwoFactorAttempt
                ? []
                : $this->only(
                    'email',
                    'password'
                );

        /*
         * Preserve the application's existing inactive-account protection.
         */
        $credentials['is_active'] = true;

        try {
            $authenticated = Auth2FA::attempt(
                $credentials,
                $this->boolean('remember')
            );
        } catch (HttpResponseException $exception) {
            /*
             * A missing MFA code after valid credentials causes Laragear
             * to display its challenge form. At this point the password
             * was correct, so clear previous password failures.
             *
             * If a 2FA code was submitted and rejected, count it against
             * a separate second-factor limiter.
             */
            if ($isTwoFactorAttempt) {
                RateLimiter::hit(
                    $this->twoFactorThrottleKey()
                );
            } else {
                RateLimiter::clear(
                    $this->throttleKey()
                );
            }

            throw $exception;
        }

        if (! $authenticated) {
            if ($isTwoFactorAttempt) {
                RateLimiter::hit(
                    $this->twoFactorThrottleKey()
                );

                throw ValidationException::withMessages([
                    'email' =>
                        'Your two-factor login session has expired. Please sign in again.',
                ]);
            }

            RateLimiter::hit(
                $this->throttleKey()
            );

            throw ValidationException::withMessages([
                'email' =>
                    'These credentials do not match our records.',
            ]);
        }

        if ($isTwoFactorAttempt) {
            RateLimiter::clear(
                $this->twoFactorThrottleKey()
            );
        } else {
            RateLimiter::clear(
                $this->throttleKey()
            );
        }
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts(
            $this->throttleKey(),
            5
        )) {
            return;
        }

        $seconds = RateLimiter::availableIn(
            $this->throttleKey()
        );

        throw ValidationException::withMessages([
            'email' =>
                "Too many login attempts. Please try again in {$seconds} seconds.",
        ]);
    }

    public function ensureTwoFactorIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts(
            $this->twoFactorThrottleKey(),
            5
        )) {
            return;
        }

        $seconds = RateLimiter::availableIn(
            $this->twoFactorThrottleKey()
        );

        /*
         * Force a fresh password authentication after excessive
         * second-factor failures instead of retaining the challenge.
         */
        $this->session()->forget(
            config(
                'two-factor.login.key',
                '_2fa_login'
            )
        );

        throw ValidationException::withMessages([
            '2fa_code' =>
                "Too many two-factor authentication attempts. Please sign in again in {$seconds} seconds.",
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(
            Str::lower(
                $this->string('email')
            ).'|'.$this->ip()
        );
    }

    public function twoFactorThrottleKey(): string
    {
        return 'two-factor-login|'.
            $this->session()->getId().
            '|'.$this->ip();
    }
}
