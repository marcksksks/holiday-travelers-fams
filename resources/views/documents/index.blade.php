@extends('layouts.app')

@section('title', 'Document Management')

@section('content')

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">

        <div>
            <div class="mb-2 h-1 w-12 rounded-full bg-secondary"></div>

            <h1 class="font-heading text-2xl font-bold text-primary">
                Document Management
            </h1>

            <p class="mt-1 max-w-3xl text-sm text-slate-500">
                Centralized digital filing cabinet for administrative records,
                facility records, contracts, legal documents, compliance files,
                and system-generated records.
            </p>
        </div>

        @can('manageDocuments')
            <a
                href="#upload-document"
                class="btn-primary inline-flex items-center justify-center gap-2">

                <span class="text-lg leading-none">+</span>
                Upload Document

            </a>
        @endcan

    </div>


    {{-- Statistics --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

        <a
            href="{{ route('documents.index') }}"
            class="card p-5 transition hover:-translate-y-0.5 hover:shadow-soft">

            <p class="text-xs font-medium text-slate-500">
                Total Documents
            </p>

            <p class="mt-2 font-heading text-2xl font-bold text-primary">
                {{ $counts['total'] }}
            </p>
        </a>


        <a
            href="{{ route('documents.index', ['status' => 'active']) }}"
            class="card p-5 transition hover:-translate-y-0.5 hover:shadow-soft">

            <p class="text-xs font-medium text-slate-500">
                Active
            </p>

            <p class="mt-2 font-heading text-2xl font-bold text-success">
                {{ $counts['active'] }}
            </p>
        </a>


        <a
            href="{{ route('documents.index', ['status' => 'needs_review']) }}"
            class="card p-5 transition hover:-translate-y-0.5 hover:shadow-soft">

            <p class="text-xs font-medium text-slate-500">
                Needs Review
            </p>

            <p class="mt-2 font-heading text-2xl font-bold text-amber-600">
                {{ $counts['needs_review'] }}
            </p>
        </a>


        <a
            href="{{ route('documents.index', ['status' => 'archived']) }}"
            class="card p-5 transition hover:-translate-y-0.5 hover:shadow-soft">

            <p class="text-xs font-medium text-slate-500">
                Archived
            </p>

            <p class="mt-2 font-heading text-2xl font-bold text-slate-500">
                {{ $counts['archived'] }}
            </p>
        </a>

    </div>


    {{-- Main Library --}}
    <div class="grid gap-6 xl:grid-cols-[290px_minmax(0,1fr)]">

        {{-- Folder tree --}}
        <aside class="card h-fit overflow-hidden xl:sticky xl:top-6">

            <div class="border-b border-border px-5 py-4">

                <h2 class="font-heading text-sm font-semibold text-primary">
                    Document Library
                </h2>

                <p class="mt-1 text-xs text-slate-500">
                    Browse records by container.
                </p>

            </div>


            <div class="max-h-[68vh] overflow-y-auto p-3">

                <a
                    href="{{ route('documents.index') }}"
                    @class([
                        'mb-2 flex items-center gap-2 rounded-lg px-3 py-2.5 text-sm font-medium transition',
                        'bg-primary text-white' => ! request()->filled('container') && ! request()->filled('status'),
                        'text-slate-600 hover:bg-background hover:text-primary' => request()->filled('container') || request()->filled('status'),
                    ])>

                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 5h16v14H4V5zm4 4h8M8 13h8" />
                    </svg>

                    All Documents
                </a>


                <div class="space-y-0.5">

                    @foreach ($rootContainers as $container)

                        @include(
                            'documents._container-node',
                            [
                                'container' => $container,
                                'level' => 0,
                                'selectedContainer' => $selectedContainer,
                            ]
                        )

                    @endforeach

                </div>


                <div class="my-4 border-t border-border"></div>


                <a
                    href="{{ route('documents.index', ['status' => 'needs_review']) }}"
                    @class([
                        'flex items-center justify-between rounded-lg px-3 py-2 text-sm transition',
                        'bg-warning/10 font-semibold text-amber-700' => request('status') === 'needs_review',
                        'text-slate-600 hover:bg-background' => request('status') !== 'needs_review',
                    ])>

                    <span>Needs Review</span>

                    <span class="rounded-full bg-warning/10 px-2 py-0.5 text-xs">
                        {{ $counts['needs_review'] }}
                    </span>
                </a>


                <a
                    href="{{ route('documents.index', ['status' => 'archived']) }}"
                    @class([
                        'mt-1 flex items-center justify-between rounded-lg px-3 py-2 text-sm transition',
                        'bg-slate-100 font-semibold text-primary' => request('status') === 'archived',
                        'text-slate-600 hover:bg-background' => request('status') !== 'archived',
                    ])>

                    <span>Archived Documents</span>

                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs">
                        {{ $counts['archived'] }}
                    </span>
                </a>

            </div>

        </aside>


        {{-- Documents --}}
        <main class="min-w-0 space-y-5">

            {{-- Breadcrumb / current location --}}
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
                            class="font-medium text-slate-600 hover:text-primary">

                            {{ $breadcrumb->name }}

                        </a>

                    @endforeach

                    @if (request('status') === 'archived')
                        <span class="text-slate-300">/</span>
                        <span class="font-medium text-slate-500">
                            Archived Documents
                        </span>
                    @elseif (request('status') === 'needs_review')
                        <span class="text-slate-300">/</span>
                        <span class="font-medium text-slate-500">
                            Needs Review
                        </span>
                    @endif

                </div>

            </div>


            {{-- Filters --}}
            <form
                method="GET"
                action="{{ route('documents.index') }}"
                class="card grid gap-3 p-4 md:grid-cols-[minmax(0,1fr)_180px_160px_auto]">

                @if ($selectedContainer)
                    <input
                        type="hidden"
                        name="container"
                        value="{{ $selectedContainer->id }}">
                @endif

                <input
                    type="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search documents..."
                    class="input">

                <select name="category" class="input">
                    <option value="">All Categories</option>

                    @foreach ([
                        'administrative',
                        'contract',
                        'legal',
                        'permit',
                        'license',
                        'compliance',
                        'partnership',
                        'financial',
                        'operational',
                        'other'
                    ] as $category)

                        <option
                            value="{{ $category }}"
                            @selected(request('category') === $category)>

                            {{ str($category)->headline() }}

                        </option>

                    @endforeach
                </select>

                <select name="status" class="input">
                    <option value="">All Statuses</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="needs_review" @selected(request('status') === 'needs_review')>Needs Review</option>
                    <option value="archived" @selected(request('status') === 'archived')>Archived</option>
                    <option value="superseded" @selected(request('status') === 'superseded')>Superseded</option>
                </select>

                <button type="submit" class="btn-primary">
                    Search
                </button>

            </form>


            {{-- Upload --}}
            @can('manageDocuments')

                <details id="upload-document" class="card overflow-hidden">

                    <summary class="cursor-pointer list-none px-5 py-4">

                        <div class="flex items-center justify-between gap-3">

                            <div>
                                <h2 class="font-heading text-sm font-semibold text-primary">
                                    Upload a Document
                                </h2>

                                <p class="mt-1 text-xs text-slate-500">
                                    Add a manually uploaded document to the selected container.
                                </p>
                            </div>

                            <span class="text-xl font-medium text-secondary">
                                +
                            </span>

                        </div>

                    </summary>


                    <form
                        method="POST"
                        action="{{ route('documents.store') }}"
                        enctype="multipart/form-data"
                        class="space-y-5 border-t border-border p-5">

                        @csrf

                        <div class="grid gap-4 md:grid-cols-2">

                            <div>
                                <label class="label">Document Title *</label>

                                <input
                                    name="title"
                                    value="{{ old('title') }}"
                                    required
                                    class="input">
                            </div>


                            <div>
                                <label class="label">Container</label>

                                <select name="container_id" class="input">

                                    <option value="">
                                        Unfiled
                                    </option>

                                    @foreach ($allContainers as $container)

                                        <option
                                            value="{{ $container->id }}"
                                            @selected(
                                                (string) old(
                                                    'container_id',
                                                    $selectedContainer?->id
                                                ) === (string) $container->id
                                            )>

                                            {{ str_replace('/', ' / ', $container->path) }}

                                        </option>

                                    @endforeach

                                </select>
                            </div>


                            <div>
                                <label class="label">Category *</label>

                                <select name="category" class="input">

                                    @foreach ([
                                        'administrative',
                                        'contract',
                                        'legal',
                                        'permit',
                                        'license',
                                        'compliance',
                                        'partnership',
                                        'financial',
                                        'operational',
                                        'other'
                                    ] as $category)

                                        <option
                                            value="{{ $category }}"
                                            @selected(old('category', 'administrative') === $category)>

                                            {{ str($category)->headline() }}

                                        </option>

                                    @endforeach

                                </select>
                            </div>


                            <div>
                                <label class="label">Confidentiality *</label>

                                <select name="confidentiality" class="input">
                                    <option value="general">General</option>
                                    <option value="restricted">Restricted</option>
                                    <option value="confidential">Confidential</option>
                                </select>
                            </div>


                            <div>
                                <label class="label">Department</label>

                                <input
                                    name="department"
                                    value="{{ old('department') }}"
                                    class="input">
                            </div>


                            <div>
                                <label class="label">Owner Email</label>

                                <input
                                    type="email"
                                    name="owner_email"
                                    value="{{ old('owner_email') }}"
                                    class="input">
                            </div>


                            <div>
                                <label class="label">Document Date</label>

                                <input
                                    type="date"
                                    name="document_date"
                                    value="{{ old('document_date') }}"
                                    class="input">
                            </div>


                            <div>
                                <label class="label">Expiration Date</label>

                                <input
                                    type="date"
                                    name="expiration_date"
                                    value="{{ old('expiration_date') }}"
                                    class="input">
                            </div>


                            <div>
                                <label class="label">Status</label>

                                <select name="status" class="input">
                                    <option value="active">Active</option>
                                    <option value="needs_review">Needs Review</option>
                                    <option value="archived">Archived</option>
                                    <option value="superseded">Superseded</option>
                                </select>
                            </div>


                            <div>
                                <label class="label">File</label>

                                <input
                                    type="file"
                                    name="file"
                                    class="input">
                            </div>

                        </div>


                        {{-- Related record --}}
                        <div class="rounded-xl border border-border bg-background/50 p-4">

                            <p class="font-heading text-sm font-semibold text-primary">
                                Related System Record
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                Optional. Link this file to an existing system record.
                            </p>


                            <div class="mt-4 grid gap-4 md:grid-cols-2">

                                <div>
                                    <label class="label">Related Module</label>

                                    <select
                                        id="related_type"
                                        name="related_type"
                                        class="input">

                                        <option value="">None</option>

                                        @if ($contracts->isNotEmpty())
                                            <option value="contract">Contract</option>
                                        @endif

                                        @if ($legalRecords->isNotEmpty())
                                            <option value="legal">Legal Record</option>
                                        @endif

                                        @if ($reservations->isNotEmpty())
                                            <option value="reservation">Facility Reservation</option>
                                        @endif

                                        @if ($visitors->isNotEmpty())
                                            <option value="visitor">Visitor Record</option>
                                        @endif

                                    </select>
                                </div>


                                <div>
                                    <label class="label">Related Record</label>

                                    <select
                                        id="related_id"
                                        name="related_id"
                                        class="input"
                                        disabled>

                                        <option value="">Select a record</option>

                                        @foreach ($contracts as $contract)
                                            <option
                                                value="{{ $contract->id }}"
                                                data-related-type="contract">

                                                {{ $contract->contract_number ?: 'Contract #'.$contract->id }}
                                                - {{ $contract->title }}

                                            </option>
                                        @endforeach


                                        @foreach ($legalRecords as $legalRecord)
                                            <option
                                                value="{{ $legalRecord->id }}"
                                                data-related-type="legal">

                                                {{ $legalRecord->reference_number ?: 'Legal #'.$legalRecord->id }}
                                                - {{ $legalRecord->title }}

                                            </option>
                                        @endforeach


                                        @foreach ($reservations as $reservation)
                                            <option
                                                value="{{ $reservation->id }}"
                                                data-related-type="reservation">

                                                #{{ $reservation->id }}
                                                - {{ $reservation->facility_name }}
                                                - {{ $reservation->date?->format('M d, Y') }}

                                            </option>
                                        @endforeach


                                        @foreach ($visitors as $visitor)
                                            <option
                                                value="{{ $visitor->id }}"
                                                data-related-type="visitor">

                                                #{{ $visitor->id }}
                                                - {{ $visitor->full_name }}

                                            </option>
                                        @endforeach

                                    </select>

                                </div>

                            </div>

                        </div>


                        <div>
                            <label class="label">Description</label>

                            <textarea
                                name="description"
                                rows="3"
                                class="input">{{ old('description') }}</textarea>
                        </div>


                        @if ($errors->any())

                            <div class="rounded-xl border border-error/20 bg-error/5 p-4">

                                <p class="text-sm font-semibold text-error">
                                    Please correct the following:
                                </p>

                                <ul class="mt-2 list-disc space-y-1 pl-5 text-xs text-error">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>

                            </div>

                        @endif


                        <div class="flex justify-end">

                            <button type="submit" class="btn-primary">
                                Add to Document Management
                            </button>

                        </div>

                    </form>

                </details>

            @endcan


            {{-- Table --}}
            <div class="table-shell">

                <div class="overflow-x-auto">

                    <table class="min-w-full text-left text-sm">

                        <thead class="table-header">

                            <tr>
                                <th class="px-5 py-4 font-medium">Document</th>
                                <th class="px-5 py-4 font-medium">Location</th>
                                <th class="px-5 py-4 font-medium">Related Record</th>
                                <th class="px-5 py-4 font-medium">Status</th>
                                <th class="px-5 py-4 font-medium">Updated</th>
                                <th class="px-5 py-4 text-right font-medium">Actions</th>
                            </tr>

                        </thead>


                        <tbody class="divide-y divide-border bg-card">

                            @forelse ($documents as $document)

                                <tr class="transition hover:bg-background/70">

                                    <td class="px-5 py-4">

                                        <div class="flex items-start gap-3">

                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary">

                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M7 3h7l5 5v13H7V3zm7 0v5h5M10 13h6M10 17h6" />
                                                </svg>

                                            </div>

                                            <div class="min-w-0">

                                                <div class="flex flex-wrap items-center gap-2">

                                                    <p class="max-w-[280px] truncate font-button text-sm font-semibold text-primary">
                                                        {{ $document->title }}
                                                    </p>

                                                    @if ($document->is_system_generated)

                                                        <span class="rounded-full bg-accent/10 px-2 py-0.5 text-[10px] font-semibold text-accent">
                                                            System Record
                                                        </span>

                                                    @else

                                                        <span class="rounded-full bg-primary/5 px-2 py-0.5 text-[10px] font-semibold text-primary">
                                                            v{{ $document->version ?? 1 }}
                                                        </span>

                                                    @endif

                                                </div>


                                                <p class="mt-1 text-xs text-slate-500">
                                                    {{ str($document->category)->headline() }}

                                                    @if ($document->file_name)
                                                        · {{ $document->file_name }}
                                                    @endif
                                                </p>

                                            </div>

                                        </div>

                                    </td>


                                    <td class="px-5 py-4">

                                        @if ($document->container)

                                            <a
                                                href="{{ route('documents.index', ['container' => $document->container->id]) }}"
                                                class="inline-flex max-w-[220px] items-center gap-1.5 rounded-lg bg-primary/5 px-2.5 py-1.5 text-xs font-medium text-primary hover:bg-primary/10">

                                                <span>📁</span>

                                                <span class="truncate">
                                                    {{ str_replace('/', ' / ', $document->container->path) }}
                                                </span>

                                            </a>

                                        @else

                                            <span class="text-xs text-slate-400">
                                                Unfiled
                                            </span>

                                        @endif

                                    </td>


                                    <td class="px-5 py-4">

                                        @if ($document->relatedModuleLabel())

                                            <div class="min-w-[170px]">

                                                <span class="inline-flex rounded-lg bg-accent/10 px-2 py-1 text-xs font-medium text-accent">
                                                    {{ $document->relatedModuleLabel() }}
                                                </span>

                                                <p
                                                    class="mt-1.5 max-w-[220px] truncate text-xs text-slate-500"
                                                    title="{{ $document->relatedRecordLabel() }}">

                                                    {{ $document->relatedRecordLabel() }}

                                                </p>

                                            </div>

                                        @else

                                            <span class="text-xs text-slate-400">
                                                Not linked
                                            </span>

                                        @endif

                                    </td>


                                    <td class="px-5 py-4">

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

                                    </td>


                                    <td class="whitespace-nowrap px-5 py-4 text-xs text-slate-500">

                                        {{ $document->updated_at?->format('M d, Y') }}

                                        <p class="mt-0.5 text-[11px] text-slate-400">
                                            {{ $document->updated_at?->format('h:i A') }}
                                        </p>

                                    </td>


                                    <td class="px-5 py-4">

                                        <div class="flex justify-end">

                                            <details class="relative">

                                                <summary class="flex h-9 w-9 cursor-pointer list-none items-center justify-center rounded-lg border border-border text-slate-500 transition hover:bg-background hover:text-primary">
                                                    ⋮
                                                </summary>


                                                <div class="absolute right-0 z-30 mt-2 w-56 overflow-hidden rounded-xl border border-border bg-card py-1 shadow-soft">

                                                    <a
                                                        href="{{ route('documents.show', $document) }}"
                                                        class="block px-4 py-2.5 text-sm font-medium text-primary hover:bg-background">

                                                        View Details

                                                    </a>

                                                    @can('manageDocuments')

                                                        @if (! $document->is_system_generated)

                                                            <a
                                                                href="{{ route('documents.edit', $document) }}#edit-metadata"
                                                                class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-background">

                                                                Edit Metadata

                                                            </a>

                                                            <a
                                                                href="{{ route('documents.edit', $document) }}#move-document"
                                                                class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-background">

                                                                Move to Folder

                                                            </a>

                                                            <a
                                                                href="{{ route('documents.edit', $document) }}#upload-version"
                                                                class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-background">

                                                                Upload New Version

                                                            </a>

                                                        @endif

                                                    @endcan


                                                    @if ($document->file_uri)

                                                        <button
                                                            type="button"
                                                            data-document-download="{{ route('documents.request-link', $document) }}"
                                                            class="block w-full px-4 py-2.5 text-left text-sm text-slate-700 hover:bg-background">

                                                            Download File

                                                        </button>

                                                    @endif


                                                    @if ($document->source_module === 'reservations')

                                                        <a
                                                            href="{{ route('reservations.index') }}"
                                                            class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-background">

                                                            Open Facilities Reservations

                                                        </a>

                                                    @elseif ($document->source_module === 'visitors')

                                                        <a
                                                            href="{{ route('visitors.index') }}"
                                                            class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-background">

                                                            Open Visitor Management

                                                        </a>

                                                    @elseif ($document->source_module === 'contracts')

                                                        <a
                                                            href="{{ route('contracts.index') }}"
                                                            class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-background">

                                                            Open Contract Management

                                                        </a>

                                                    @elseif ($document->source_module === 'legal')

                                                        <a
                                                            href="{{ route('legal.index') }}"
                                                            class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-background">

                                                            Open Legal Management

                                                        </a>

                                                    @endif


                                                    @can('manageDocuments')

                                                        <div class="my-1 border-t border-border"></div>


                                                        @if ($document->status === 'archived')

                                                            <form
                                                                method="POST"
                                                                action="{{ route('documents.restore', $document) }}">

                                                                @csrf

                                                                <button
                                                                    type="submit"
                                                                    class="block w-full px-4 py-2.5 text-left text-sm font-medium text-success hover:bg-success/5">

                                                                    Restore Document

                                                                </button>

                                                            </form>

                                                        @else

                                                            <form
                                                                method="POST"
                                                                action="{{ route('documents.archive', $document) }}"
                                                                onsubmit="return confirm('Archive this document? It will remain stored and can be restored later.')">

                                                                @csrf

                                                                <button
                                                                    type="submit"
                                                                    class="block w-full px-4 py-2.5 text-left text-sm font-medium text-error hover:bg-error/5">

                                                                    Archive Document

                                                                </button>

                                                            </form>

                                                        @endif

                                                    @endcan

                                                </div>

                                            </details>

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="6" class="px-6 py-16">

                                        <div class="mx-auto max-w-sm text-center">

                                            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-accent/10 text-accent">
                                                📁
                                            </div>

                                            <h3 class="font-heading text-base font-semibold text-primary">
                                                No documents found
                                            </h3>

                                            <p class="mt-1 text-sm text-slate-500">
                                                This container does not currently contain any matching documents.
                                            </p>

                                        </div>

                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>


            @if ($documents->hasPages())

                <div class="card px-5 py-4">
                    {{ $documents->links() }}
                </div>

            @endif

        </main>

    </div>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
     * Related-record selector
     */
    const typeSelect =
        document.getElementById('related_type');

    const recordSelect =
        document.getElementById('related_id');

    if (typeSelect && recordSelect) {

        const recordOptions = Array.from(
            recordSelect.querySelectorAll(
                'option[data-related-type]'
            )
        );

        function syncRelatedRecords(
            clearSelection = false
        ) {
            const selectedType =
                typeSelect.value;

            if (clearSelection) {
                recordSelect.value = '';
            }

            recordOptions.forEach(function (option) {

                const matches =
                    selectedType &&
                    option.dataset.relatedType ===
                        selectedType;

                option.hidden = !matches;
                option.disabled = !matches;
            });

            recordSelect.disabled =
                !selectedType;
        }

        typeSelect.addEventListener(
            'change',
            function () {
                syncRelatedRecords(true);
            }
        );

        syncRelatedRecords(false);
    }


    /*
     * Secure signed document download
     */
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
                    'Preparing download...';

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
                            'Unable to generate secure download link.'
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


<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll(
        '[data-container-toggle]'
    ).forEach(function (button) {

        button.addEventListener(
            'click',
            function () {

                const targetId =
                    button.dataset.containerToggle;

                const target =
                    document.getElementById(targetId);

                if (!target) {
                    return;
                }

                const isHidden =
                    target.classList.contains('hidden');

                target.classList.toggle(
                    'hidden',
                    !isHidden
                );

                button.setAttribute(
                    'aria-expanded',
                    isHidden ? 'true' : 'false'
                );

                const chevron =
                    button.querySelector(
                        '.document-tree-chevron'
                    );

                if (chevron) {
                    chevron.classList.toggle(
                        'rotate-90',
                        isHidden
                    );
                }

                button.title =
                    isHidden
                        ? 'Collapse folder'
                        : 'Expand folder';
            }
        );

    });

});
</script>
@endsection