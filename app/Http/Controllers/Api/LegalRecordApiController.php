<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LegalRecordRequest;
use App\Http\Resources\LegalRecordResource;
use App\Models\LegalRecord;
use App\Services\AuditService;
use App\Services\DocumentFileStorageService;
use Illuminate\Http\Request;

class LegalRecordApiController extends Controller
{
    public function __construct(
        private AuditService $audit,
        private DocumentFileStorageService $fileStorage
    ) {}

    public function index(Request $request)
    {
        abort_unless(
            $request->user()->can('viewLegal'),
            403
        );

        $records = LegalRecord::query()
            ->when(
                $request->filled('status'),
                fn ($q) => $q->where(
                    'status',
                    $request->string('status')
                )
            )
            ->orderByDesc('updated_at')
            ->paginate(20);

        return LegalRecordResource::collection(
            $records
        );
    }

    public function show(
        Request $request,
        LegalRecord $legal
    ) {
        abort_unless(
            $request->user()->can('viewLegal'),
            403
        );

        return new LegalRecordResource(
            $legal
        );
    }

    public function store(
        LegalRecordRequest $request
    ) {
        abort_unless(
            $request->user()->can('manageLegal'),
            403
        );

        $data = $request->validated();
        $file = $request->file('file');

        unset($data['file']);

        $persist = function (
            array $payload
        ) use ($request): LegalRecord {
            $record = LegalRecord::create(
                $payload
            );

            $this->audit->log(
                $request->user(),
                'create',
                'legal',
                "Legal \u{2022} {$record->title}",
                (string) $record->id
            );

            return $record;
        };

        if ($file) {
            $fileName =
                $file->getClientOriginalName();

            $record =
                $this->fileStorage->create(
                    $file,
                    'legal',
                    function (
                        string $newPath
                    ) use (
                        $persist,
                        $data,
                        $fileName
                    ): LegalRecord {
                        $payload = $data;

                        $payload['file_uri'] =
                            $newPath;

                        $payload['file_name'] =
                            $fileName;

                        return $persist(
                            $payload
                        );
                    }
                );
        } else {
            $record = $persist(
                $data
            );
        }

        return (
            new LegalRecordResource($record)
        )
            ->response()
            ->setStatusCode(201);
    }
    public function update(
        LegalRecordRequest $request,
        LegalRecord $legal
    ) {
        abort_unless(
            $request->user()->can('manageLegal'),
            403
        );

        $data = $request->validated();
        $file = $request->file('file');

        unset($data['file']);

        $persist = function (
            array $payload
        ) use (
            $request,
            $legal
        ): LegalRecord {
            $legal->update(
                $payload
            );

            $this->audit->log(
                $request->user(),
                'update',
                'legal',
                "Legal \u{2022} {$legal->title}",
                (string) $legal->id
            );

            return $legal;
        };

        if ($file) {
            $fileName =
                $file->getClientOriginalName();

            $this->fileStorage->replace(
                $file,
                'legal',
                $legal->file_uri,
                function (
                    string $newPath
                ) use (
                    $persist,
                    $data,
                    $fileName
                ): LegalRecord {
                    $payload = $data;

                    $payload['file_uri'] =
                        $newPath;

                    $payload['file_name'] =
                        $fileName;

                    return $persist(
                        $payload
                    );
                }
            );
        } else {
            $persist(
                $data
            );
        }

        return new LegalRecordResource(
            $legal->refresh()
        );
    }}