<?php

namespace App\Services;

use App\Models\Facility;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Throwable;

class FacilityImportService
{
    private const MAX_ROWS = 500;

    private const REQUIRED_HEADERS = [
        'name',
        'facility_type',
        'status',
    ];

    private const ALLOWED_HEADERS = [
        'name',
        'description',
        'location',
        'capacity',
        'facility_type',
        'status',
        'equipment',
    ];

    private const FACILITY_TYPES = [
        'conference_room',
        'meeting_room',
        'training_room',
        'function_room',
        'vehicle',
        'other',
    ];

    private const STATUSES = [
        'available',
        'maintenance',
        'unavailable',
        'archived',
    ];

    public function __construct(
        private AuditService $audit
    ) {}

    /**
     * @return array{
     *     format:string,
     *     imported:int
     * }
     */
    public function import(
        UploadedFile $file,
        User $actor
    ): array {
        $extension =
            strtolower(
                $file->getClientOriginalExtension()
            );

        if (! in_array(
            $extension,
            [
                'csv',
                'xlsx',
                'json',
            ],
            true
        )) {
            throw ValidationException::withMessages([
                'import_file' => [
                    'Unsupported import file extension.',
                ],
            ]);
        }

        try {
            $rows =
                match ($extension) {
                    'csv' => $this->spreadsheetRows(
                        $file,
                        new Csv
                    ),

                    'xlsx' => $this->spreadsheetRows(
                        $file,
                        new Xlsx
                    ),

                    'json' => $this->jsonRows(
                        $file
                    ),
                };
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Throwable) {
            throw ValidationException::withMessages([
                'import_file' => [
                    'The uploaded file could not be parsed. Verify that it is a valid '.
                    strtoupper($extension).
                    ' file and try again.',
                ],
            ]);
        }

        if ($rows === []) {
            throw ValidationException::withMessages([
                'import_file' => [
                    'The import file does not contain any facility records.',
                ],
            ]);
        }

        if (count($rows) > self::MAX_ROWS) {
            throw ValidationException::withMessages([
                'import_file' => [
                    'A single import can contain at most '.
                    self::MAX_ROWS.
                    ' facility records.',
                ],
            ]);
        }

        $validatedRows =
            $this->validateRows(
                $rows
            );

        DB::transaction(
            function () use (
                $validatedRows,
                $actor,
                $extension
            ): void {
                foreach (
                    $validatedRows as $row
                ) {
                    Facility::create(
                        $row + [
                            'updated_by_email' => $actor->email,
                        ]
                    );
                }

                $this->audit->log(
                    $actor,
                    'bulk_import',
                    'facilities',
                    'Facility Bulk Import',
                    null,
                    sprintf(
                        '%d facilities imported from %s.',
                        count($validatedRows),
                        strtoupper($extension)
                    )
                );
            }
        );

        return [
            'format' => strtoupper($extension),

            'imported' => count($validatedRows),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function spreadsheetRows(
        UploadedFile $file,
        Csv|Xlsx $reader
    ): array {
        $reader->setReadDataOnly(
            true
        );

        $spreadsheet =
            $reader->load(
                $file->getRealPath()
            );

        try {
            $raw =
                $spreadsheet
                    ->getActiveSheet()
                    ->toArray(
                        null,
                        true,
                        true,
                        false
                    );
        } finally {
            $spreadsheet
                ->disconnectWorksheets();
        }

        if (count($raw) < 2) {
            throw ValidationException::withMessages([
                'import_file' => [
                    'The spreadsheet must contain a header row and at least one data row.',
                ],
            ]);
        }

        $headers =
            array_map(
                fn ($value) => $this->normalizeHeader(
                    $value
                ),
                array_shift($raw)
            );

        $this->validateHeaders(
            $headers
        );

        $rows = [];

        foreach (
            $raw as $index => $values
        ) {
            $isBlank =
                collect($values)
                    ->every(
                        fn ($value) => $value === null
                            ||
                            trim(
                                (string) $value
                            ) === ''
                    );

            if ($isBlank) {
                continue;
            }

            $row = [];

            foreach (
                $headers as $column => $header
            ) {
                if ($header === '') {
                    continue;
                }

                $row[$header] =
                    $values[$column]
                    ?? null;
            }

            $row['_row_number'] =
                $index + 2;

            $rows[] =
                $row;
        }

        return $rows;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function jsonRows(
        UploadedFile $file
    ): array {
        $contents =
            file_get_contents(
                $file->getRealPath()
            );

        if ($contents === false) {
            throw ValidationException::withMessages([
                'import_file' => [
                    'The JSON file could not be read.',
                ],
            ]);
        }

        try {
            $decoded =
                json_decode(
                    $contents,
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                );
        } catch (Throwable) {
            throw ValidationException::withMessages([
                'import_file' => [
                    'The uploaded JSON file is malformed.',
                ],
            ]);
        }

        if (
            is_array($decoded)
            &&
            array_key_exists(
                'facilities',
                $decoded
            )
        ) {
            $decoded =
                $decoded['facilities'];
        }

        if (
            ! is_array($decoded)
            ||
            ! array_is_list($decoded)
        ) {
            throw ValidationException::withMessages([
                'import_file' => [
                    'JSON imports must contain an array of facility objects or a "facilities" array.',
                ],
            ]);
        }

        $rows = [];

        foreach (
            $decoded as $index => $row
        ) {
            if (! is_array($row)) {
                throw ValidationException::withMessages([
                    'import_file' => [
                        'JSON record '.
                        ($index + 1).
                        ' must be an object.',
                    ],
                ]);
            }

            $normalized = [];

            foreach (
                $row as $key => $value
            ) {
                $header =
                    $this->normalizeHeader(
                        $key
                    );

                if ($header !== '') {
                    $normalized[$header] =
                        $value;
                }
            }

            $normalized['_row_number'] =
                $index + 1;

            $rows[] =
                $normalized;
        }

        return $rows;
    }

    /**
     * @param  array<int, string>  $headers
     */
    private function validateHeaders(
        array $headers
    ): void {
        $usableHeaders =
            array_values(
                array_filter(
                    $headers
                )
            );

        $missing =
            array_values(
                array_diff(
                    self::REQUIRED_HEADERS,
                    $usableHeaders
                )
            );

        if ($missing !== []) {
            throw ValidationException::withMessages([
                'import_file' => [
                    'Missing required column(s): '.
                    implode(
                        ', ',
                        $missing
                    ).
                    '.',
                ],
            ]);
        }

        $unsupported =
            array_values(
                array_diff(
                    $usableHeaders,
                    self::ALLOWED_HEADERS
                )
            );

        if ($unsupported !== []) {
            throw ValidationException::withMessages([
                'import_file' => [
                    'Unsupported column(s): '.
                    implode(
                        ', ',
                        $unsupported
                    ).
                    '.',
                ],
            ]);
        }

        if (
            count($usableHeaders)
            !==
            count(
                array_unique(
                    $usableHeaders
                )
            )
        ) {
            throw ValidationException::withMessages([
                'import_file' => [
                    'The import file contains duplicate column names.',
                ],
            ]);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<int, array<string, mixed>>
     */
    private function validateRows(
        array $rows
    ): array {
        $validated = [];
        $errors = [];
        $seenNames = [];

        foreach (
            $rows as $index => $row
        ) {
            $rowNumber =
                (int) (
                    $row['_row_number']
                    ?? $index + 1
                );

            unset(
                $row['_row_number']
            );

            $row =
                $this->normalizeRow(
                    $row
                );

            $validator =
                Validator::make(
                    $row,
                    [
                        'name' => [
                            'required',
                            'string',
                            'max:255',
                        ],

                        'description' => [
                            'nullable',
                            'string',
                        ],

                        'location' => [
                            'nullable',
                            'string',
                            'max:255',
                        ],

                        'capacity' => [
                            'nullable',
                            'integer',
                            'min:0',
                        ],

                        'facility_type' => [
                            'required',
                            'in:'.
                            implode(
                                ',',
                                self::FACILITY_TYPES
                            ),
                        ],

                        'status' => [
                            'required',
                            'in:'.
                            implode(
                                ',',
                                self::STATUSES
                            ),
                        ],

                        'equipment' => [
                            'nullable',
                            'array',
                        ],

                        'equipment.*' => [
                            'string',
                            'max:255',
                        ],
                    ]
                );

            if ($validator->fails()) {
                foreach (
                    $validator
                        ->errors()
                        ->all() as $message
                ) {
                    $errors[] =
                        "Row {$rowNumber}: {$message}";
                }

                continue;
            }

            $data =
                $validator->validated();

            $nameKey =
                strtolower(
                    trim(
                        $data['name']
                    )
                );

            if (
                array_key_exists(
                    $nameKey,
                    $seenNames
                )
            ) {
                $errors[] =
                    "Row {$rowNumber}: facility name \"".
                    $data['name'].
                    '" is duplicated in the import file.';

                continue;
            }

            $seenNames[$nameKey] =
                true;

            $exists =
                Facility::query()
                    ->whereRaw(
                        'LOWER(name) = ?',
                        [
                            $nameKey,
                        ]
                    )
                    ->exists();

            if ($exists) {
                $errors[] =
                    "Row {$rowNumber}: facility \"".
                    $data['name'].
                    '" already exists.';

                continue;
            }

            $validated[] =
                $data;
        }

        if ($errors !== []) {
            throw ValidationException::withMessages([
                'import_file' => array_slice(
                    $errors,
                    0,
                    25
                ),
            ]);
        }

        return $validated;
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    private function normalizeRow(
        array $row
    ): array {
        foreach (
            self::ALLOWED_HEADERS as $header
        ) {
            if (! array_key_exists(
                $header,
                $row
            )) {
                $row[$header] =
                    null;
            }
        }

        $row['name'] =
            trim(
                (string) (
                    $row['name']
                    ?? ''
                )
            );

        $row['description'] =
            $this->nullableString(
                $row['description']
            );

        $row['location'] =
            $this->nullableString(
                $row['location']
            );

        $row['facility_type'] =
            Str::of(
                (string) (
                    $row['facility_type']
                    ?? ''
                )
            )
                ->trim()
                ->lower()
                ->replace(
                    [
                        ' ',
                        '-',
                    ],
                    '_'
                )
                ->toString();

        $row['status'] =
            Str::of(
                (string) (
                    $row['status']
                    ?? ''
                )
            )
                ->trim()
                ->lower()
                ->toString();

        if (
            $row['capacity'] === ''
            ||
            $row['capacity'] === null
        ) {
            $row['capacity'] =
                null;
        }

        $equipment =
            $row['equipment'];

        if (is_string($equipment)) {
            $equipment =
                preg_split(
                    '/[;|]+/',
                    $equipment
                ) ?: [];
        }

        if (! is_array($equipment)) {
            $equipment = [];
        }

        $row['equipment'] =
            collect($equipment)
                ->map(
                    fn ($item) => trim(
                        (string) $item
                    )
                )
                ->filter()
                ->unique()
                ->values()
                ->all();

        return array_intersect_key(
            $row,
            array_flip(
                self::ALLOWED_HEADERS
            )
        );
    }

    private function normalizeHeader(
        mixed $value
    ): string {
        return Str::of(
            (string) $value
        )
            ->trim()
            ->lower()
            ->replace(
                [
                    ' ',
                    '-',
                ],
                '_'
            )
            ->replaceMatches(
                '/[^a-z0-9_]/',
                ''
            )
            ->toString();
    }

    private function nullableString(
        mixed $value
    ): ?string {
        if ($value === null) {
            return null;
        }

        $value =
            trim(
                (string) $value
            );

        return $value === ''
            ? null
            : $value;
    }
}
