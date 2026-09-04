<?php

namespace App\Observers;

use App\Models\Visitor;
use App\Services\DocumentAutomationService;

class VisitorObserver
{
    public function __construct(
        private DocumentAutomationService $documents
    ) {}

    public function created(
        Visitor $visitor
    ): void {
        $this->documents->syncVisitor($visitor);
    }

    public function updated(
        Visitor $visitor
    ): void {
        $this->documents->syncVisitor($visitor);
    }

    public function deleted(
        Visitor $visitor
    ): void {
        $this->documents
            ->archiveDeletedVisitor($visitor);
    }
}