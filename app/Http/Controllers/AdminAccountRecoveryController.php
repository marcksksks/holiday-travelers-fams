<?php

namespace App\Http\Controllers;

use App\Models\AccountRecoveryRequest;
use App\Services\AccountRecoveryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminAccountRecoveryController extends Controller
{
    public function __construct(
        private AccountRecoveryService $recovery
    ) {}

    public function index(
        Request $request
    ): View {
        abort_unless(
            $request->user()->isSysAdmin(),
            403
        );

        $request->validate([
            'search' => [
                'nullable',
                'string',
                'max:255',
            ],

            'status' => [
                'nullable',
                Rule::in([
                    AccountRecoveryRequest::STATUS_PENDING,
                    AccountRecoveryRequest::STATUS_APPROVED,
                    AccountRecoveryRequest::STATUS_REJECTED,
                    AccountRecoveryRequest::STATUS_COMPLETED,
                    AccountRecoveryRequest::STATUS_EXPIRED,
                ]),
            ],
        ]);

        /*
         * Keep visible status accurate even if no recovery action
         * has run since the authorization expired.
         */
        AccountRecoveryRequest::query()
            ->whereIn(
                'status',
                [
                    AccountRecoveryRequest::STATUS_PENDING,
                    AccountRecoveryRequest::STATUS_APPROVED,
                ]
            )
            ->whereNotNull(
                'expires_at'
            )
            ->where(
                'expires_at',
                '<',
                now()
            )
            ->update([
                'status' => AccountRecoveryRequest::STATUS_EXPIRED,

                'updated_at' => now(),
            ]);

        $recoveries =
            AccountRecoveryRequest::query()
                ->with([
                    'user',
                    'approver',
                ])

                ->when(
                    $request->filled('search'),
                    function ($query) use ($request): void {
                        $search =
                            '%'.
                            $request
                                ->string('search')
                                ->trim().
                            '%';

                        $query->where(
                            function ($query) use ($search): void {
                                $query
                                    ->where(
                                        'reference',
                                        'ilike',
                                        $search
                                    )
                                    ->orWhereHas(
                                        'user',
                                        function ($userQuery) use ($search): void {
                                            $userQuery
                                                ->where(
                                                    'full_name',
                                                    'ilike',
                                                    $search
                                                )
                                                ->orWhere(
                                                    'email',
                                                    'ilike',
                                                    $search
                                                );
                                        }
                                    );
                            }
                        );
                    }
                )

                ->when(
                    $request->filled('status'),
                    fn ($query) => $query->where(
                        'status',
                        $request->string('status')
                    )
                )

                ->orderByDesc(
                    'requested_at'
                )
                ->paginate(20)
                ->withQueryString();

        $stats = [
            'pending' => AccountRecoveryRequest::query()
                ->where(
                    'status',
                    AccountRecoveryRequest::STATUS_PENDING
                )
                ->count(),

            'approved' => AccountRecoveryRequest::query()
                ->where(
                    'status',
                    AccountRecoveryRequest::STATUS_APPROVED
                )
                ->count(),

            'completed' => AccountRecoveryRequest::query()
                ->where(
                    'status',
                    AccountRecoveryRequest::STATUS_COMPLETED
                )
                ->count(),

            'closed' => AccountRecoveryRequest::query()
                ->whereIn(
                    'status',
                    [
                        AccountRecoveryRequest::STATUS_REJECTED,
                        AccountRecoveryRequest::STATUS_EXPIRED,
                    ]
                )
                ->count(),
        ];

        return view(
            'account-recovery.index',
            compact(
                'recoveries',
                'stats'
            )
        );
    }

    public function approve(
        Request $request,
        AccountRecoveryRequest $recovery
    ): RedirectResponse {
        abort_unless(
            $request->user()->isSysAdmin(),
            403
        );

        $this->recovery
            ->approve(
                $recovery,
                $request->user()
            );

        return back()->with(
            'status',
            'Account recovery authorized. The user now has a 15-minute password reset window.'
        );
    }

    public function reject(
        Request $request,
        AccountRecoveryRequest $recovery
    ): RedirectResponse {
        abort_unless(
            $request->user()->isSysAdmin(),
            403
        );

        $this->recovery
            ->reject(
                $recovery,
                $request->user()
            );

        return back()->with(
            'status',
            'Account recovery request rejected.'
        );
    }
}
