<?php

namespace App\Http\Controllers;

use App\Models\PrivacyRequest;
use App\Services\PrivacyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PrivacyRequestController extends Controller
{
    public function __construct(
        private PrivacyService $privacy
    ) {}

    public function index(
        Request $request
    ): View {
        abort_unless(
            $request->user()->can(
                'managePrivacy'
            ),
            403
        );

        $filters =
            $request->validate([
                'status' => [
                    'nullable',
                    Rule::in([
                        PrivacyRequest::STATUS_PENDING,
                        PrivacyRequest::STATUS_UNDER_REVIEW,
                        PrivacyRequest::STATUS_APPROVED,
                        PrivacyRequest::STATUS_PARTIALLY_APPROVED,
                        PrivacyRequest::STATUS_DENIED,
                        PrivacyRequest::STATUS_COMPLETED,
                    ]),
                ],

                'type' => [
                    'nullable',
                    Rule::in(
                        PrivacyRequest::TYPES
                    ),
                ],

                'q' => [
                    'nullable',
                    'string',
                    'max:100',
                ],
            ]);

        $status =
            $filters['status']
            ?? null;

        $type =
            $filters['type']
            ?? null;

        $search =
            trim(
                $filters['q']
                ?? ''
            );

        $privacyRequests =
            PrivacyRequest::query()
                ->with([
                    'user:id,full_name,email,app_role',
                    'reviewedBy:id,full_name,email,app_role',
                    'executedBy:id,full_name,email,app_role',
                ])
                ->when(
                    $status,
                    fn ($query) => $query->where(
                        'status',
                        $status
                    )
                )
                ->when(
                    $type,
                    fn ($query) => $query->where(
                        'type',
                        $type
                    )
                )
                ->when(
                    $search !== '',
                    function ($query) use (
                        $search
                    ) {
                        $query->where(
                            function ($query) use (
                                $search
                            ) {
                                if (
                                    ctype_digit(
                                        $search
                                    )
                                ) {
                                    $query->orWhere(
                                        'id',
                                        (int) $search
                                    );
                                }

                                $query
                                    ->orWhere(
                                        'type',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhereHas(
                                        'user',
                                        function (
                                            $userQuery
                                        ) use (
                                            $search
                                        ) {
                                            $userQuery
                                                ->where(
                                                    'full_name',
                                                    'like',
                                                    "%{$search}%"
                                                )
                                                ->orWhere(
                                                    'email',
                                                    'like',
                                                    "%{$search}%"
                                                );
                                        }
                                    );
                            }
                        );
                    }
                )
                ->orderByRaw(
                    '
                    CASE
                        WHEN status = ? THEN 0
                        WHEN status = ? THEN 1
                        WHEN status IN (?, ?) THEN 2
                        ELSE 3
                    END
                    ',
                    [
                        PrivacyRequest::STATUS_PENDING,
                        PrivacyRequest::STATUS_UNDER_REVIEW,
                        PrivacyRequest::STATUS_APPROVED,
                        PrivacyRequest::STATUS_PARTIALLY_APPROVED,
                    ]
                )
                ->orderByDesc(
                    'submitted_at'
                )
                ->orderByDesc(
                    'id'
                )
                ->paginate(20)
                ->withQueryString();

        $stats = [
            'total' => PrivacyRequest::count(),

            'pending' => PrivacyRequest::where(
                'status',
                PrivacyRequest::STATUS_PENDING
            )->count(),

            'under_review' => PrivacyRequest::where(
                'status',
                PrivacyRequest::STATUS_UNDER_REVIEW
            )->count(),

            'execution_pending' => PrivacyRequest::whereIn(
                'status',
                [
                    PrivacyRequest::STATUS_APPROVED,
                    PrivacyRequest::STATUS_PARTIALLY_APPROVED,
                ]
            )->count(),

            'closed' => PrivacyRequest::whereIn(
                'status',
                [
                    PrivacyRequest::STATUS_DENIED,
                    PrivacyRequest::STATUS_COMPLETED,
                ]
            )->count(),
        ];

        return view(
            'privacy.index',
            compact(
                'privacyRequests',
                'stats',
                'status',
                'type',
                'search'
            )
        );
    }

    public function startReview(
        Request $request,
        PrivacyRequest $privacyRequest
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'managePrivacy'
            ),
            403
        );

        $this->privacy->startReview(
            $privacyRequest,
            $request->user()
        );

        return redirect()
            ->route(
                'privacy-requests.index'
            )
            ->with(
                'status',
                "Privacy request #{$privacyRequest->id} is now under review."
            );
    }

    public function decision(
        Request $request,
        PrivacyRequest $privacyRequest
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'managePrivacy'
            ),
            403
        );

        $data =
            $request->validate([
                'decision' => [
                    'required',
                    Rule::in([
                        PrivacyRequest::STATUS_APPROVED,
                        PrivacyRequest::STATUS_PARTIALLY_APPROVED,
                        PrivacyRequest::STATUS_DENIED,
                    ]),
                ],

                'decision_reason' => [
                    'required',
                    'string',
                    'max:2000',
                ],

                'retention_basis' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],
            ]);

        $updated =
            $this->privacy
                ->decideRequest(
                    $privacyRequest,
                    $request->user(),
                    $data['decision'],
                    $data['decision_reason'],
                    $data['retention_basis']
                        ?? null
                );

        $label =
            str(
                $updated->status
            )
                ->replace(
                    '_',
                    ' '
                )
                ->title();

        return redirect()
            ->route(
                'privacy-requests.index'
            )
            ->with(
                'status',
                "Privacy request #{$updated->id}: {$label}. No personal data has been erased."
            );
    }

    public function execute(
        Request $request,
        PrivacyRequest $privacyRequest
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'executePrivacy'
            ),
            403
        );

        $request->validate([
            'current_password' => [
                'required',
                'current_password',
            ],

            'confirm_execution' => [
                'accepted',
            ],
        ]);

        $updated =
            $this->privacy
                ->executeRequest(
                    $privacyRequest,
                    $request->user()
                );

        return redirect()
            ->route(
                'privacy-requests.index'
            )
            ->with(
                'status',
                "Privacy request #{$updated->id} controlled execution completed."
            );
    }
}
