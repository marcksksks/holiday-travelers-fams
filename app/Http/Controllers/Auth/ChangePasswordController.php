<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Services\CredentialRevocationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    public function __construct(
        private CredentialRevocationService $credentials
    ) {}

    public function edit()
    {
        $forced = (bool) request()
            ->user()
            ->force_password_change;

        return view(
            'auth.change-password',
            compact('forced')
        );
    }

    public function update(
        ChangePasswordRequest $request
    ) {
        $user = $request->user();

        $currentSessionId =
            $request->session()->getId();

        DB::transaction(
            function () use (
                $request,
                $user,
                $currentSessionId
            ): void {
                $user->forceFill([
                    'password' => Hash::make(
                        $request->string('password')
                    ),
                    'force_password_change' =>
                        false,
                ])->save();

                $this->credentials
                    ->revokeOtherSessions(
                        $user,
                        $currentSessionId
                    );
            }
        );

        $request
            ->session()
            ->regenerate();

        return redirect()
            ->route('dashboard')
            ->with(
                'status',
                'Password updated.'
            );
    }
}