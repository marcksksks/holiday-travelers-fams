<?php

namespace App\Services;

use App\Models\ArchiveDocument;
use App\Models\Contract;
use App\Models\DocumentContainer;
use App\Models\LegalRecord;
use App\Models\Reservation;
use App\Models\Visitor;
use Illuminate\Support\Str;

class DocumentAutomationService
{
    public function ensureBaseContainers(): void
    {
        $containers = [
            ['name' => 'Administrative', 'slug' => 'administrative', 'module' => 'administrative'],
            ['name' => 'Facilities', 'slug' => 'facilities', 'module' => 'facilities'],
            ['name' => 'Visitors', 'slug' => 'visitors', 'module' => 'visitors'],
            ['name' => 'Legal', 'slug' => 'legal', 'module' => 'legal'],
            ['name' => 'Contracts', 'slug' => 'contracts', 'module' => 'contracts'],
            ['name' => 'Compliance', 'slug' => 'compliance', 'module' => 'compliance'],
            ['name' => 'Partnerships', 'slug' => 'partnerships', 'module' => 'partnerships'],
            ['name' => 'Financial', 'slug' => 'financial', 'module' => 'financial'],
            ['name' => 'Operational', 'slug' => 'operational', 'module' => 'operational'],
            ['name' => 'Other', 'slug' => 'other', 'module' => 'other'],
        ];

        foreach ($containers as $container) {
            DocumentContainer::firstOrCreate(
                ['path' => $container['slug']],
                [
                    'parent_id' => null,
                    'name' => $container['name'],
                    'slug' => $container['slug'],
                    'module' => $container['module'],
                    'is_system' => true,
                ]
            );
        }
    }

    public function syncReservation(
        Reservation $reservation
    ): ArchiveDocument {
        $reservation->refresh();

        $date = $reservation->date
            ?? $reservation->created_at
            ?? now();

        $container = $this->ensurePath([
            [
                'name' => 'Facilities',
                'slug' => 'facilities',
                'module' => 'facilities',
            ],
            [
                'name' => 'Reservations',
                'slug' => 'reservations',
                'module' => 'reservations',
            ],
            [
                'name' => $date->format('Y'),
                'slug' => $date->format('Y'),
                'module' => 'reservations',
            ],
            [
                'name' => $date->format('F'),
                'slug' => Str::slug($date->format('F')),
                'module' => 'reservations',
            ],
        ]);

        return $this->upsertSystemDocument(
            'reservation:'.$reservation->id,
            [
                'title' =>
                    'Reservation #'.$reservation->id.
                    ' - '.$reservation->facility_name,

                'description' =>
                    'System-linked facility reservation record. '.
                    'The Facilities Reservation module remains the source of truth.',

                'category' => 'operational',
                'department' => 'Facilities',

                'owner_email' =>
                    $reservation->requester_email,

                'confidentiality' => 'restricted',
                'document_date' => $date,
                'container_id' => $container->id,

                'linked_contract_id' => null,
                'linked_legal_record_id' => null,
                'linked_reservation_id' => $reservation->id,
                'linked_visitor_id' => null,

                'source_module' => 'reservations',
                'uploaded_by_email' => 'system',
                'is_system_generated' => true,
            ],
            'Synchronized from Facilities Reservation.'
        );
    }

    public function syncVisitor(
        Visitor $visitor
    ): ArchiveDocument {
        $visitor->refresh();

        $date = $visitor->check_in_at
            ?? $visitor->created_at
            ?? now();

        [$typeName, $typeSlug] =
            $this->visitorTypeFolder(
                $visitor->visitor_type
            );

        $container = $this->ensurePath([
            [
                'name' => 'Visitors',
                'slug' => 'visitors',
                'module' => 'visitors',
            ],
            [
                'name' => $typeName,
                'slug' => $typeSlug,
                'module' => 'visitors',
            ],
            [
                'name' => $date->format('Y'),
                'slug' => $date->format('Y'),
                'module' => 'visitors',
            ],
            [
                'name' => $date->format('F'),
                'slug' => Str::slug($date->format('F')),
                'module' => 'visitors',
            ],
        ]);

        return $this->upsertSystemDocument(
            'visitor:'.$visitor->id,
            [
                'title' =>
                    'Visitor #'.$visitor->id.
                    ' - '.$visitor->full_name,

                'description' =>
                    'System-linked visitor record. '.
                    'The Visitor Management module remains the source of truth.',

                'category' => 'operational',
                'department' => 'Visitor Management',

                'owner_email' =>
                    $visitor->host_email,

                'confidentiality' => 'restricted',
                'document_date' => $date,
                'container_id' => $container->id,

                'linked_contract_id' => null,
                'linked_legal_record_id' => null,
                'linked_reservation_id' => null,
                'linked_visitor_id' => $visitor->id,

                'source_module' => 'visitors',
                'uploaded_by_email' => 'system',
                'is_system_generated' => true,
            ],
            'Synchronized from Visitor Management.'
        );
    }

