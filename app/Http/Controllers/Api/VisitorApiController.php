<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VisitorRequest;
use App\Http\Resources\VisitorResource;
use App\Models\Visitor;
use App\Services\AuditService;
use App\Services\PrivacyService;
use App\Services\VisitorCheckService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VisitorApiController extends Controller
{
    public function __construct(
        private VisitorCheckService $checks,
        private AuditService $audit,
        private PrivacyService $privacy
    ) {}

    public function index(Request $request)
    {
        abort_unless($request->user()->can('viewVisitors'), 403);
        $visitors = Visitor::when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->orderByDesc('created_at')->paginate(20);

        return VisitorResource::collection($visitors);
    }

    public function show(Request $request, Visitor $visitor)
    {
        abort_unless($request->user()->can('viewVisitors'), 403);

        return new VisitorResource($visitor);
    }

    public function store(VisitorRequest $request)
    {
        abort_unless(
            $request->user()->can('operateVisitorDesk'),
            403
        );

        $visitor = DB::transaction(
            function () use ($request): Visitor {
                $data = $request->validated();

                unset(
                    $data['privacy_acknowledged']
                );

                $visitor =
                    Visitor::create($data);

                $this->privacy->recordForVisitor(
                    $request->user(),
                    $visitor,
                    PrivacyService::PURPOSE_VISITOR_MANAGEMENT,
                    PrivacyService::LAWFUL_BASIS_LEGITIMATE_INTERESTS,
                    PrivacyService::NOTICE_VERSION,
                    false,
                    null,
                    'visitor_api',
                    [
                        'notice_acknowledged' => true,
                    ]
                );

                $this->audit->log(
                    $request->user(),
                    'create',
                    'visitors',
                    "Visitor • {$visitor->full_name}",
                    (string) $visitor->id,
                    'Visitor registered'
                );

                return $visitor;
            }
        );

        return (new VisitorResource($visitor))
            ->response()
            ->setStatusCode(201);
    }

    public function checkIn(Request $request, Visitor $visitor)
    {
        abort_unless($request->user()->can('operateVisitorDesk'), 403);
        $data = $request->validate(['badge_number' => ['nullable', 'string']]);

        return new VisitorResource($this->checks->checkIn($request->user(), $visitor, $data['badge_number'] ?? null));
    }

    public function checkOut(Request $request, Visitor $visitor)
    {
        abort_unless($request->user()->can('operateVisitorDesk'), 403);

        return new VisitorResource($this->checks->checkOut($request->user(), $visitor));
    }
}
