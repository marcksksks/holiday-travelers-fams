<?php

namespace App\Http\Controllers;

use App\Services\AuditService;
use App\Services\ReportDataService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExportController extends Controller
{
    public function __construct(
        private ReportDataService $reports,
        private AuditService $audit
    ) {}

    public function pdf(
        Request $request
    ): Response {
        $data =
            $this->reports->build(
                $request
            );

        $this->recordExport(
            $request,
            'pdf',
            $data
        );

        return Pdf::loadView(
            'reports.print',
            [
                ...$data,
                'pdfMode' => true,
            ]
        )
            ->setPaper(
                'a4',
                'portrait'
            )
            ->download(
                $this->filename(
                    $data,
                    'pdf'
                )
            );
    }

    public function xlsx(
        Request $request
    ): StreamedResponse {
        $data =
            $this->reports->build(
                $request
            );

        $this->recordExport(
            $request,
            'xlsx',
            $data
        );

        $spreadsheet =
            $this->spreadsheet(
                $data
            );

        $writer =
            new Xlsx(
                $spreadsheet
            );

        return response()
            ->streamDownload(
                function () use (
                    $writer,
                    $spreadsheet
                ) {
                    $writer->save(
                        'php://output'
                    );

                    $spreadsheet
                        ->disconnectWorksheets();
                },
                $this->filename(
                    $data,
                    'xlsx'
                ),
                [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',

                    'Cache-Control' => 'no-store, no-cache, must-revalidate',
                ]
            );
    }

    public function csv(
        Request $request
    ): StreamedResponse {
        $data =
            $this->reports->build(
                $request
            );

        $this->recordExport(
            $request,
            'csv',
            $data
        );

        return response()
            ->streamDownload(
                function () use ($data) {
                    $handle =
                        fopen(
                            'php://output',
                            'wb'
                        );

                    if ($handle === false) {
                        return;
                    }

                    /*
                     * UTF-8 BOM improves compatibility with
                     * desktop spreadsheet applications.
                     */
                    fwrite(
                        $handle,
                        "\xEF\xBB\xBF"
                    );

                    fputcsv(
                        $handle,
                        [
                            'Section',
                            'Metric',
                            'Value',
                        ]
                    );

                    foreach (
                        $this->summaryRows(
                            $data
                        ) as $row
                    ) {
                        fputcsv(
                            $handle,
                            $row
                        );
                    }

                    foreach (
                        $this->breakdownRows(
                            $data
                        ) as $row
                    ) {
                        fputcsv(
                            $handle,
                            $row
                        );
                    }

                    foreach (
                        $data['facilityUtilization'] as $row
                    ) {
                        fputcsv(
                            $handle,
                            [
                                'Facility Utilization',
                                $row->facility?->name
                                    ?? 'Unknown Facility',
                                (int) $row->bookings,
                            ]
                        );
                    }

                    fclose(
                        $handle
                    );
                },
                $this->filename(
                    $data,
                    'csv'
                ),
                [
                    'Content-Type' => 'text/csv; charset=UTF-8',

                    'Cache-Control' => 'no-store, no-cache, must-revalidate',
                ]
            );
    }

    public function print(
        Request $request
    ): View {
        $data =
            $this->reports->build(
                $request
            );

        $this->audit->log(
            $request->user(),
            'report_print',
            'reports',
            'Management Report',
            null,
            'Printable report opened for '.
                $data['from']->toDateString().
                ' through '.
                $data['to']->toDateString().
                '.'
        );

        return view(
            'reports.print',
            [
                ...$data,
                'pdfMode' => false,
            ]
        );
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function spreadsheet(
        array $data
    ): Spreadsheet {
        $spreadsheet =
            new Spreadsheet;

        $spreadsheet
            ->getProperties()
            ->setCreator(
                'Holiday Travelers Inc.'
            )
            ->setTitle(
                'FAMS Management Report'
            )
            ->setSubject(
                'Facilities and Administrative Management System'
            )
            ->setDescription(
                'Operational, facilities, visitor, records, legal, contract, and compliance report.'
            );

        /*
         * Executive Summary worksheet.
         */
        $summary =
            $spreadsheet
                ->getActiveSheet();

        $summary->setTitle(
            'Executive Summary'
        );

        $summary->fromArray(
            [
                [
                    'HOLIDAY TRAVELERS INC.',
                ],
                [
                    'Facilities & Administrative Management System',
                ],
                [
                    'Management Report',
                ],
                [
                    'Reporting Period',
                    $data['from']->format('M d, Y').
                    ' - '.
                    $data['to']->format('M d, Y'),
                ],
                [
                    'Generated',
                    now()->format(
                        'M d, Y h:i A'
                    ),
                ],
                [],
                [
                    'Metric',
                    'Value',
                ],
            ],
            null,
            'A1'
        );

        $rowNumber = 8;

        foreach (
            $this->summaryRows(
                $data
            ) as $row
        ) {
            $summary
                ->setCellValue(
                    "A{$rowNumber}",
                    $row[1]
                );

            $summary
                ->setCellValueExplicit(
                    "B{$rowNumber}",
                    (string) $row[2],
                    DataType::TYPE_STRING
                );

            $rowNumber++;
        }

        /*
         * Operational breakdown worksheet.
         */
        $breakdowns =
            $spreadsheet
                ->createSheet();

        $breakdowns->setTitle(
            'Breakdowns'
        );

        $breakdowns->fromArray(
            [
                [
                    'Section',
                    'Metric',
                    'Value',
                ],
            ],
            null,
            'A1'
        );

        $breakdownRow = 2;

        foreach (
            $this->breakdownRows(
                $data
            ) as $row
        ) {
            $breakdowns
                ->fromArray(
                    [$row],
                    null,
                    "A{$breakdownRow}"
                );

            $breakdownRow++;
        }

        /*
         * Facility utilization worksheet.
         */
        $facilities =
            $spreadsheet
                ->createSheet();

        $facilities->setTitle(
            'Facility Utilization'
        );

        $facilities->fromArray(
            [
                [
                    'Facility',
                    'Approved Bookings',
                ],
            ],
            null,
            'A1'
        );

        $facilityRow = 2;

        foreach (
            $data['facilityUtilization'] as $row
        ) {
            $facilities->fromArray(
                [
                    [
                        $row->facility?->name
                            ?? 'Unknown Facility',

                        (int) $row->bookings,
                    ],
                ],
                null,
                "A{$facilityRow}"
            );

            $facilityRow++;
        }

        foreach (
            [
                $summary,
                $breakdowns,
                $facilities,
            ] as $sheet
        ) {
            $highestColumn =
                $sheet
                    ->getHighestColumn();

            foreach (
                range(
                    'A',
                    $highestColumn
                ) as $column
            ) {
                $sheet
                    ->getColumnDimension(
                        $column
                    )
                    ->setAutoSize(
                        true
                    );
            }

            $sheet
                ->getStyle(
                    'A1:'.
                    $highestColumn.
                    '1'
                )
                ->getFont()
                ->setBold(
                    true
                );

            $sheet
                ->freezePane(
                    'A2'
                );
        }

        /*
         * FAMS branded header styling.
         */
        $summary
            ->mergeCells(
                'A1:B1'
            );

        $summary
            ->mergeCells(
                'A2:B2'
            );

        $summary
            ->mergeCells(
                'A3:B3'
            );

        $summary
            ->getStyle(
                'A1:B3'
            )
            ->getAlignment()
            ->setHorizontal(
                Alignment::HORIZONTAL_CENTER
            );

        $summary
            ->getStyle(
                'A1:B3'
            )
            ->getFont()
            ->setBold(
                true
            );

        $summary
            ->getStyle(
                'A1:B1'
            )
            ->getFont()
            ->setSize(
                16
            );

        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => [
                    'rgb' => 'FFFFFF',
                ],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => '163B6D',
                ],
            ],
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => [
                        'rgb' => 'D1D5DB',
                    ],
                ],
            ],
        ];

        $summary
            ->getStyle(
                'A7:B7'
            )
            ->applyFromArray(
                $headerStyle
            );

        $breakdowns
            ->getStyle(
                'A1:C1'
            )
            ->applyFromArray(
                $headerStyle
            );

        $facilities
            ->getStyle(
                'A1:B1'
            )
            ->applyFromArray(
                $headerStyle
            );

        return $spreadsheet;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<int, array{0:string,1:string,2:int|float|string}>
     */
    private function summaryRows(
        array $data
    ): array {
        $labels = [
            'reservations' => 'Reservations',

            'approved_reservations' => 'Approved Reservations',

            'reservation_attendees' => 'Reservation Attendees',

            'visitors' => 'Visitors',

            'walk_in_visitors' => 'Walk-in Visitors',

            'average_visit_minutes' => 'Average Visit Duration (minutes)',

            'appointments' => 'Appointments',

            'documents' => 'Documents',

            'total_facilities' => 'Total Facilities',

            'available_facilities' => 'Available Facilities',

            'active_contracts' => 'Active Contracts',

            'contracts_expiring_soon' => 'Contracts Expiring Within 30 Days',

            'retention_issues' => 'Retention Issues',

            'pending_disposals' => 'Pending Disposals',

            'legal_action_required' => 'Legal Action Required',

            'legal_expiring_soon' => 'Legal Records Expiring Within 30 Days',
        ];

        $rows = [];

        foreach (
            $labels as $key => $label
        ) {
            $rows[] = [
                'Executive Summary',
                $label,
                $data['summary'][$key],
            ];
        }

        return $rows;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<int, array{0:string,1:string,2:int}>
     */
    private function breakdownRows(
        array $data
    ): array {
        $groups = [
            'Reservations by Status' => $data['reservationsByStatus'],

            'Visitors by Type' => $data['visitorsByType'],

            'Visitor Entry Type' => $data['visitorEntryType'],

            'Appointments by Status' => $data['appointmentsByStatus'],

            'Documents by Status' => $data['documentsByStatus'],

            'Documents by Category' => $data['documentsByCategory'],

            'Retention Compliance' => $data['retentionByCompliance'],

            'Legal Review Status' => $data['legalByReviewStatus'],

            'Contracts by Status' => $data['contractsByStatus'],

            'Contract Legal Review' => $data['contractsByLegalReview'],
        ];

        $rows = [];

        foreach (
            $groups as $group => $values
        ) {
            foreach (
                $values as $label => $value
            ) {
                $rows[] = [
                    $group,
                    str((string) $label)
                        ->headline()
                        ->toString(),
                    (int) $value,
                ];
            }
        }

        return $rows;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function filename(
        array $data,
        string $extension
    ): string {
        return sprintf(
            'fams-management-report-%s-to-%s.%s',
            $data['from']->toDateString(),
            $data['to']->toDateString(),
            $extension
        );
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function recordExport(
        Request $request,
        string $format,
        array $data
    ): void {
        $this->audit->log(
            $request->user(),
            'report_export',
            'reports',
            strtoupper($format).
                ' Management Report',
            null,
            sprintf(
                'Management report exported as %s for %s through %s.',
                strtoupper($format),
                $data['from']->toDateString(),
                $data['to']->toDateString()
            )
        );
    }
}
