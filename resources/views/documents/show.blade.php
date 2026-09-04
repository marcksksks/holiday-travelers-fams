@extends('layouts.app')

@section('title', 'Document Details')

@section('content')

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">

        <div>

            <a
                href="{{ route('documents.index', ['container' => $document->container_id]) }}"
                class="mb-3 inline-flex items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-primary">

                <span>&larr;</span>
                Back to Document Management

            </a>

            <div class="mb-2 h-1 w-12 rounded-full bg-secondary"></div>

            <div class="flex flex-wrap items-center gap-3">

                <h1 class="font-heading text-2xl font-bold text-primary">
                    {{ $document->title }}
                </h1>

                @if ($document->is_system_generated)

                    <span class="rounded-full bg-accent/10 px-3 py-1 text-xs font-semibold text-accent">
                        System Record
                    </span>

                @else

                    <span class="rounded-full bg-primary/5 px-3 py-1 text-xs font-semibold text-primary">
                        Version {{ $document->version ?? 1 }}
                    </span>

                @endif

            </div>

            <p class="mt-2 text-sm text-slate-500">
                {{ $document->description ?: 'No description provided.' }}
            </p>

        </div>


        <div class="flex flex-wrap gap-2">

            @can('manageDocuments')

                @if (! $document->is_system_generated)

                    <a
                        href="{{ route('documents.edit', $document) }}"
                        class="btn-outline">

                        Manage Document

                    </a>

                @endif

            @endcan


            @if ($document->file_uri)

                <button
                    type="button"
                    data-document-download="{{ route('documents.request-link', $document) }}"
                    class="btn-primary">

                    Download File

                </button>

            @endif


            @can('manageDocuments')

                @if ($document->status === 'archived')

                    <form
                        method="POST"
                        action="{{ route('documents.restore', $document) }}">

                        @csrf

                        <button
                            type="submit"
                            class="btn-primary">

                            Restore

                        </button>

                    </form>

                @else

                    <form
                        method="POST"
                        action="{{ route('documents.archive', $document) }}"
                        onsubmit="return confirm('Archive this document? It can be restored later.')">

                        @csrf

                        <button
                            type="submit"
                            class="btn-outline">

                            Archive

                        </button>

                    </form>

                @endif

            @endcan

        </div>

    </div>


    {{-- Breadcrumb --}}
    <div class="card px-5 py-4">

        <div class="flex flex-wrap items-center gap-2 text-sm">

            <a
                href="{{ route('documents.index') }}"
                class="font-medium text-primary hover:text-secondary">

                Document Management

            </a>

            @foreach ($breadcrumbs as $breadcrumb)

                <span class="text-slate-300">/</span>

                <a
                    href="{{ route('documents.index', ['container' => $breadcrumb->id]) }}"
                    class="text-slate-600 hover:text-primary">

                    {{ $breadcrumb->name }}

                </a>

            @endforeach

        </div>

    </div>


    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_380px]">

        {{-- Main metadata --}}
        <div class="space-y-6">

            <section class="card overflow-hidden">

                <div class="border-b border-border px-6 py-5">

                    <h2 class="font-heading text-base font-semibold text-primary">
                        Document Information
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        Classification and records-management metadata.
                    </p>

                </div>


                <div class="grid gap-x-8 gap-y-6 p-6 sm:grid-cols-2">

                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Category
                        </p>

                        <p class="mt-1.5 text-sm font-medium text-primary">
                            {{ str($document->category)->headline() }}
                        </p>
                    </div>


                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Status
                        </p>

                        <div class="mt-1.5">

                            @switch($document->status)

                                @case('active')
                                    <span class="badge badge-success">Active</span>
                                    @break

                                @case('needs_review')
                                    <span class="badge badge-warning">Needs Review</span>
                                    @break

                                @case('archived')
                                    <span class="badge bg-slate-100 text-slate-600">
                                        Archived
                                    </span>
                                    @break

                                @case('superseded')
                                    <span class="badge badge-info">Superseded</span>
                                    @break

                                @default
                                    <span class="badge badge-info">
                                        {{ str($document->status)->headline() }}
                                    </span>

                            @endswitch

                        </div>
                    </div>


                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Confidentiality
                        </p>

                        <p class="mt-1.5 text-sm font-medium text-primary">
                            {{ str($document->confidentiality)->headline() }}
                        </p>
                    </div>


                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Department
                        </p>

                        <p class="mt-1.5 text-sm font-medium text-primary">
                            {{ $document->department ?: 'Not specified' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Document Date
                        </p>

                        <p class="mt-1.5 text-sm font-medium text-primary">
                            {{ $document->document_date?->format('M d, Y') ?: 'Not specified' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Expiration Date
                        </p>

                        <p class="mt-1.5 text-sm font-medium text-primary">
                            {{ $document->expiration_date?->format('M d, Y') ?: 'No expiration' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Owner
                        </p>

                        <p class="mt-1.5 break-all text-sm font-medium text-primary">
                            {{ $document->owner_email ?: 'Not assigned' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Version
                        </p>

                        <p class="mt-1.5 text-sm font-medium text-primary">
                            {{ $document->version ?? 1 }}
                        </p>
                    </div>


                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            File
                        </p>

                        <p class="mt-1.5 break-all text-sm font-medium text-primary">
                            {{ $document->file_name ?: 'No physical file attached' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Created
                        </p>

                        <p class="mt-1.5 text-sm font-medium text-primary">
                            {{ $document->created_at?->format('M d, Y h:i A') }}
                        </p>
                    </div>

                </div>

            </section>


            {{-- History --}}
            <section class="card overflow-hidden">

                <div class="border-b border-border px-6 py-5">

                    <h2 class="font-heading text-base font-semibold text-primary">
                        Document History
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        Lifecycle and synchronization activity for this record.
                    </p>

                </div>


                <div class="divide-y divide-border">

                    @forelse (
                        collect($document->history ?? [])->reverse()
                        as $entry
                    )

                        <div class="flex gap-4 px-6 py-5">

                            <div class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-accent"></div>

                            <div class="min-w-0 flex-1">

                                <div class="flex flex-wrap items-center justify-between gap-2">

                                    <p class="font-button text-sm font-semibold text-primary">
                                        {{ str($entry['action'] ?? 'activity')->headline() }}
                                    </p>

                                    <span class="text-xs text-slate-400">
                                        @if (!empty($entry['at']))
                                            {{ \Illuminate\Support\Carbon::parse($entry['at'])->format('M d, Y h:i A') }}
                                        @endif
                                    </span>

                                </div>

                                <p class="mt-1 text-sm text-slate-500">
                                    {{ $entry['note'] ?? 'No additional details.' }}
                                </p>

                                <p class="mt-2 text-xs text-slate-400">
                                    By:
                                    {{ $entry['by'] ?? 'system' }}

                                    @if (!empty($entry['version']))
                                        · Version {{ $entry['version'] }}
                                    @endif
                                </p>

                            </div>

                        </div>

                    @empty

                        <div class="px-6 py-10 text-center text-sm text-slate-500">
                            No document history recorded.
                        </div>

                    @endforelse

                </div>

            </section>

        </div>


        {{-- Right panel --}}
        <aside class="space-y-6">

            <section class="card overflow-hidden">

                <div class="border-b border-border px-5 py-4">

                    <h2 class="font-heading text-sm font-semibold text-primary">
                        Related System Record
                    </h2>

                </div>


                <div class="space-y-4 p-5">

                    @if ($document->relatedModuleLabel())

                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-400">
                                Module
                            </p>

                            <span class="mt-2 inline-flex rounded-lg bg-accent/10 px-2.5 py-1 text-xs font-medium text-accent">
                                {{ $document->relatedModuleLabel() }}
                            </span>
                        </div>


                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-400">
                                Record
                            </p>

                            <p class="mt-2 text-sm font-medium text-primary">
                                {{ $document->relatedRecordLabel() }}
                            </p>
                        </div>

                    @else

                        <p class="text-sm text-slate-500">
                            This document is not linked to another system record.
                        </p>

                    @endif


                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-400">
                            Source
                        </p>

                        <p class="mt-2 text-sm font-medium text-primary">

                            @if ($document->is_system_generated)
                                System Generated
                            @else
                                Manual Upload
                            @endif

                        </p>
                    </div>


                    @if ($document->source_module)

                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-400">
                                Source Module
                            </p>

                            <p class="mt-2 text-sm font-medium text-primary">
                                {{ str($document->source_module)->headline() }}
                            </p>
                        </div>

                    @endif


                    @if ($document->source_module === 'reservations')

                        <a
                            href="{{ route('reservations.index') }}"
                            class="btn-outline flex w-full justify-center">

                            Open Facilities Reservations

                        </a>

                    @elseif ($document->source_module === 'visitors')

                        <a
                            href="{{ route('visitors.index') }}"
                            class="btn-outline flex w-full justify-center">

                            Open Visitor Management

                        </a>

                    @elseif ($document->source_module === 'contracts')

                        <a
                            href="{{ route('contracts.index') }}"
                            class="btn-outline flex w-full justify-center">

                            Open Contract Management

                        </a>

                    @elseif ($document->source_module === 'legal')

                        <a
                            href="{{ route('legal.index') }}"
                            class="btn-outline flex w-full justify-center">

                            Open Legal Management

                        </a>

                    @endif

                </div>

            </section>


            {{-- Retention & Compliance --}}
            <section class="card overflow-hidden">

                <div class="border-b border-border px-5 py-4">
                    <h2 class="font-heading text-sm font-semibold text-primary">
                        Retention & Compliance
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        Retention policy and review status for this document.
                    </p>
                </div>

                <div class="space-y-4 p-5">

                    @if ($document->retentionRecord)

                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-400">
                                Policy
                            </p>

                            <p class="mt-1.5 text-sm font-semibold text-primary">
                                {{ $document->retentionRecord->policy_name ?: 'No policy name' }}
                            </p>
                        </div>


                        <div class="grid grid-cols-2 gap-4">

                            <div>
                                <p class="text-xs uppercase tracking-wide text-slate-400">
                                    Retention Status
                                </p>

                                <div class="mt-2">
                                    @switch($document->retentionRecord->status)

                                        @case('retained')
                                            <span class="badge badge-success">
                                                Retained
                                            </span>
                                            @break

                                        @case('review_required')
                                            <span class="badge badge-warning">
                                                Review Required
                                            </span>
                                            @break

                                        @case('extended')
                                            <span class="badge badge-info">
                                                Extended
                                            </span>
                                            @break

                                        @case('archived')
                                            <span class="badge bg-slate-100 text-slate-600">
                                                Archived
                                            </span>
                                            @break

                                        @case('marked_for_disposal')
                                            <span class="badge badge-error">
                                                Marked for Disposal
                                            </span>
                                            @break

                                    @endswitch
                                </div>
                            </div>


                            <div>
                                <p class="text-xs uppercase tracking-wide text-slate-400">
                                    Compliance
                                </p>

                                <div class="mt-2">
                                    @switch($document->retentionRecord->compliance_status)

                                        @case('compliant')
                                            <span class="badge badge-success">
                                                Compliant
                                            </span>
                                            @break

                                        @case('at_risk')
                                            <span class="badge badge-warning">
                                                At Risk
                                            </span>
                                            @break

                                        @case('non_compliant')
                                            <span class="badge badge-error">
                                                Non-Compliant
                                            </span>
                                            @break

                                    @endswitch
                                </div>
                            </div>

                        </div>


                        <div class="grid grid-cols-2 gap-4">

                            <div>
                                <p class="text-xs uppercase tracking-wide text-slate-400">
                                    Retention Start
                                </p>

                                <p class="mt-1.5 text-sm font-medium text-primary">
                                    {{ $document->retentionRecord->start_date?->format('M d, Y') ?: 'Not specified' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs uppercase tracking-wide text-slate-400">
                                    Review Date
                                </p>

                                <p class="mt-1.5 text-sm font-medium text-primary">
                                    {{ $document->retentionRecord->review_date?->format('M d, Y') ?: 'Not specified' }}
                                </p>
                            </div>

                        </div>


                        @if ($document->retentionRecord->notes)

                            <div>
                                <p class="text-xs uppercase tracking-wide text-slate-400">
                                    Notes
                                </p>

                                <p class="mt-1.5 text-sm leading-relaxed text-slate-600">
                                    {{ $document->retentionRecord->notes }}
                                </p>
                            </div>

                        @endif


                        <a
                            href="{{ route('retention.index', ['record_type' => 'document']) }}"
                            class="btn-outline flex w-full justify-center">

                            Open Records Retention

                        </a>

                    @else

                        <div class="rounded-xl border border-dashed border-border bg-background/50 p-4 text-center">

                            <p class="text-sm font-medium text-primary">
                                No retention policy assigned
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                Assign an active policy matching this document's category.
                            </p>

                        </div>


                        @can('manageRetention')

                            @if ($retentionPolicies->isNotEmpty())

                                <form
                                    method="POST"
                                    action="{{ route('documents.retention.assign', $document) }}"
                                    class="space-y-4">

                                    @csrf


                                    <div>
                                        <label class="label">
                                            Retention Policy
                                        </label>

                                        <select
                                            name="policy_id"
                                            required
                                            class="input">

                                            <option value="">
                                                Select policy
                                            </option>

                                            @foreach ($retentionPolicies as $policy)

                                                <option value="{{ $policy->id }}">
                                                    {{ $policy->name }}
                                                    - {{ $policy->retention_years }}
                                                    {{ $policy->retention_years === 1 ? 'year' : 'years' }}
                                                </option>

                                            @endforeach

                                        </select>
                                    </div>


                                    <div>
                                        <label class="label">
                                            Retention Start
                                        </label>

                                        <input
                                            type="date"
                                            name="start_date"
                                            value="{{ $document->document_date?->format('Y-m-d') ?? now()->format('Y-m-d') }}"
                                            class="input">
                                    </div>


                                    <div>
                                        <label class="label">
                                            Notes
                                        </label>

                                        <textarea
                                            name="notes"
                                            rows="3"
                                            class="input"
                                            placeholder="Optional retention notes..."></textarea>
                                    </div>


                                    <button
                                        type="submit"
                                        class="btn-primary w-full">

                                        Assign Retention Policy

                                    </button>

                                </form>

                            @else

                                <div class="rounded-xl bg-warning/5 p-4">

                                    <p class="text-sm font-medium text-amber-700">
                                        No matching active policy
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        Create an active retention policy for the
                                        {{ str($document->category)->headline() }}
                                        category first.
                                    </p>

                                    <a
                                        href="{{ route('retention.index') }}"
                                        class="mt-3 inline-flex text-sm font-semibold text-primary hover:text-secondary">

                                        Open Records Retention →

                                    </a>

                                </div>

                            @endif

                        @endcan

                    @endif

                </div>

            </section>


            <section class="card p-5">

                <p class="text-xs uppercase tracking-wide text-slate-400">
                    Document Location
                </p>

                @if ($document->container)

                    <p class="mt-2 text-sm font-medium leading-relaxed text-primary">
                        {{ str_replace('/', ' / ', $document->container->path) }}
                    </p>

                @else

                    <p class="mt-2 text-sm text-slate-500">
                        Unfiled
                    </p>

                @endif

            </section>

        </aside>

    </div>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll(
        '[data-document-download]'
    ).forEach(function (button) {

        button.addEventListener(
            'click',
            async function () {

                const originalText =
                    button.textContent;

                button.disabled = true;
                button.textContent =
                    'Preparing...';

                try {

                    const response = await fetch(
                        button.dataset.documentDownload,
                        {
                            method: 'POST',

                            headers: {
                                'X-CSRF-TOKEN':
                                    document.querySelector(
                                        'meta[name="csrf-token"]'
                                    ).content,

                                'Accept':
                                    'application/json'
                            }
                        }
                    );

                    if (!response.ok) {
                        throw new Error(
                            'Unable to prepare download.'
                        );
                    }

                    const data =
                        await response.json();

                    window.location.href =
                        data.signed_url;

                } catch (error) {

                    alert(error.message);

                } finally {

                    button.disabled = false;
                    button.textContent =
                        originalText;
                }

            }
        );

    });

});
</script>

@endsection