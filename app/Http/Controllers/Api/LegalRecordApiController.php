<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LegalRecordRequest;
use App\Http\Resources\LegalRecordResource;
use App\Models\LegalRecord;
use App\Models\User;
use App\Services\AuditService;
use App\Services\DocumentFileStorageService;
use Illuminate\Http\Request;

class LegalRecordApiController extends Controller
{
    public function __construct(
        private AuditService $audit,
        private DocumentFileStorageService $fileStorage
    ) {}

    public function index(
        Request $request
    ) {
        abort_unless(
            $request->user()
                ->can('viewLegal'),
            403
        );

        $records =
            LegalRecord::query()
                ->with(
                    'assignedOfficer:id,full_name,email,app_role'
                )
                ->when(
                    $request->filled(
                        'status'
                    ),
                    fn ($query) => $query->where(
                        'status',
                        $request->string(
                            'status'
                        )
                    )
                )
                ->when(
                    $request->filled(
                        'priority'
                    ),
                    fn ($query) => $query->where(
                        'priority',
                        $request->string(
                            'priority'
                        )
                    )
                )
                ->when(
                    $request->filled(
                        'review_status'
                    ),
                    fn ($query) => $query->where(
                        'review_status',
                        $request->string(
                            'review_status'
                        )
                    )
                )
                ->orderByDesc(
                    'updated_at'
                )
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
            $request->user()
                ->can('viewLegal'),
            403
        );

        $legal->load(
            'assignedOfficer:id,full_name,email,app_role'
        );

        return new LegalRecordResource(
            $legal
        );
    }

    public function store(
        LegalRecordRequest $request
    ) {
        abort_unless(
            $request->user()
                ->can('manageLegal'),
            403
        );

        $data =
            $this->preparePayload(
                $request->validated()
            );

        $file =
            $request->file('file');

        unset(
            $data['file']
        );

        $persist =
            function (
                array $payload
            ) use (
                $request
            ): LegalRecord {
                $record =
                    LegalRecord::create(
                        $payload
                    );

                $this->audit->log(
                    $request->user(),
                    'create',
                    'legal',
                    "Legal • {$record->title}",
                    (string) $record->id
                );

                return $record;
            };

        if ($file) {
            $fileName =
                $file
                    ->getClientOriginalName();

            $record =
                $this->fileStorage
                    ->create(
                        $file,
                        'legal',
                        function (
                            string $newPath
                        ) use (
                            $persist,
                            $data,
                            $fileName
                        ): LegalRecord {
                            $payload =
                                $data;

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
            $record =
                $persist(
                    $data
                );
        }

        $record->load(
            'assignedOfficer:id,full_name,email,app_role'
        );

        return (
            new LegalRecordResource(
                $record
            )
        )
            ->response()
            ->setStatusCode(201);
    }

    public function update(
        LegalRecordRequest $request,
        LegalRecord $legal
    ) {
        abort_unless(
            $request->user()
                ->can('manageLegal'),
            403
        );

        $data =
            $this->preparePayload(
                $request->validated(),
                $legal
            );

        $file =
            $request->file('file');

        unset(
            $data['file']
        );

        $persist =
            function (
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
                    "Legal • {$legal->title}",
                    (string) $legal->id
                );

                return $legal;
            };

        if ($file) {
            $fileName =
                $file
                    ->getClientOriginalName();

            $this->fileStorage
                ->replace(
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
                        $payload =
                            $data;

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

        $legal =
            $legal
                ->refresh()
                ->load(
                    'assignedOfficer:id,full_name,email,app_role'
                );

        return new LegalRecordResource(
            $legal
        );
    }

    private function preparePayload(
        array $data,
        ?LegalRecord $legal = null
    ): array {
        if (
            filled(
                $data['assigned_user_id']
                ?? null
            )
        ) {
            $email =
                User::query()
                    ->whereKey(
                        $data['assigned_user_id']
                    )
                    ->value('email');

            if ($email) {
                $data['responsible_officer_email'] =
                    $email;
            }
        }

        if (
            array_key_exists(
                'status',
                $data
            )
        ) {
            if (
                $data['status'] ===
                'closed'
            ) {
                $data['closed_at'] =
                    $legal?->closed_at
                    ?? now();
            } else {
                $data['closed_at'] =
                    null;
            }
        }

        return $data;
    }
}
