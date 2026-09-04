<?php

namespace App\Observers;

use App\Models\Reservation;
use App\Services\DocumentAutomationService;

class ReservationObserver
{
    public function __construct(
        private DocumentAutomationService $documents
    ) {}

    public function created(
        Reservation $reservation
    ): void {
        $this->documents->syncReservation(
            $reservation
        );
    }

    public function updated(
        Reservation $reservation
    ): void {
        $this->documents->syncReservation(
            $reservation
        );
    }

    public function deleted(
        Reservation $reservation
    ): void {
        $this->documents->archiveDeletedReservation(
            $reservation
        );
    }
}