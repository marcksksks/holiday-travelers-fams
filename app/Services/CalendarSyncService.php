<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

/**
 * Port of base44/functions/syncAppointmentToCalendar/entry.ts.
 * Uses a shared Google Calendar service-account/refresh-token connection
 * (configured once by sys_admin in .env), same as the original "shared
 * builder's calendar" connector model.
 */
class CalendarSyncService
{
    public function __construct(private AuditService $audit) {}

    public function sync(User $user, Visitor $visitor): array
    {
        $appointment = $visitor->appointment_id ? Appointment::find($visitor->appointment_id) : null;

        $accessToken = $this->accessToken();
        if (! $accessToken) {
            throw ValidationException::withMessages(['calendar' => 'Google Calendar is not connected. Configure GOOGLE_CALENDAR_* in .env.']);
        }

        $timezone = config('services.google_calendar.timezone', 'Asia/Singapore');
        $summary = "Visitor: {$visitor->full_name}";
        $descriptionParts = [
            'Organization: '.($visitor->organization ?: '—'),
            'Visitor type: '.($visitor->visitor_type ?: '—'),
            'Purpose: '.($visitor->purpose ?: '—'),
            'Host: '.($visitor->host_name ?: $visitor->host_email ?: '—'),
        ];
        if ($appointment?->facility_name) {
            $descriptionParts[] = "Room: {$appointment->facility_name}";
        }
        if ($visitor->notes) {
            $descriptionParts[] = "Notes: {$visitor->notes}";
        }

        if ($appointment && $appointment->date && $appointment->start_time) {
            $endTime = $appointment->end_time ?: $appointment->start_time;
            $date = $appointment->date->toDateString();
            $start = ['dateTime' => "{$date}T{$appointment->start_time}:00", 'timeZone' => $timezone];
            $end = ['dateTime' => "{$date}T{$endTime}:00", 'timeZone' => $timezone];
        } else {
            $today = now()->toDateString();
            $start = ['date' => $today];
            $end = ['date' => $today];
        }

        $attendees = [];
        if ($visitor->email) {
            $attendees[] = ['email' => $visitor->email];
        }
        if ($visitor->host_email && $visitor->host_email !== $visitor->email) {
            $attendees[] = ['email' => $visitor->host_email];
        }

        $response = Http::withToken($accessToken)
            ->timeout(15)->retry(2, 200)->post('https://www.googleapis.com/calendar/v3/calendars/'.rawurlencode(config('services.google_calendar.calendar_id', 'primary')).'/events', array_filter([
                'summary' => $summary,
                'description' => implode("\n", $descriptionParts),
                'start' => $start,
                'end' => $end,
                'location' => $appointment->facility_name ?? null,
                'attendees' => $attendees ?: null,
            ]));

        if ($response->failed()) {
            throw ValidationException::withMessages(['calendar' => 'Google Calendar error: '.$response->body()]);
        }

        $created = $response->json();

        $this->audit->log($user, 'create', 'visitors', "Calendar event • {$visitor->full_name}", (string) $visitor->id,
            "Synced visitor appointment to Google Calendar ({$created['id']})");

        return ['event_id' => $created['id'] ?? null, 'html_link' => $created['htmlLink'] ?? null];
    }

    private function accessToken(): ?string
    {
        $refreshToken = config('services.google_calendar.refresh_token');
        $clientId = config('services.google_calendar.client_id');
        $clientSecret = config('services.google_calendar.client_secret');
        if (! $refreshToken || ! $clientId || ! $clientSecret) {
            return null;
        }
        $response = Http::asForm()->timeout(15)->post('https://oauth2.googleapis.com/token', [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token',
        ]);
        return $response->successful() ? $response->json('access_token') : null;
    }
}