    public function syncContract(
        Contract $contract
    ): ArchiveDocument {
        $contract->refresh();

        [$typeName, $typeSlug] =
            $this->contractTypeFolder(
                $contract->contract_type
            );

        $container = $this->ensurePath([
            [
                'name' => 'Contracts',
                'slug' => 'contracts',
                'module' => 'contracts',
            ],
            [
                'name' => $typeName,
                'slug' => $typeSlug,
                'module' => 'contracts',
            ],
        ]);

        $number = $contract->contract_number
            ?: 'Contract #'.$contract->id;

        return $this->upsertSystemDocument(
            'contract:'.$contract->id,
            [
                'title' =>
                    $number.' - '.$contract->title,

                'description' =>
                    'System-linked contract record. '.
                    'The Contract Management module remains the source of truth.',

                'category' => 'contract',
                'department' => 'Contract Management',

                'owner_email' =>
                    $contract->responsible_officer_email,

                'confidentiality' => 'confidential',

                'document_date' =>
                    $contract->start_date
                    ?? $contract->created_at,

                'expiration_date' =>
                    $contract->end_date,

                'version' =>
                    (int) ($contract->version ?? 1),

                'file_uri' =>
                    $contract->file_uri,

                'file_name' =>
                    $contract->file_name,

                'container_id' =>
                    $container->id,

                'linked_contract_id' =>
                    $contract->id,

                'linked_legal_record_id' => null,
                'linked_reservation_id' => null,
                'linked_visitor_id' => null,

                'source_module' => 'contracts',
                'uploaded_by_email' => 'system',
                'is_system_generated' => true,
            ],
            'Synchronized from Contract Management.'
        );
    }

    public function syncLegalRecord(
        LegalRecord $legal
    ): ArchiveDocument {
        $legal->refresh();

        [$typeName, $typeSlug] =
            $this->legalTypeFolder(
                $legal->record_type
            );

        $date = $legal->issue_date
            ?? $legal->created_at
            ?? now();

        $container = $this->ensurePath([
            [
                'name' => 'Legal',
                'slug' => 'legal',
                'module' => 'legal',
            ],
            [
                'name' => $typeName,
                'slug' => $typeSlug,
                'module' => 'legal',
            ],
            [
                'name' => $date->format('Y'),
                'slug' => $date->format('Y'),
                'module' => 'legal',
            ],
        ]);

        $reference = $legal->reference_number
            ?: 'Legal #'.$legal->id;

        $category = match ($typeSlug) {
            'permit', 'permits' => 'permit',
            'license', 'licenses' => 'license',
            default => 'legal',
        };

        return $this->upsertSystemDocument(
            'legal:'.$legal->id,
            [
                'title' =>
                    $reference.' - '.$legal->title,

                'description' =>
                    'System-linked legal record. '.
                    'The Legal Management module remains the source of truth.',

                'category' => $category,
                'department' => 'Legal Management',

                'owner_email' =>
                    $legal->responsible_officer_email,

                'confidentiality' => 'confidential',

                'document_date' =>
                    $date,

                'expiration_date' =>
                    $legal->expiration_date,

                'file_uri' =>
                    $legal->file_uri,

                'file_name' =>
                    $legal->file_name,

                'container_id' =>
                    $container->id,

                'linked_contract_id' => null,

                'linked_legal_record_id' =>
                    $legal->id,

                'linked_reservation_id' => null,
                'linked_visitor_id' => null,

                'source_module' => 'legal',
                'uploaded_by_email' => 'system',
                'is_system_generated' => true,
            ],
            'Synchronized from Legal Management.'
        );
    }

    public function archiveDeletedReservation(
        Reservation $reservation
    ): void {
        $this->archiveDeletedSource(
            'reservation:'.$reservation->id,
            'Source reservation was deleted.'
        );
    }

    public function archiveDeletedVisitor(
        Visitor $visitor
    ): void {
        $this->archiveDeletedSource(
            'visitor:'.$visitor->id,
            'Source visitor record was deleted.'
        );
    }

