<?php

namespace App\Observers;

use App\Models\Contract;
use App\Services\DocumentAutomationService;

class ContractObserver
{
    public function __construct(
        private DocumentAutomationService $documents
    ) {}

    public function created(
        Contract $contract
    ): void {
        $this->documents->syncContract($contract);
    }

    public function updated(
        Contract $contract
    ): void {
        $this->documents->syncContract($contract);
    }

    public function deleted(
        Contract $contract
    ): void {
        $this->documents
            ->archiveDeletedContract($contract);
    }
}