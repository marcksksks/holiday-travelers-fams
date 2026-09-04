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
        abort_unless($request->user()->can('viewVisitors'), 403);

        $visitors = Visitor::with('appointment')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->orderByDesc('created_at')
            ->paginate(20)->withQueryString();

        return view('visitors.index', compact('visitors'));
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
        $data = $request->validate([
            'mode' => ['required', 'in:classify,extract,summary,appointment_check'],
            'text' => ['nullable', 'string', 'max:2000'],
            'context' => ['nullable', 'array'],
        ]);

        $result = $this->ai->assist($request->user(), $data['mode'], $data['text'] ?? '', $data['context'] ?? []);

        return response()->json($result);
    }
}
