@extends('layouts.app')

@section('title', 'Document Details')

@section('content')

<div class="space-y-4">

    {{-- =====================================================
         MODERN DOCUMENT DETAILS HEADER
    ====================================================== --}}
    <section class="card overflow-visible">

        <div class="px-5 py-5 sm:px-6">

            {{-- Navigation --}}
            <div class="flex flex-wrap items-center gap-1.5 text-[10px] text-slate-400">

                <a
                    href="{{ route(
                        'documents.index',
                        $document->container_id
                            ? ['container' => $document->container_id]
                            : []
                    ) }}"
                    class="inline-flex items-center gap-1.5 font-semibold text-primary transition hover:text-secondary">

                    <svg
                        class="h-3.5 w-3.5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 19l-7-7 7-7" />

                    </svg>

                    Document Management

                </a>


                @foreach ($breadcrumbs as $breadcrumb)

                    <svg
                        class="h-3 w-3 text-slate-300"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 5l7 7-7 7" />

                    </svg>

                    <a
                        href="{{ route('documents.index', ['container' => $breadcrumb->id]) }}"
                        class="max-w-40 truncate font-medium transition hover:text-primary">

                        {{ $breadcrumb->name }}

                    </a>

                @endforeach

            </div>


            <div class="mt-4 flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">

                <div class="min-w-0">

                    <div class="flex items-start gap-3">

                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary/5 text-primary">

                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M7 3h7l5 5v13H7V3zm7 0v5h5M10 13h6M10 17h6" />

                            </svg>

                        </div>


                        <div class="min-w-0">

                            <p class="text-[9px] font-semibold uppercase tracking-[0.16em] text-secondary">
                                Document Details
                            </p>

                            <h1 class="mt-1 break-words font-heading text-xl font-bold tracking-tight text-primary sm:text-2xl">
                                {{ $document->title }}
                            </h1>


                            {{-- Compact classification --}}
                            <div class="mt-2 flex flex-wrap items-center gap-1.5">

                                <span class="rounded-full bg-primary/5 px-2.5 py-1 text-[10px] font-semibold text-primary">
                                    {{ str($document->category)->headline() }}
                                </span>


                                <span class="rounded-full bg-accent/10 px-2.5 py-1 text-[10px] font-semibold text-primary">
                                    {{ str($document->confidentiality ?? 'general')->headline() }}
                                </span>


                                @switch($document->status)

                                    @case('active')

                                        <span class="badge badge-success">
                                            Active
                                        </span>

                                        @break


                                    @case('needs_review')

                                        <span class="badge badge-warning">
                                            Needs Review
                                        </span>

                                        @break


                                    @case('archived')

                                        <span class="badge bg-slate-100 text-slate-600">
                                            Archived
                                        </span>

                                        @break


                                    @case('superseded')

                                        <span class="badge badge-info">
                                            Superseded
                                        </span>

                                        @break


                                    @default

                                        <span class="badge badge-info">
                                            {{ str($document->status)->headline() }}
                                        </span>

                                @endswitch


                                @if ($document->is_system_generated)

                                    <span class="rounded-full bg-accent/10 px-2.5 py-1 text-[10px] font-semibold text-accent">
                                        System Record
                                    </span>

                                @else

                                    <span class="rounded-full bg-primary/5 px-2.5 py-1 text-[10px] font-semibold text-primary">
                                        v{{ $document->version ?? 1 }}
                                    </span>

                                @endif

                            </div>

                        </div>

                    </div>


                    @if ($document->description)

                        <p class="mt-4 max-w-3xl text-xs leading-5 text-slate-500 sm:text-sm">
                            {{ $document->description }}
                        </p>

                    @endif

                </div>


                {{-- Primary actions --}}
                <div class="flex shrink-0 items-center gap-2">

                    @if ($document->file_uri)

                        <button
                            type="button"
                            data-document-download="{{ route('documents.request-link', $document) }}"
                            class="btn-primary inline-flex items-center justify-center gap-2">

                            <svg
                                class="h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 3v12m0 0l-4-4m4 4l4-4M5 21h14" />

                            </svg>

                            Download File

                        </button>

                    @endif


                    @include(
                        'documents._details-actions',
                        [
                            'document' => $document,
                        ]
                    )

                </div>

            </div>

        </div>

    </section>


    <div class="grid gap-4 xl:grid-cols-[minmax(0,1fr)_340px]">

        {{-- =====================================================
             MAIN RECORD CONTENT
        ====================================================== --}}
        <div class="space-y-4">

            <section class="card overflow-hidden">

                <div class="flex items-center justify-between gap-3 border-b border-border px-5 py-4">

                    <div>

                        <h2 class="font-heading text-sm font-semibold text-primary">
                            Document Information
                        </h2>

                        <p class="mt-0.5 text-[10px] text-slate-400">
                            File, ownership, filing location, and record dates.
                        </p>

                    </div>


                    @if (! $document->is_system_generated)

                        <span class="rounded-full bg-primary/5 px-2.5 py-1 text-[9px] font-semibold text-primary">
                            Version {{ $document->version ?? 1 }}
                        </span>

                    @endif

                </div>


                <div class="divide-y divide-border">

                    {{-- File / Folder --}}
                    <div class="grid gap-4 px-5 py-4 sm:grid-cols-2">

                        <div class="min-w-0">

                            <p class="text-[9px] font-semibold uppercase tracking-wide text-slate-400">
                                File
                            </p>

                            <div class="mt-1.5 flex items-start gap-2">

                                <svg
                                    class="mt-0.5 h-4 w-4 shrink-0 text-primary"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M7 3h7l5 5v13H7V3zm7 0v5h5" />

                                </svg>

                                <p
                                    class="min-w-0 break-all text-xs font-medium text-primary"
                                    title="{{ $document->file_name }}">

                                    {{ $document->file_name ?: 'No physical file attached' }}

                                </p>

                            </div>

                        </div>


                        <div class="min-w-0">

                            <p class="text-[9px] font-semibold uppercase tracking-wide text-slate-400">
                                Folder
                            </p>

                            @if ($document->container)

                                <a
                                    href="{{ route('documents.index', ['container' => $document->container->id]) }}"
                                    class="mt-1.5 inline-flex max-w-full items-center gap-1.5 text-xs font-medium text-primary transition hover:text-secondary">

                                    <svg
                                        class="h-4 w-4 shrink-0"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24">

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M3 7h6l2 2h10v10H3V7z" />

                                    </svg>

                                    <span class="truncate">
                                        {{ str_replace('/', ' / ', $document->container->path) }}
                                    </span>

                                </a>

                            @else

                                <p class="mt-1.5 text-xs font-medium text-slate-400">
                                    Unfiled
                                </p>

                            @endif

                        </div>

                    </div>


                    {{-- Organization --}}
                    <div class="grid gap-4 px-5 py-4 sm:grid-cols-2">

                        <div>

                            <p class="text-[9px] font-semibold uppercase tracking-wide text-slate-400">
                                Department
                            </p>

                            <p class="mt-1.5 text-xs font-medium text-primary">
                                {{ $document->department ?: 'Not specified' }}
                            </p>

                        </div>


                        <div class="min-w-0">

                            <p class="text-[9px] font-semibold uppercase tracking-wide text-slate-400">
                                Owner
                            </p>

                            <p class="mt-1.5 break-all text-xs font-medium text-primary">
                                {{ $document->owner_email ?: 'Not assigned' }}
                            </p>

                        </div>

                    </div>


                    {{-- Important dates --}}
                    <div class="grid gap-4 px-5 py-4 sm:grid-cols-2">

                        <div>

                            <p class="text-[9px] font-semibold uppercase tracking-wide text-slate-400">
                                Document Date
                            </p>

                            <p class="mt-1.5 text-xs font-medium text-primary">
                                {{ $document->document_date?->format('M d, Y') ?: 'Not specified' }}
                            </p>

                        </div>


                        <div>

                            <p class="text-[9px] font-semibold uppercase tracking-wide text-slate-400">
                                Expiration Date
                            </p>

                            <p
                                @class([
                                    'mt-1.5 text-xs font-medium',
                                    'text-primary' => ! $document->expiration_date,
                                    'text-primary' => $document->expiration_date && $document->expiration_date->isFuture(),
                                    'text-error' => $document->expiration_date && $document->expiration_date->isPast(),
                                ])>

                                {{ $document->expiration_date?->format('M d, Y') ?: 'No expiration' }}

                            </p>

                        </div>

                    </div>


                    {{-- Record timestamps --}}
                    <div class="grid gap-4 bg-background/30 px-5 py-4 sm:grid-cols-2">

                        <div>

                            <p class="text-[9px] font-semibold uppercase tracking-wide text-slate-400">
                                Created
                            </p>

                            <p class="mt-1.5 text-xs font-medium text-slate-600">
                                {{ $document->created_at?->format('M d, Y') }}
                            </p>

                            <p class="mt-0.5 text-[9px] text-slate-400">
                                {{ $document->created_at?->format('h:i A') }}
                            </p>

                        </div>


                        <div>

                            <p class="text-[9px] font-semibold uppercase tracking-wide text-slate-400">
                                Last Updated
                            </p>

                            <p class="mt-1.5 text-xs font-medium text-slate-600">
                                {{ $document->updated_at?->format('M d, Y') }}
                            </p>

                            <p class="mt-0.5 text-[9px] text-slate-400">
                                {{ $document->updated_at?->format('h:i A') }}
                            </p>

                        </div>

                    </div>

                </div>

            </section>

            {{-- History --}}
            @include(
                'documents._history-timeline',
                [
                    'document' => $document,
                ]
            )

        </div>


        {{-- =====================================================
             RIGHT DETAILS PANEL
        ====================================================== --}}
        <aside class="space-y-4">

            <section class="card overflow-hidden">

                <div class="border-b border-border px-4 py-4">

                    <div class="flex items-center gap-3">

                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-accent/10 text-primary">

                            <svg
                                class="h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M10 13a5 5 0 007.1 0l2-2a5 5 0 00-7.1-7.1l-1.1 1.1M14 11a5 5 0 00-7.1 0l-2 2A5 5 0 0012 20.1l1.1-1.1" />

                            </svg>

                        </span>


                        <div>

                            <h2 class="font-heading text-sm font-semibold text-primary">
                                Related Record
                            </h2>

                            <p class="mt-0.5 text-[10px] text-slate-400">
                                Source and system relationship
                            </p>

                        </div>

                    </div>

                </div>


                <div class="p-4">

                    @if ($document->relatedModuleLabel())

                        <div class="rounded-xl bg-background/60 p-3">

                            <span class="inline-flex rounded-md bg-accent/10 px-2 py-1 text-[9px] font-semibold text-primary">
                                {{ $document->relatedModuleLabel() }}
                            </span>


                            <p class="mt-2 break-words text-xs font-semibold leading-5 text-primary">
                                {{ $document->relatedRecordLabel() }}
                            </p>

                        </div>

                    @else

                        <div class="rounded-xl border border-dashed border-border px-3 py-4 text-center">

                            <p class="text-xs font-medium text-primary">
                                No linked record
                            </p>

                            <p class="mt-1 text-[10px] leading-4 text-slate-400">
                                This document is not connected to another system record.
                            </p>

                        </div>

                    @endif


                    <div class="mt-4 grid grid-cols-2 gap-3">

                        <div>

                            <p class="text-[9px] font-semibold uppercase tracking-wide text-slate-400">
                                Source
                            </p>

                            <p class="mt-1 text-[10px] font-semibold text-primary">

                                {{
                                    $document->is_system_generated
                                        ? 'System Generated'
                                        : 'Manual Upload'
                                }}

                            </p>

                        </div>


                        <div>

                            <p class="text-[9px] font-semibold uppercase tracking-wide text-slate-400">
                                Module
                            </p>

                            <p class="mt-1 text-[10px] font-semibold text-primary">

                                {{
                                    $document->source_module
                                        ? str($document->source_module)->headline()
                                        : 'Document Management'
                                }}

                            </p>

                        </div>

                    </div>


                    @if ($document->source_module === 'reservations')

                        <a
                            href="{{ route('reservations.index') }}"
                            class="btn-outline mt-4 flex w-full justify-center text-xs">

                            Open Facilities Reservation

                        </a>

                    @elseif ($document->source_module === 'visitors')

                        <a
                            href="{{ route('visitors.index') }}"
                            class="btn-outline mt-4 flex w-full justify-center text-xs">

                            Open Visitor Management

                        </a>

                    @elseif ($document->source_module === 'contracts')

                        <a
                            href="{{ route('contracts.index') }}"
                            class="btn-outline mt-4 flex w-full justify-center text-xs">

                            Open Contract Management

                        </a>

                    @elseif ($document->source_module === 'legal')

                        <a
                            href="{{ route('legal.index') }}"
                            class="btn-outline mt-4 flex w-full justify-center text-xs">

                            Open Legal Management

                        </a>

                    @endif

                </div>

            </section>

            {{-- Retention & Compliance --}}
            @include(
                'documents._retention-card',
                [
                    'document' => $document,
                    'retentionPolicies' => $retentionPolicies,
                ]
            )

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