<?php

namespace App\Http\Controllers;

use App\Http\Requests\FacilityRequest;
use App\Models\AuditLog;
use App\Models\Facility;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    public function index(Request $request)
    {
        $canViewArchived =
            $request->user()->can('viewArchivedFacilities');

        $baseQuery = Facility::query();

        if (! $canViewArchived) {
            $baseQuery->where(
                'status',
                '!=',
                'archived'
            );
        }

        $counts = [
            'total' =>
                (clone $baseQuery)->count(),

            'available' =>
                (clone $baseQuery)
                    ->where('status', 'available')
                    ->count(),

            'maintenance' =>
                (clone $baseQuery)
                    ->where('status', 'maintenance')
                    ->count(),

            'unavailable' =>
                (clone $baseQuery)
                    ->where('status', 'unavailable')
                    ->count(),

            'archived' =>
                $canViewArchived
                    ? (clone $baseQuery)
                        ->where('status', 'archived')
                        ->count()
                    : 0,
        ];

        $query = clone $baseQuery;

        if ($request->filled('search')) {
            $search = trim(
                (string) $request->input('search')
            );

            $searchLike =
                '%'.strtolower($search).'%';

            $typeSearch =
                '%'.strtolower(
                    str_replace(
                        [' ', '-'],
                        '_',
                        $search
                    )
                ).'%';

            $query->where(
                function ($facilityQuery) use (
                    $searchLike,
                    $typeSearch
                ) {
                    $facilityQuery
                        ->whereRaw(
                            'LOWER(name) LIKE ?',
                            [$searchLike]
                        )
                        ->orWhereRaw(
                            'LOWER(COALESCE(location, ?)) LIKE ?',
                            ['', $searchLike]
                        )
                        ->orWhereRaw(
                            'LOWER(facility_type) LIKE ?',
                            [$typeSearch]
                        );
                }
            );
        }

        if ($request->filled('type')) {
            $request->validate([
                'type' => [
                    'in:conference_room,meeting_room,training_room,function_room,vehicle,other',
                ],
            ]);

            $query->where(
                'facility_type',
                $request->input('type')
            );
        }

        if ($request->filled('status')) {
            $allowedStatuses = [
                'available',
                'maintenance',
                'unavailable',
            ];

            if ($canViewArchived) {
                $allowedStatuses[] =
                    'archived';
            }

            $request->validate([
                'status' => [
                    'in:'.implode(
                        ',',
                        $allowedStatuses
                    ),
                ],
            ]);

            $query->where(
                'status',
                $request->input('status')
            );
        }

        $facilities = $query
            ->orderByRaw(
                "CASE
                    WHEN status = 'available' THEN 1
                    WHEN status = 'maintenance' THEN 2
                    WHEN status = 'unavailable' THEN 3
                    WHEN status = 'archived' THEN 4
                    ELSE 5
                END"
            )
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view(
            'facilities.index',
            compact(
                'facilities',
                'counts',
                'canViewArchived'
            )
        );
    }

    public function create(Request $request)
    {
        abort_unless($request->user()->can('manageFacilities'), 403);

        return view('facilities.create');
    }

    public function store(FacilityRequest $request): RedirectResponse
    {
        $facility = Facility::create($request->validated() + ['updated_by_email' => $request->user()->email]);

        AuditLog::create([
            'actor_email' => $request->user()->email, 'actor_role' => $request->user()->app_role,
            'action' => 'create', 'module' => 'facilities', 'record_label' => "Facility • {$facility->name}",
            'record_id' => $facility->id, 'created_at' => now(),
        ]);

        return redirect()->route('facilities.index')->with('status', 'Facility created.');
    }

    public function edit(Request $request, Facility $facility)
    {
        abort_unless($request->user()->can('manageFacilities'), 403);

        return view('facilities.edit', compact('facility'));
    }

    public function update(FacilityRequest $request, Facility $facility): RedirectResponse
    {
        $facility->update($request->validated() + ['updated_by_email' => $request->user()->email]);

        AuditLog::create([
            'actor_email' => $request->user()->email, 'actor_role' => $request->user()->app_role,
            'action' => 'update', 'module' => 'facilities', 'record_label' => "Facility • {$facility->name}",
            'record_id' => $facility->id, 'created_at' => now(),
        ]);

        return redirect()->route('facilities.index')->with('status', 'Facility updated.');
    }

    public function destroy(Request $request, Facility $facility): RedirectResponse
    {
        abort_unless($request->user()->can('manageFacilities'), 403);
        $facility->update(['status' => 'archived']);

        AuditLog::create([
            'actor_email' => $request->user()->email, 'actor_role' => $request->user()->app_role,
            'action' => 'archive', 'module' => 'facilities', 'record_label' => "Facility • {$facility->name}",
            'record_id' => $facility->id, 'created_at' => now(),
        ]);

        return redirect()->route('facilities.index')->with('status', 'Facility archived.');
    }
}
