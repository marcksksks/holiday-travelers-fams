<?php

namespace App\Observers;

use App\Models\LegalRecord;
use App\Services\DocumentAutomationService;

class LegalRecordObserver
{
    public function __construct(
        private DocumentAutomationService $documents
    ) {}

    public function created(
        LegalRecord $legal
    ): void {
        $this->documents
            ->syncLegalRecord($legal);
    }

    public function updated(
        LegalRecord $legal
    ): void {
        $this->documents
            ->syncLegalRecord($legal);
    }

    public function deleted(
        LegalRecord $legal
    ): void {
        $this->documents
            ->archiveDeletedLegalRecord($legal);
    }
}