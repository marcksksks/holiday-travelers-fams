<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\CredentialRevocationService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRule;

class PasswordResetController extends Controller
{
    public function __construct(
        private CredentialRevocationService $credentials
    ) {}

    public function requestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => [
                'required',
                'email',
            ],
        ]);

        Password::sendResetLink(
            $request->only('email')
        );

        return back()->with(
            'status',
            'If an account exists for that email address, a password reset link has been sent.'
        );
    }

    public function resetForm(
        Request $request,
        string $token
    ) {
        return view(
            'auth.reset-password',
            [
                'token' => $token,
                'email' => $request->email,
            ]
        );
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => [
                'required',
            ],
            'email' => [
                'required',
                'email',
            ],
            'password' => [
                'required',
                'confirmed',
                PasswordRule::defaults(),
            ],
        ]);

        $status = Password::reset(
            $request->only(
                'email',
                'password',
                'password_confirmation',
                'token'
            ),
            function (
                $user,
                $password
            ): void {
                DB::transaction(
                    function () use (
                        $user,
                        $password
                    ): void {
                        $user->forceFill([
                            'password' => Hash::make(
                                $password
                            ),
                            'force_password_change' => false,
                        ])->save();

                        $this->credentials
                            ->revokeAll($user);
                    }
                );

                event(
                    new PasswordReset($user)
                );
            }
        );

        return $status ===
            Password::PASSWORD_RESET
                ? redirect()
                    ->route('login')
                    ->with(
                        'status',
                        __($status)
                    )
                : back()
                    ->withErrors([
                        'email' => [
                            __($status),
                        ],
                    ]);
    }
}
