<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">

    <title>
        FAMS Management Report
    </title>

    <style>
        @page {
            margin: 18mm 14mm 18mm 14mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #1f2937;
            background: #ffffff;
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10px;
            line-height: 1.45;
        }

        .toolbar {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 18px;
            margin-bottom: 20px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
        }

        .toolbar a,
        .toolbar button {
            display: inline-block;
            padding: 9px 14px;
            border: 0;
            border-radius: 7px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
        }

        .toolbar a {
            background: #ffffff;
            color: #163b6d;
            border: 1px solid #d1d5db;
        }

        .toolbar button {
            background: #163b6d;
            color: #ffffff;
        }

        .report {
            width: 100%;
        }

        .brand {
            width: 100%;
            margin-bottom: 14px;
            border-bottom: 2px solid #163b6d;
            padding-bottom: 12px;
        }

        .brand td {
            vertical-align: middle;
        }

        .logo {
            width: 54px;
            height: 54px;
            object-fit: contain;
        }

        .organization {
            margin: 0;
            color: #163b6d;
            font-size: 18px;
            font-weight: 700;
        }

        .system-name {
            margin: 2px 0 0;
            color: #475569;
            font-size: 10px;
        }

        .report-title {
            margin: 18px 0 4px;
            color: #163b6d;
            font-size: 20px;
            text-align: center;
        }

        .report-subtitle {
            margin: 0 0 18px;
            color: #64748b;
            text-align: center;
        }

        .meta {
            width: 100%;
            margin-bottom: 18px;
            border-collapse: collapse;
        }

        .meta td {
            padding: 5px 7px;
            border: 1px solid #e5e7eb;
        }

        .meta-label {
            width: 28%;
            color: #475569;
            background: #f8fafc;
            font-weight: 700;
        }

        h2 {
            margin: 18px 0 8px;
            color: #163b6d;
            font-size: 13px;
            border-bottom: 1px solid #dbe4ef;
            padding-bottom: 5px;
        }

        table.data {
            width: 100%;
            margin-bottom: 14px;
            border-collapse: collapse;
        }

        table.data th {
            padding: 6px 7px;
            color: #ffffff;
            background: #163b6d;
            border: 1px solid #163b6d;
            text-align: left;
            font-size: 9px;
        }

        table.data td {
            padding: 6px 7px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
        }

        table.data tr:nth-child(even) td {
            background: #f8fafc;
        }

        .grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
        }

        .grid > tbody > tr > td {
            width: 50%;
            vertical-align: top;
        }

        .section-card {
            border: 1px solid #e5e7eb;
        }

        .section-card-title {
            padding: 7px 8px;
            color: #163b6d;
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
            font-weight: 700;
        }

        .footer {
            margin-top: 20px;
            padding-top: 8px;
            color: #64748b;
            border-top: 1px solid #e5e7eb;
            font-size: 8px;
            text-align: center;
        }

        .scope {
            margin-top: 14px;
            padding: 8px 10px;
            background: #f8fafc;
            border-left: 3px solid #6fa9e6;
            color: #475569;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            .report {
                width: 100%;
            }
        }
    </style>
</head>

<body>

@if (! $pdfMode)

    <div class="toolbar no-print">

        <a
            href="{{ route('reports.index', [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
                'facility_sort' => $facilitySort,
            ]) }}">

            Back to Reports

        </a>

        <button
            type="button"
            onclick="window.print()">

            Print Report

        </button>

    </div>

@endif


