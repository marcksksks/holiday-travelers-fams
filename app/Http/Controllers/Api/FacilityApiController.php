<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FacilityRequest;
use App\Http\Resources\FacilityResource;
use App\Models\Facility;
use Illuminate\Http\Request;

class FacilityApiController extends Controller
{
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
        $facility = Facility::create($request->validated() + ['updated_by_email' => $request->user()->email]);

        return (new FacilityResource($facility))->response()->setStatusCode(201);
    }

    public function update(FacilityRequest $request, Facility $facility)
    {
        $facility->update($request->validated() + ['updated_by_email' => $request->user()->email]);

        return new FacilityResource($facility);
    }

    public function destroy(Request $request, Facility $facility)
    {
        abort_unless($request->user()->can('manageFacilities'), 403);
        $facility->update(['status' => 'archived']);

        return response()->json(null, 204);
    }
}
