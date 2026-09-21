<?php

namespace App\Http\Controllers;

use App\Http\Requests\VisitorRequest;
use App\Models\Visitor;
use App\Services\AiAssistService;
use App\Services\AuditService;
use App\Services\CalendarSyncService;
use App\Services\VisitorCheckService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
    public function __construct(
        private VisitorCheckService $checks,
        private CalendarSyncService $calendar,
        private AiAssistService $ai,
        private AuditService $audit,
    ) {}

    public function index(Request $request)
    {
        abort_unless(
            $request->user()->can('viewVisitors'),
            403
        );

        /*
         * Visitor Desk filters.
         *
         * Keep these independent from Appointment Management.
         * Visitor Desk is responsible for reception operations,
         * while appointments remain their own records.
         */
        $allowedStatuses = [
            'expected',
            'awaiting_host',
            'checked_in',
            'completed',
            'declined',
            'cancelled',
            'no_show',
        ];

        $allowedVisitorTypes = [
            'customer',
            'business_partner',
            'supplier',
            'government',
            'applicant',
            'guest',
            'other',
        ];

        $search =
            trim(
                (string) $request->query(
                    'q',
                    ''
                )
            );

        $requestedStatus =
            (string) $request->query(
                'status',
                ''
            );

        $status =
            in_array(
                $requestedStatus,
                $allowedStatuses,
                true
            )
                ? $requestedStatus
                : null;

        $requestedVisitorType =
            (string) $request->query(
                'visitor_type',
                ''
            );

        $visitorType =
            in_array(
                $requestedVisitorType,
                $allowedVisitorTypes,
                true
            )
                ? $requestedVisitorType
                : null;

        /*
         * Operational snapshot.
         *
         * These counts come directly from PostgreSQL and are not
         * limited to the current paginator page.
         */
        $statusCounts =
            Visitor::query()
                ->selectRaw(
                    'status, COUNT(*) as total'
                )
                ->whereIn(
                    'status',
                    $allowedStatuses
                )
                ->groupBy(
                    'status'
                )
                ->pluck(
                    'total',
                    'status'
                );

        $visitorCounts = [
            'total' => Visitor::query()->count(),

            'expected' => (int) (
                $statusCounts['expected']
                ?? 0
            ),

            'awaiting_host' => (int) (
                $statusCounts['awaiting_host']
                ?? 0
            ),

            'checked_in' => (int) (
                $statusCounts['checked_in']
                ?? 0
            ),

            'completed' => (int) (
                $statusCounts['completed']
                ?? 0
            ),

            'declined' => (int) (
                $statusCounts['declined']
                ?? 0
            ),
        ];

        /*
         * Searchable visitor directory.
         */
        $visitors =
            Visitor::query()
                ->with(
                    'appointment'
                )

                ->when(
                    $search !== '',
                    function ($query) use ($search) {

                        $needle =
                            '%'
                            .mb_strtolower($search)
                            .'%';

                        $query->where(
                            function ($query) use ($needle) {

                                $query
                                    ->whereRaw(
                                        'LOWER(full_name) LIKE ?',
                                        [$needle]
                                    )

                                    ->orWhereRaw(
                                        "LOWER(COALESCE(organization, '')) LIKE ?",
                                        [$needle]
                                    )

                                    ->orWhereRaw(
                                        "LOWER(COALESCE(host_name, '')) LIKE ?",
                                        [$needle]
                                    )

                                    ->orWhereRaw(
                                        "LOWER(COALESCE(host_email, '')) LIKE ?",
                                        [$needle]
                                    )

                                    ->orWhereRaw(
                                        "LOWER(COALESCE(email, '')) LIKE ?",
                                        [$needle]
                                    )

                                    ->orWhereRaw(
                                        "LOWER(COALESCE(badge_number, '')) LIKE ?",
                                        [$needle]
                                    );
                            }
                        );
                    }
                )

                ->when(
                    $status,
                    fn ($query) => $query->where(
                        'status',
                        $status
                    )
                )

                ->when(
                    $visitorType,
                    fn ($query) => $query->where(
                        'visitor_type',
                        $visitorType
                    )
                )

                ->orderByDesc(
                    'created_at'
                )

                ->paginate(
                    20
                )

                ->withQueryString();

        return view(
            'visitors.index',
            compact(
                'visitors',
                'visitorCounts',
                'allowedStatuses',
                'allowedVisitorTypes'
            )
        );
    }

    public function store(VisitorRequest $request): RedirectResponse
    {
        $visitor = Visitor::create($request->validated());

        $this->audit->log(
            $request->user(),
            'create',
            'visitors',
            "Visitor • {$visitor->full_name}",
            (string) $visitor->id,
            'Visitor registered'
        );

        return redirect()->route('visitors.index')->with('status', "{$visitor->full_name} logged.");
    }

    public function checkIn(Request $request, Visitor $visitor): RedirectResponse
    {
        abort_unless($request->user()->can('operateVisitorDesk'), 403);
        $data = $request->validate(['badge_number' => ['nullable', 'string'], 'notes' => ['nullable', 'string']]);
        $this->checks->checkIn($request->user(), $visitor, $data['badge_number'] ?? null, $data['notes'] ?? null);

        return back()->with('status', "{$visitor->full_name} checked in.");
    }

    public function checkOut(Request $request, Visitor $visitor): RedirectResponse
    {
        abort_unless($request->user()->can('operateVisitorDesk'), 403);
        $this->checks->checkOut($request->user(), $visitor);

        return back()->with('status', "{$visitor->full_name} checked out.");
    }

    public function decline(Request $request, Visitor $visitor): RedirectResponse
    {
        abort_unless($request->user()->can('operateVisitorDesk'), 403);
        $data = $request->validate(['notes' => ['nullable', 'string']]);
        $this->checks->decline($request->user(), $visitor, $data['notes'] ?? null);

        return back()->with('status', 'Visit declined.');
    }

    public function syncCalendar(Request $request, Visitor $visitor): RedirectResponse
    {
        abort_unless($request->user()->can('operateVisitorDesk'), 403);
        $result = $this->calendar->sync($request->user(), $visitor);

        return back()->with('status', "Synced to Google Calendar ({$result['event_id']}).");
    }

    public function aiAssist(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('useAiAssist'), 403);

        $data = $request->validate([
            'mode' => ['required', 'in:classify,extract,summary,appointment_check'],
            'text' => ['nullable', 'string', 'max:2000'],
            'context' => ['nullable', 'array'],
        ]);

        $result = $this->ai->assist($request->user(), $data['mode'], $data['text'] ?? '', $data['context'] ?? []);

        return response()->json($result);
    }
}