    public function archiveDeletedContract(
        Contract $contract
    ): void {
        $this->archiveDeletedSource(
            'contract:'.$contract->id,
            'Source contract was deleted.'
        );
    }

    public function archiveDeletedLegalRecord(
        LegalRecord $legal
    ): void {
        $this->archiveDeletedSource(
            'legal:'.$legal->id,
            'Source legal record was deleted.'
        );
    }

    private function upsertSystemDocument(
        string $systemKey,
        array $data,
        string $note
    ): ArchiveDocument {
        $document = ArchiveDocument::where(
            'system_key',
            $systemKey
        )->first();

        $data['system_key'] = $systemKey;

        if (! $document) {
            $data['status'] = 'active';

            if (! isset($data['version'])) {
                $data['version'] = 1;
            }

            $data['history'] = [[
                'version' => $data['version'] ?? 1,
                'action' => 'system_create',
                'by' => 'system',
                'at' => now()->toISOString(),
                'note' => $note,
            ]];

            return ArchiveDocument::create($data);
        }

        /*
         * Important:
         * Status is intentionally not synchronized here.
         * If a records officer archives this record,
         * source-module updates must not restore it automatically.
         */
        $document->fill($data);

        if ($document->isDirty()) {
            $history = $document->history ?? [];

            $history[] = [
                'version' =>
                    $data['version']
                    ?? $document->version
                    ?? 1,

                'action' => 'system_sync',
                'by' => 'system',
                'at' => now()->toISOString(),
                'note' => $note,
            ];

            $document->history = $history;

            $document->save();
        }

        return $document->fresh();
    }

    private function archiveDeletedSource(
        string $systemKey,
        string $note
    ): void {
        $document = ArchiveDocument::where(
            'system_key',
            $systemKey
        )->first();

        if (! $document) {
            return;
        }

        $history = $document->history ?? [];

        $history[] = [
            'version' =>
                $document->version ?? 1,

            'action' => 'system_archive',
            'by' => 'system',
            'at' => now()->toISOString(),
            'note' => $note,
        ];

        $document->update([
            'status' => 'archived',
            'history' => $history,
        ]);
    }

    private function visitorTypeFolder(
        ?string $type
    ): array {
        $slug = Str::slug($type ?: 'other');

        $name = match ($slug) {
            'government' => 'Government',
            'guest' => 'Guests',
            'employee' => 'Employees',
            'vendor' => 'Vendors',
            'supplier' => 'Suppliers',
            'partner' => 'Partners',
            default => Str::headline(
                $type ?: 'Other'
            ),
        };

        return [$name, $slug ?: 'other'];
    }

    private function contractTypeFolder(
        ?string $type
    ): array {
        $slug = Str::slug($type ?: 'other');

        $name = match ($slug) {
            'hotel' => 'Hotels',
            'transport',
            'transportation' => 'Transportation',
            'supplier' => 'Suppliers',
            'vendor' => 'Vendors',
            'partnership' => 'Partnerships',
            'tour-operator',
            'tour_operator' => 'Tour Operators',
            'service' => 'Service Agreements',
            'sla' => 'Service Level Agreements',
            'lease' => 'Leases',
            'nda' => 'NDAs',
            default => Str::headline(
                $type ?: 'Other'
            ),
        };

        return [$name, $slug ?: 'other'];
    }

    private function legalTypeFolder(
        ?string $type
    ): array {
        $slug = Str::slug($type ?: 'other');

        $name = match ($slug) {
            'permit' => 'Permits',
            'license' => 'Licenses',
            'requirement' => 'Requirements',
            'notice' => 'Notices',
            'litigation' => 'Litigation',
            'compliance' => 'Compliance',
            default => Str::headline(
                $type ?: 'Other'
            ),
        };

        return [$name, $slug ?: 'other'];
    }

    private function ensurePath(
        array $segments
    ): DocumentContainer {
        $parent = null;
        $parts = [];

        foreach ($segments as $segment) {
            $parts[] = $segment['slug'];

            $path = implode('/', $parts);

            $container = DocumentContainer::firstOrCreate(
                [
                    'path' => $path,
                ],
                [
                    'parent_id' => $parent?->id,
                    'name' => $segment['name'],
                    'slug' => $segment['slug'],
                    'module' => $segment['module'] ?? null,
                    'is_system' => true,
                ]
            );

            $parent = $container;
        }

        return $parent;
    }
}