<div class="report">

    <table class="brand">

        <tr>

            <td style="width: 66px;">

                <img
                    class="logo"
                    src="{{ $pdfMode
                        ? public_path('images/holiday-travelers-mark.png')
                        : asset('images/holiday-travelers-mark.png') }}"
                    alt="Holiday Travelers Inc.">

            </td>

            <td>

                <p class="organization">
                    HOLIDAY TRAVELERS INC.
                </p>

                <p class="system-name">
                    Facilities &amp; Administrative Management System
                </p>

            </td>

        </tr>

    </table>


    <h1 class="report-title">
        Management Report
    </h1>

    <p class="report-subtitle">
        Operational Intelligence &amp; Governance Summary
    </p>


    <table class="meta">

        <tr>
            <td class="meta-label">
                Reporting Period
            </td>

            <td>
                {{ $from->format('F d, Y') }}
                &ndash;
                {{ $to->format('F d, Y') }}
            </td>
        </tr>

        <tr>
            <td class="meta-label">
                Generated
            </td>

            <td>
                {{ now()->format('F d, Y h:i A') }}
                ({{ config('app.timezone') }})
            </td>
        </tr>

        <tr>
            <td class="meta-label">
                Facility Sorting
            </td>

            <td>
                {{ str($facilitySort)->replace('_', ' ')->headline() }}
            </td>
        </tr>

    </table>


    <h2>
        Executive Summary
    </h2>

    <table class="data">

        <thead>
            <tr>
                <th>Metric</th>
                <th style="width: 24%;">
                    Value
                </th>
            </tr>
        </thead>

        <tbody>

            @foreach ([
                'Reservations' => $summary['reservations'],
                'Approved Reservations' => $summary['approved_reservations'],
                'Reservation Attendees' => $summary['reservation_attendees'],
                'Visitors' => $summary['visitors'],
                'Walk-in Visitors' => $summary['walk_in_visitors'],
                'Average Visit Duration (minutes)' => $summary['average_visit_minutes'],
                'Appointments' => $summary['appointments'],
                'Documents' => $summary['documents'],
                'Total Facilities' => $summary['total_facilities'],
                'Available Facilities' => $summary['available_facilities'],
                'Active Contracts' => $summary['active_contracts'],
                'Contracts Expiring Within 30 Days' => $summary['contracts_expiring_soon'],
                'Retention Issues' => $summary['retention_issues'],
                'Pending Disposals' => $summary['pending_disposals'],
                'Legal Action Required' => $summary['legal_action_required'],
                'Legal Records Expiring Within 30 Days' => $summary['legal_expiring_soon'],
            ] as $label => $value)

                <tr>
                    <td>
                        {{ $label }}
                    </td>

                    <td>
                        {{ number_format((float) $value, is_float($value) ? 1 : 0) }}
                    </td>
                </tr>

            @endforeach

        </tbody>

    </table>


    <h2>
        Operational Breakdowns
    </h2>

    @php
        $groups = [
            'Reservations by Status' => $reservationsByStatus,
            'Visitors by Type' => $visitorsByType,
            'Visitor Entry Type' => $visitorEntryType,
            'Appointments by Status' => $appointmentsByStatus,
            'Documents by Status' => $documentsByStatus,
            'Documents by Category' => $documentsByCategory,
            'Retention Compliance' => $retentionByCompliance,
            'Legal Review Status' => $legalByReviewStatus,
            'Contracts by Status' => $contractsByStatus,
            'Contract Legal Review' => $contractsByLegalReview,
        ];
    @endphp

    @foreach ($groups as $group => $values)

        <div class="section-card">

            <div class="section-card-title">
                {{ $group }}
            </div>

            <table class="data" style="margin-bottom: 10px;">

                <thead>
                    <tr>
                        <th>Classification</th>
                        <th style="width: 24%;">
                            Count
                        </th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($values as $label => $value)

                        <tr>
                            <td>
                                {{ str((string) $label)->headline() }}
                            </td>

                            <td>
                                {{ number_format((int) $value) }}
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="2">
                                No records available.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    @endforeach


    <h2>
        Facility Utilization
    </h2>

    <table class="data">

        <thead>
            <tr>
                <th>Facility</th>
                <th style="width: 28%;">
                    Approved Bookings
                </th>
            </tr>
        </thead>

        <tbody>

            @forelse ($facilityUtilization as $row)

                <tr>
                    <td>
                        {{ $row->facility?->name ?? 'Unknown Facility' }}
                    </td>

                    <td>
                        {{ number_format((int) $row->bookings) }}
                    </td>
                </tr>

            @empty

                <tr>
                    <td colspan="2">
                        No approved facility bookings for this reporting period.
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>


    <div class="scope">

        <strong>Report scope:</strong>

        Reservation, visitor, appointment, document, and facility-utilization
        statistics follow the selected reporting period. Contract, legal,
        and retention compliance indicators represent the current portfolio.

    </div>


    <div class="footer">

        Holiday Travelers Inc. &mdash;
        Facilities &amp; Administrative Management System &mdash;
        Generated {{ now()->format('Y-m-d H:i:s') }}

    </div>

</div>

</body>
</html>