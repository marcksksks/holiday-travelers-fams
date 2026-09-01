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
        $facilities = Facility::when(! $request->user()->can('viewArchivedFacilities'), fn ($q) => $q->where('status', '!=', 'archived'))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('facilities.index', compact('facilities'));
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
