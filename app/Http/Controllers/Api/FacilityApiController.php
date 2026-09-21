<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FacilityRequest;
use App\Http\Resources\FacilityResource;
use App\Models\Facility;
use App\Services\AuditService;
use Illuminate\Http\Request;

class FacilityApiController extends Controller
{
    public function __construct(
        private AuditService $audit
    ) {}

    public function index(Request $request)
    {
        $facilities = Facility::when(! $request->user()->can('viewArchivedFacilities'), fn ($q) => $q->where('status', '!=', 'archived'))
            ->orderBy('name')->paginate(20);

        return FacilityResource::collection($facilities);
    }

    public function show(Request $request, Facility $facility)
    {
        abort_if($facility->status === 'archived' && ! $request->user()->can('viewArchivedFacilities'), 404);

        return new FacilityResource($facility);
    }

    public function store(FacilityRequest $request)
    {
        abort_unless(
            $request->user()->can('manageFacilities'),
            403
        );

        $facility = Facility::create($request->validated() + ['updated_by_email' => $request->user()->email]);

        $this->audit->log(
            $request->user(),
            'create',
            'facilities',
            "Facility • {$facility->name}",
            (string) $facility->id
        );

        return (new FacilityResource($facility))->response()->setStatusCode(201);
    }

    public function update(FacilityRequest $request, Facility $facility)
    {
        abort_unless(
            $request->user()->can('manageFacilities'),
            403
        );

        $facility->update($request->validated() + ['updated_by_email' => $request->user()->email]);

        $this->audit->log(
            $request->user(),
            'update',
            'facilities',
            "Facility • {$facility->name}",
            (string) $facility->id
        );

        return new FacilityResource($facility);
    }

    public function destroy(Request $request, Facility $facility)
    {
        abort_unless($request->user()->can('manageFacilities'), 403);
        $facility->update([
            'status' => 'archived',
            'updated_by_email' => $request->user()->email,
        ]);

        $this->audit->log(
            $request->user(),
            'archive',
            'facilities',
            "Facility • {$facility->name}",
            (string) $facility->id
        );

        return response()->json(null, 204);
    }

    public function restore(Request $request, Facility $facility)
    {
        abort_unless(
            $request->user()->can('manageFacilities'),
            403
        );

        abort_if(
            $facility->status !== 'archived',
            422,
            'Only archived facilities can be restored.'
        );

        $facility->update([
            'status' => 'unavailable',
            'updated_by_email' => $request->user()->email,
        ]);

        $this->audit->log(
            $request->user(),
            'restore',
            'facilities',
            "Facility • {$facility->name}",
            (string) $facility->id
        );

        return new FacilityResource(
            $facility->refresh()
        );
    }
}
