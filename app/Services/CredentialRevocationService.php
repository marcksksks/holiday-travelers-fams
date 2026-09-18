<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CredentialRevocationService
{
    public function revokeAll(User $user): void
    {
        DB::transaction(function () use ($user): void {
            $this->clearRememberToken($user);

            $this->deleteSessions(
                $user
            );

            $user
                ->tokens()
                ->delete();
        });
    }

    public function revokeOtherSessions(
        User $user,
        string $currentSessionId
    ): void {
        $currentSessionId =
            trim($currentSessionId);

        if ($currentSessionId === '') {
            throw new InvalidArgumentException(
                'Current session ID is required.'
            );
        }

        DB::transaction(
            function () use (
                $user,
                $currentSessionId
            ): void {
                $this->clearRememberToken(
                    $user
                );

                $this->deleteSessions(
                    $user,
                    $currentSessionId
                );

                $user
                    ->tokens()
                    ->delete();
            }
        );
    }

    private function clearRememberToken(
        User $user
    ): void {
        $tokenName =
            $user->getRememberTokenName();

        if ($tokenName === '') {
            return;
        }

        DB::table(
            $user->getTable()
        )
            ->where(
                $user->getKeyName(),
                $user->getKey()
            )
            ->update([
                $tokenName => null,
            ]);

        $user->setRememberToken(null);
    }

    private function deleteSessions(
        User $user,
        ?string $exceptSessionId = null
    ): void {
        $query = DB::connection(
            config('session.connection')
        )
            ->table(
                config(
                    'session.table',
                    'sessions'
                )
            )
            ->where(
                'user_id',
                $user->getKey()
            );

        if ($exceptSessionId !== null) {
            $query->where(
                'id',
                '<>',
                $exceptSessionId
            );
        }

        $query->delete();
    }
}