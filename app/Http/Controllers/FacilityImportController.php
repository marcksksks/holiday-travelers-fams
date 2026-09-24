<?php

namespace App\Http\Controllers;

use App\Http\Requests\FacilityImportRequest;
use App\Services\FacilityImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FacilityImportController extends Controller
{
    public function index(
        Request $request
    ): View {
        abort_unless(
            $request
                ->user()
                ->can('manageFacilities'),
            403
        );

        return view(
            'facilities.import'
        );
    }

    public function store(
        FacilityImportRequest $request,
        FacilityImportService $service
    ): RedirectResponse {
        $result =
            $service->import(
                $request->file(
                    'import_file'
                ),
                $request->user()
            );

        return redirect()
            ->route(
                'facilities.index'
            )
            ->with(
                'status',
                sprintf(
                    '%d facilities imported successfully from %s.',
                    $result['imported'],
                    $result['format']
                )
            );
    }

    public function template(
        Request $request
    ): StreamedResponse {
        abort_unless(
            $request
                ->user()
                ->can('manageFacilities'),
            403
        );

        return response()
            ->streamDownload(
                function (): void {
                    $handle =
                        fopen(
                            'php://output',
                            'wb'
                        );

                    if ($handle === false) {
                        return;
                    }

                    fwrite(
                        $handle,
                        "\xEF\xBB\xBF"
                    );

                    fputcsv(
                        $handle,
                        [
                            'name',
                            'description',
                            'location',
                            'capacity',
                            'facility_type',
                            'status',
                            'equipment',
                        ]
                    );

                    fputcsv(
                        $handle,
                        [
                            'Example Meeting Room',
                            'Sample import row',
                            'Main Office - 2nd Floor',
                            '12',
                            'meeting_room',
                            'available',
                            'Projector;Whiteboard;Wi-Fi',
                        ]
                    );

                    fclose(
                        $handle
                    );
                },
                'fams-facility-import-template.csv',
                [
                    'Content-Type' => 'text/csv; charset=UTF-8',

                    'Cache-Control' => 'no-store, no-cache, must-revalidate',
                ]
            );
    }
}
