@extends('layouts.app')

@section('title', 'Document Management')

@section('content')

<div class="space-y-6">

    {{-- =====================================================
         DOCUMENT MANAGEMENT WORKSPACE HEADER
    ====================================================== --}}
    <x-page-header
        eyebrow="Records Workspace"
        title="Document Management"
        badge="Controlled Library"
        description="Organize, secure, retrieve, version, review, and archive organizational records from one controlled library.">

        @can('manageDocuments')

            <x-slot:actions>

                <a
                    href="#upload-document"
                    data-document-upload-open
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
                            d="M12 5v14M5 12h14" />

                    </svg>

                    Upload Document

                </a>

            </x-slot:actions>

        @endcan

    </x-page-header>


    {{-- =====================================================
         DOCUMENT LIBRARY OVERVIEW
    ====================================================== --}}
    <section class="space-y-4">

        <x-section-header
            eyebrow="Overview"
            title="Library Status"
            description="A current snapshot of the document records available within your access scope." />


        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">

            <x-metric-card
                label="Total Documents"
                :value="number_format($counts['total'])"
                :href="route('documents.index')"
                helper="All document records currently visible to your role."
                tone="primary">

                <x-slot:icon>

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

                </x-slot:icon>

            </x-metric-card>


            <x-metric-card
                label="Active"
                :value="number_format($counts['active'])"
                :href="route('documents.index', ['status' => 'active'])"
                helper="Current records available for normal operational use."
                tone="success">

                <x-slot:icon>

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 13l4 4L19 7" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>


            <x-metric-card
                label="Needs Review"
                :value="number_format($counts['needs_review'])"
                :href="route('documents.index', ['status' => 'needs_review'])"
                helper="Records currently requiring review or administrative attention."
                tone="warning">

                <x-slot:icon>

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 9v4m0 4h.01M10.3 4.3L2.8 17.3A2 2 0 004.5 20h15a2 2 0 001.7-2.7L13.7 4.3a2 2 0 00-3.4 0z" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>


            <x-metric-card
                label="Archived"
                :value="number_format($counts['archived'])"
                :href="route('documents.index', ['status' => 'archived'])"
                helper="Historical records retained outside the active document set."
                tone="accent">

                <x-slot:icon>

                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 8h14v12H5V8zm-1-4h16v4H4V4zm5 8h6" />

                    </svg>

                </x-slot:icon>

            </x-metric-card>

        </div>

    </section>

    {{-- =====================================================
         DOCUMENT LIBRARY
    ====================================================== --}}
    <x-section-header
        eyebrow="Library"
        title="Document Library"
        description="Browse folders, locate records, manage document lifecycle, and open related operational records." />


    {{-- Main Library --}}
    <div class="grid gap-4 xl:grid-cols-[260px_minmax(0,1fr)]">

        {{-- =====================================================
             MOBILE DOCUMENT LIBRARY
        ====================================================== --}}
        <details class="card group overflow-hidden xl:hidden">

            <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-4 py-3">

                <div class="flex min-w-0 items-center gap-3">

                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary/5 text-primary">

                        <svg
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 7h6l2 2h10v10H3V7z" />

                        </svg>

                    </span>


                    <div class="min-w-0">

                        <p class="font-heading text-sm font-semibold text-primary">
                            Document Library
                        </p>

                        <p class="mt-0.5 truncate text-[10px] text-slate-400">

                            {{
                                $selectedContainer
                                    ? $selectedContainer->name
                                    : 'Browse folders'
                            }}

                        </p>

                    </div>

                </div>


                <svg
                    class="h-4 w-4 shrink-0 text-slate-400 transition-transform duration-200 group-open:rotate-180"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 9l-7 7-7-7" />

                </svg>

            </summary>


            <div class="border-t border-border">

                @include(
                    'documents._library-tree',
                    [
                        'treePrefix' =>
                            'mobile-document-library',
                    ]
                )

            </div>

        </details>


        {{-- =====================================================
             DESKTOP DOCUMENT LIBRARY
        ====================================================== --}}
        <aside class="hidden h-fit overflow-hidden rounded-xl border border-border bg-card xl:sticky xl:top-24 xl:block">

            <div class="border-b border-border px-4 py-4">

                <div class="flex items-center gap-3">

                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary/5 text-primary">

                        <svg
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 7h6l2 2h10v10H3V7z" />

                        </svg>

                    </span>


                    <div>

                        <h2 class="font-heading text-sm font-semibold text-primary">
                            Document Library
                        </h2>

                        <p class="mt-0.5 text-[10px] text-slate-400">
                            Browse by folder
                        </p>

                    </div>

                </div>

            </div>


            <div class="max-h-[calc(100vh-12rem)] overflow-y-auto">

                @include(
                    'documents._library-tree',
                    [
                        'treePrefix' =>
                            'desktop-document-library',
                    ]
                )

            </div>

        </aside>

        {{-- Documents --}}
        <main class="min-w-0 space-y-4">

            {{-- =====================================================
                 DOCUMENT WORKSPACE TOOLBAR
            ====================================================== --}}
            <section class="card overflow-hidden">

                {{-- Current location --}}
                <div class="flex flex-col gap-3 border-b border-border px-4 py-4 sm:px-5 lg:flex-row lg:items-center lg:justify-between">

                    <div class="min-w-0">

                        {{-- Breadcrumb --}}
                        <div class="flex flex-wrap items-center gap-1.5 text-[10px] text-slate-400">

                            <a
                                href="{{ route('documents.index') }}"
                                class="font-semibold text-primary transition hover:text-secondary">

                                Document Management

                            </a>


                            @foreach ($breadcrumbs as $breadcrumb)

                                <svg
                                    class="h-3 w-3 shrink-0"
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


                        <div class="mt-1.5 flex flex-wrap items-center gap-2">

                            <h2 class="font-heading text-base font-semibold text-primary">

                                @if ($selectedContainer)

                                    {{ $selectedContainer->name }}

                                @elseif (request('status') === 'archived')

                                    Archived Documents

                                @elseif (request('status') === 'needs_review')

                                    Needs Review

                                @elseif (request('status') === 'active')

                                    Active Documents

                                @elseif (request('status') === 'superseded')

                                    Superseded Documents

                                @else

                                    All Documents

                                @endif

                            </h2>


                            <span class="rounded-full bg-primary/5 px-2 py-0.5 text-[10px] font-semibold text-primary">
                                {{ number_format($documents->total()) }}
                            </span>

                        </div>


                        @if ($documents->total() > 0)

                            <p class="mt-1 text-[10px] text-slate-400">

                                Showing
                                {{ number_format($documents->firstItem()) }}
                                –
                                {{ number_format($documents->lastItem()) }}
                                of
                                {{ number_format($documents->total()) }}

                            </p>

                        @endif

                    </div>


                    @if (
                        request()->filled('search')
                        || request()->filled('category')
                        || request()->filled('status')
                    )

                        <a
                            href="{{ route(
                                'documents.index',
                                $selectedContainer
                                    ? ['container' => $selectedContainer->id]
                                    : []
                            ) }}"
                            class="inline-flex shrink-0 items-center gap-1.5 self-start text-xs font-semibold text-slate-400 transition hover:text-primary lg:self-auto">

                            <svg
                                class="h-3.5 w-3.5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />

                            </svg>

                            Clear filters

                        </a>

                    @endif

                </div>


                {{-- Search and filters --}}
                <form
                    method="GET"
                    action="{{ route('documents.index') }}"
                    class="px-4 py-3 sm:px-5">

                    @if ($selectedContainer)

                        <input
                            type="hidden"
                            name="container"
                            value="{{ $selectedContainer->id }}">

                    @endif


                    <div class="grid gap-2 md:grid-cols-2 xl:grid-cols-12">

                        {{-- Search --}}
                        <div class="relative md:col-span-2 xl:col-span-6">

                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">

                                <svg
                                    class="h-4 w-4 text-slate-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M21 21l-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z" />

                                </svg>

                            </div>


                            <input
                                type="search"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Search title, filename, reference, or related record..."
                                class="input pl-9">

                        </div>


                        {{-- Category --}}
                        <div class="xl:col-span-2">

                            <select
                                name="category"
                                class="input"
                                aria-label="Document category">

                                <option value="">
                                    All Categories
                                </option>

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
                                ] as $category)

                                    <option
                                        value="{{ $category }}"
                                        @selected(request('category') === $category)>

                                        {{ str($category)->headline() }}

                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Status --}}
                        <div class="xl:col-span-2">

                            <select
                                name="status"
                                class="input"
                                aria-label="Document status">

                                <option value="">
                                    All Statuses
                                </option>

                                @foreach ([
                                    'active',
                                    'needs_review',
                                    'archived',
                                    'superseded',
                                ] as $status)

                                    <option
                                        value="{{ $status }}"
                                        @selected(request('status') === $status)>

                                        {{ str($status)->headline() }}

                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Apply --}}
                        <div class="xl:col-span-2">

                            <button
                                type="submit"
                                class="btn-secondary w-full justify-center">

                                Apply

                            </button>

                        </div>

                    </div>

                </form>

            </section>

            {{-- =====================================================
                 MODERN UPLOAD DOCUMENT MODAL
            ====================================================== --}}
            @can('manageDocuments')

                <div
                    id="upload-document"
                    data-document-upload-modal
                    @if ($errors->any() && old('category') !== null)
                        data-open-on-error="true"
                    @endif
                    class="fixed inset-0 z-[80] hidden"
                    role="dialog"
                    aria-modal="true"
                    aria-labelledby="upload-document-title">

                    {{-- Backdrop --}}
                    <div
                        data-document-upload-backdrop
                        class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm">
                    </div>


                    <div class="relative flex min-h-full items-center justify-center p-2 sm:p-5">

                        <div
                            data-document-upload-panel
                            class="flex max-h-[calc(100vh-1rem)] w-full max-w-3xl flex-col overflow-hidden rounded-2xl border border-border bg-card shadow-2xl sm:max-h-[calc(100vh-2.5rem)]">

                            {{-- =====================================================
                                 HEADER
                            ====================================================== --}}
                            <div class="flex shrink-0 items-start justify-between gap-4 border-b border-border px-5 py-4 sm:px-6">

                                <div class="flex min-w-0 items-start gap-3">

                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary">

                                        <svg
                                            class="h-5 w-5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24">

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 16V4m0 0L7 9m5-5l5 5M5 20h14" />

                                        </svg>

                                    </div>


                                    <div class="min-w-0">

                                        <p class="text-[9px] font-semibold uppercase tracking-[0.16em] text-secondary">
                                            Document Management
                                        </p>

                                        <h2
                                            id="upload-document-title"
                                            class="mt-0.5 font-heading text-lg font-semibold text-primary">

                                            Upload Document

                                        </h2>

                                        <p class="mt-1 text-xs text-slate-500">
                                            Add a record to the controlled document library.
                                        </p>


                                        @if ($selectedContainer)

                                            <div class="mt-2 inline-flex max-w-full items-center gap-1.5 rounded-lg bg-primary/5 px-2 py-1 text-[10px] font-medium text-primary">

                                                <svg
                                                    class="h-3 w-3 shrink-0"
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
                                                    {{ str_replace('/', ' / ', $selectedContainer->path) }}
                                                </span>

                                            </div>

                                        @endif

                                    </div>

                                </div>


                                <button
                                    type="button"
                                    data-document-upload-close
                                    aria-label="Close upload document dialog"
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-background hover:text-primary">

                                    <svg
                                        class="h-5 w-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24">

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />

                                    </svg>

                                </button>

                            </div>


                            <form
                                method="POST"
                                action="{{ route('documents.store') }}"
                                enctype="multipart/form-data"
                                class="flex min-h-0 flex-1 flex-col">

                                @csrf


                                {{-- =====================================================
                                     SCROLLABLE CONTENT
                                ====================================================== --}}
                                <div class="min-h-0 flex-1 overflow-y-auto px-5 py-5 sm:px-6">

                                    <div class="space-y-5">

                                        {{-- Validation --}}
                                        @if ($errors->any() && old('category') !== null)

                                            <div class="rounded-xl border border-error/20 bg-error/5 px-4 py-3">

                                                <div class="flex items-start gap-3">

                                                    <svg
                                                        class="mt-0.5 h-4 w-4 shrink-0 text-error"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24">

                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 9v3m0 4h.01M10.3 3.6L2.5 17a2 2 0 001.7 3h15.6a2 2 0 001.7-3L13.7 3.6a2 2 0 00-3.4 0z" />

                                                    </svg>


                                                    <div class="min-w-0">

                                                        <p class="text-xs font-semibold text-error">
                                                            Please review the highlighted information.
                                                        </p>

                                                        <ul class="mt-1.5 list-disc space-y-0.5 pl-4 text-[10px] text-error">

                                                            @foreach ($errors->all() as $error)

                                                                <li>
                                                                    {{ $error }}
                                                                </li>

                                                            @endforeach

                                                        </ul>

                                                    </div>

                                                </div>

                                            </div>

                                        @endif


                                        {{-- =====================================================
                                             FILE FIRST
                                        ====================================================== --}}
                                        <section>

                                            <div class="mb-2 flex items-center justify-between">

                                                <div>

                                                    <h3 class="font-heading text-xs font-semibold text-primary">
                                                        Document File
                                                    </h3>

                                                    <p class="mt-0.5 text-[10px] text-slate-400">
                                                        Select the file that will be stored with this record.
                                                    </p>

                                                </div>

                                            </div>


                                            <label class="group relative block cursor-pointer">

                                                <input
                                                    type="file"
                                                    name="file"
                                                    accept="{{ \App\Support\DocumentUploadPolicy::acceptAttribute() }}"
                                                    data-document-file-input
                                                    class="absolute inset-0 z-10 h-full w-full cursor-pointer opacity-0">


                                                <div class="rounded-xl border border-dashed border-accent/40 bg-accent/5 px-4 py-5 text-center transition group-hover:border-accent/70 group-hover:bg-accent/10">

                                                    <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-xl bg-card text-primary shadow-sm ring-1 ring-border">

                                                        <svg
                                                            class="h-5 w-5"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            viewBox="0 0 24 24">

                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 16V4m0 0L7 9m5-5l5 5M5 20h14" />

                                                        </svg>

                                                    </div>


                                                    <p class="mt-3 text-xs font-semibold text-primary">
                                                        Choose a document file
                                                    </p>

                                                    <p
                                                        data-document-file-name
                                                        class="mt-1 truncate text-[10px] text-slate-400">

                                                        No file selected

                                                    </p>

                                                </div>

                                            </label>

                                        </section>


                                        <div class="border-t border-border"></div>


                                        {{-- =====================================================
                                             ESSENTIAL DETAILS
                                        ====================================================== --}}
                                        <section>

                                            <div class="mb-3">

                                                <h3 class="font-heading text-xs font-semibold text-primary">
                                                    Document Details
                                                </h3>

                                                <p class="mt-0.5 text-[10px] text-slate-400">
                                                    Required information used to classify and secure the record.
                                                </p>

                                            </div>


                                            <div class="grid gap-3 sm:grid-cols-2">

                                                {{-- Title --}}
                                                <div class="sm:col-span-2">

                                                    <label class="label">
                                                        Document Title
                                                        <span class="text-error">*</span>
                                                    </label>

                                                    <input
                                                        name="title"
                                                        value="{{ old('title') }}"
                                                        placeholder="Enter a clear document title"
                                                        required
                                                        autofocus
                                                        class="input">

                                                </div>


                                                {{-- Category --}}
                                                <div>

                                                    <label class="label">
                                                        Category
                                                        <span class="text-error">*</span>
                                                    </label>

                                                    <select
                                                        name="category"
                                                        required
                                                        class="input">

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
                                                            'other',
                                                        ] as $category)

                                                            <option
                                                                value="{{ $category }}"
                                                                @selected(old('category', 'administrative') === $category)>

                                                                {{ str($category)->headline() }}

                                                            </option>

                                                        @endforeach

                                                    </select>

                                                </div>


                                                {{-- Confidentiality --}}
                                                <div>

                                                    <label class="label">
                                                        Confidentiality
                                                        <span class="text-error">*</span>
                                                    </label>

                                                    <select
                                                        name="confidentiality"
                                                        required
                                                        class="input">

                                                        <option
                                                            value="general"
                                                            @selected(old('confidentiality', 'general') === 'general')>

                                                            General

                                                        </option>

                                                        <option
                                                            value="restricted"
                                                            @selected(old('confidentiality') === 'restricted')>

                                                            Restricted

                                                        </option>

                                                        <option
                                                            value="confidential"
                                                            @selected(old('confidentiality') === 'confidential')>

                                                            Confidential

                                                        </option>

                                                    </select>

                                                </div>


                                                {{-- Folder --}}
                                                <div class="sm:col-span-2">

                                                    <label class="label">
                                                        Folder
                                                    </label>

                                                    <select
                                                        name="container_id"
                                                        class="input">

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
                                                                    ) ===
                                                                    (string) $container->id
                                                                )>

                                                                {{ str_replace('/', ' / ', $container->path) }}

                                                            </option>

                                                        @endforeach

                                                    </select>

                                                </div>

                                            </div>

                                        </section>


                                        {{-- =====================================================
                                             OPTIONAL METADATA
                                        ====================================================== --}}
                                        <details
                                            @if (
                                                old('status')
                                                || old('department')
                                                || old('owner_email')
                                                || old('document_date')
                                                || old('expiration_date')
                                            )
                                                open
                                            @endif
                                            class="group overflow-hidden rounded-xl border border-border">

                                            <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-4 py-3 transition hover:bg-background/60">

                                                <div>

                                                    <p class="text-xs font-semibold text-primary">
                                                        Additional Details
                                                    </p>

                                                    <p class="mt-0.5 text-[10px] text-slate-400">
                                                        Status, ownership, department, and important dates.
                                                    </p>

                                                </div>


                                                <svg
                                                    class="h-4 w-4 shrink-0 text-slate-400 transition-transform duration-200 group-open:rotate-180"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24">

                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 9l-7 7-7-7" />

                                                </svg>

                                            </summary>


                                            <div class="grid gap-3 border-t border-border bg-background/25 p-4 sm:grid-cols-2">

                                                <div>

                                                    <label class="label">
                                                        Status
                                                    </label>

                                                    <select
                                                        name="status"
                                                        class="input">

                                                        <option
                                                            value="active"
                                                            @selected(old('status', 'active') === 'active')>

                                                            Active

                                                        </option>

                                                        <option
                                                            value="needs_review"
                                                            @selected(old('status') === 'needs_review')>

                                                            Needs Review

                                                        </option>

                                                        <option
                                                            value="archived"
                                                            @selected(old('status') === 'archived')>

                                                            Archived

                                                        </option>

                                                        <option
                                                            value="superseded"
                                                            @selected(old('status') === 'superseded')>

                                                            Superseded

                                                        </option>

                                                    </select>

                                                </div>


                                                <div>

                                                    <label class="label">
                                                        Department
                                                    </label>

                                                    <input
                                                        name="department"
                                                        value="{{ old('department') }}"
                                                        placeholder="e.g. Administration"
                                                        class="input">

                                                </div>


                                                <div class="sm:col-span-2">

                                                    <label class="label">
                                                        Owner Email
                                                    </label>

                                                    <input
                                                        type="email"
                                                        name="owner_email"
                                                        value="{{ old('owner_email') }}"
                                                        placeholder="name@example.com"
                                                        class="input">

                                                </div>


                                                <div>

                                                    <label class="label">
                                                        Document Date
                                                    </label>

                                                    <input
                                                        type="date"
                                                        name="document_date"
                                                        value="{{ old('document_date') }}"
                                                        class="input">

                                                </div>


                                                <div>

                                                    <label class="label">
                                                        Expiration Date
                                                    </label>

                                                    <input
                                                        type="date"
                                                        name="expiration_date"
                                                        value="{{ old('expiration_date') }}"
                                                        class="input">

                                                </div>

                                            </div>

                                        </details>


                                        {{-- =====================================================
                                             RELATED RECORD
                                        ====================================================== --}}
                                        <details
                                            @if (old('related_type'))
                                                open
                                            @endif
                                            class="group overflow-hidden rounded-xl border border-border">

                                            <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-4 py-3 transition hover:bg-background/60">

                                                <div>

                                                    <p class="text-xs font-semibold text-primary">
                                                        Link to System Record
                                                    </p>

                                                    <p class="mt-0.5 text-[10px] text-slate-400">
                                                        Optional connection to a contract, legal record, reservation, or visitor.
                                                    </p>

                                                </div>


                                                <span class="flex items-center gap-2">

                                                    <span class="rounded-full bg-primary/5 px-2 py-0.5 text-[9px] font-semibold text-slate-500">
                                                        Optional
                                                    </span>

                                                    <svg
                                                        class="h-4 w-4 text-slate-400 transition-transform duration-200 group-open:rotate-180"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24">

                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 9l-7 7-7-7" />

                                                    </svg>

                                                </span>

                                            </summary>


                                            <div class="grid gap-3 border-t border-border bg-background/25 p-4 sm:grid-cols-2">

                                                <div>

                                                    <label class="label">
                                                        Related Module
                                                    </label>

                                                    <select
                                                        id="related_type"
                                                        name="related_type"
                                                        class="input">

                                                        <option value="">
                                                            Not linked
                                                        </option>


                                                        @if ($contracts->isNotEmpty())

                                                            <option
                                                                value="contract"
                                                                @selected(old('related_type') === 'contract')>

                                                                Contract

                                                            </option>

                                                        @endif


                                                        @if ($legalRecords->isNotEmpty())

                                                            <option
                                                                value="legal"
                                                                @selected(old('related_type') === 'legal')>

                                                                Legal Record

                                                            </option>

                                                        @endif


                                                        @if ($reservations->isNotEmpty())

                                                            <option
                                                                value="reservation"
                                                                @selected(old('related_type') === 'reservation')>

                                                                Facility Reservation

                                                            </option>

                                                        @endif


                                                        @if ($visitors->isNotEmpty())

                                                            <option
                                                                value="visitor"
                                                                @selected(old('related_type') === 'visitor')>

                                                                Visitor Record

                                                            </option>

                                                        @endif

                                                    </select>

                                                </div>


                                                <div
                                                    data-related-record-wrapper
                                                    class="{{ old('related_type') ? '' : 'hidden' }}">

                                                    <label class="label">
                                                        Related Record
                                                    </label>

                                                    <select
                                                        id="related_id"
                                                        name="related_id"
                                                        class="input"
                                                        disabled>

                                                        <option value="">
                                                            Select a record
                                                        </option>


                                                        @foreach ($contracts as $contract)

                                                            <option
                                                                value="{{ $contract->id }}"
                                                                data-related-type="contract"
                                                                @selected(
                                                                    old('related_type') === 'contract'
                                                                    &&
                                                                    (string) old('related_id') ===
                                                                    (string) $contract->id
                                                                )>

                                                                {{ $contract->contract_number ?: 'Contract #'.$contract->id }}
                                                                - {{ $contract->title }}

                                                            </option>

                                                        @endforeach


                                                        @foreach ($legalRecords as $legalRecord)

                                                            <option
                                                                value="{{ $legalRecord->id }}"
                                                                data-related-type="legal"
                                                                @selected(
                                                                    old('related_type') === 'legal'
                                                                    &&
                                                                    (string) old('related_id') ===
                                                                    (string) $legalRecord->id
                                                                )>

                                                                {{ $legalRecord->reference_number ?: 'Legal #'.$legalRecord->id }}
                                                                - {{ $legalRecord->title }}

                                                            </option>

                                                        @endforeach


                                                        @foreach ($reservations as $reservation)

                                                            <option
                                                                value="{{ $reservation->id }}"
                                                                data-related-type="reservation"
                                                                @selected(
                                                                    old('related_type') === 'reservation'
                                                                    &&
                                                                    (string) old('related_id') ===
                                                                    (string) $reservation->id
                                                                )>

                                                                #{{ $reservation->id }}
                                                                - {{ $reservation->facility_name }}
                                                                - {{ $reservation->date?->format('M d, Y') }}

                                                            </option>

                                                        @endforeach


                                                        @foreach ($visitors as $visitor)

                                                            <option
                                                                value="{{ $visitor->id }}"
                                                                data-related-type="visitor"
                                                                @selected(
                                                                    old('related_type') === 'visitor'
                                                                    &&
                                                                    (string) old('related_id') ===
                                                                    (string) $visitor->id
                                                                )>

                                                                #{{ $visitor->id }}
                                                                - {{ $visitor->full_name }}

                                                            </option>

                                                        @endforeach

                                                    </select>

                                                </div>

                                            </div>

                                        </details>


                                        {{-- Description --}}
                                        <section>

                                            <label class="label">
                                                Description
                                            </label>

                                            <textarea
                                                name="description"
                                                rows="3"
                                                placeholder="Add a short description or useful filing notes..."
                                                class="input resize-y">{{ old('description') }}</textarea>

                                        </section>

                                    </div>

                                </div>


                                {{-- =====================================================
                                     FOOTER
                                ====================================================== --}}
                                <div class="flex shrink-0 flex-col-reverse gap-2 border-t border-border bg-background/40 px-5 py-3 sm:flex-row sm:items-center sm:justify-between sm:px-6">

                                    <p class="text-[10px] text-slate-400">
                                        <span class="text-error">*</span>
                                        Required fields
                                    </p>


                                    <div class="flex flex-col-reverse gap-2 sm:flex-row">

                                        <button
                                            type="button"
                                            data-document-upload-close
                                            class="btn-outline justify-center">

                                            Cancel

                                        </button>


                                        <button
                                            type="submit"
                                            class="btn-primary justify-center">

                                            <svg
                                                class="h-4 w-4"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24">

                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M12 16V4m0 0L7 9m5-5l5 5M5 20h14" />

                                            </svg>

                                            Upload Document

                                        </button>

                                    </div>

                                </div>

                            </form>

                        </div>

                    </div>

                </div>


                <script>
                document.addEventListener(
                    'DOMContentLoaded',
                    () => {

                        /*
                         * Selected file name.
                         */
                        const fileInput =
                            document.querySelector(
                                '[data-document-file-input]'
                            );

                        const fileName =
                            document.querySelector(
                                '[data-document-file-name]'
                            );

                        if (
                            fileInput
                            &&
                            fileName
                        ) {

                            fileInput.addEventListener(
                                'change',
                                () => {

                                    fileName.textContent =
                                        fileInput.files?.[0]?.name
                                        ??
                                        'No file selected';

                                }
                            );

                        }


                        /*
                         * Progressive related-record field.
                         *
                         * Existing related-record filtering logic
                         * still controls the actual select options.
                         */
                        const relatedType =
                            document.getElementById(
                                'related_type'
                            );

                        const relatedWrapper =
                            document.querySelector(
                                '[data-related-record-wrapper]'
                            );

                        const updateRelatedVisibility =
                            () => {

                                if (
                                    !relatedType
                                    ||
                                    !relatedWrapper
                                ) {
                                    return;
                                }

                                relatedWrapper.classList.toggle(
                                    'hidden',
                                    !relatedType.value
                                );

                            };


                        relatedType?.addEventListener(
                            'change',
                            updateRelatedVisibility
                        );


                        updateRelatedVisibility();

                    }
                );
                </script>

            @endcan

            @can('manageDocuments')

                <form
                    id="document-bulk-form"
                    method="POST"
                    action="{{ route('documents.bulk-action') }}"
                    data-document-bulk-toolbar
                    class="hidden rounded-xl border border-accent/25 bg-card px-4 py-3 shadow-sm">

                    @csrf

                    <input
                        type="hidden"
                        name="action"
                        data-document-bulk-action>


                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                        <div class="flex items-center gap-3">

                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-accent/10 text-primary">

                                <svg
                                    class="h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 13l4 4L19 7" />

                                </svg>

                            </div>


                            <p
                                data-document-bulk-count
                                class="text-sm font-semibold text-primary">

                                0 documents selected

                            </p>

                        </div>


                        <div class="flex flex-wrap items-center gap-2">

                            <button
                                type="button"
                                data-document-bulk-archive
                                class="hidden items-center justify-center rounded-lg border border-error/20 bg-error/5 px-3 py-2 text-xs font-semibold text-error transition hover:bg-error/10">

                                Archive

                            </button>


                            <button
                                type="button"
                                data-document-bulk-restore
                                class="hidden items-center justify-center rounded-lg border border-success/20 bg-success/5 px-3 py-2 text-xs font-semibold text-success transition hover:bg-success/10">

                                Restore

                            </button>


                            <button
                                type="button"
                                data-document-bulk-clear
                                class="inline-flex items-center justify-center rounded-lg px-3 py-2 text-xs font-semibold text-slate-500 transition hover:bg-background hover:text-primary">

                                Clear selection

                            </button>

                        </div>

                    </div>

                </form>

            @endcan

            {{-- =====================================================
                 MODERN DOCUMENT DIRECTORY
            ====================================================== --}}
            @include('documents._mobile-cards')

            <div class="table-shell hidden md:block">

                <div class="overflow-x-auto">

                    <table class="min-w-full text-left">

                        <thead class="table-header">

                            <tr>

                                @can('manageDocuments')

                                    <th class="w-12 px-4 py-3">

                                        <input
                                            type="checkbox"
                                            data-document-select-all
                                            aria-label="Select all documents on this page"
                                            class="h-4 w-4 rounded border-border text-primary focus:ring-primary">

                                    </th>

                                @endcan


                                <th class="px-4 py-3 text-[10px] font-semibold uppercase tracking-wide">
                                    Document
                                </th>

                                <th class="w-36 px-4 py-3 text-[10px] font-semibold uppercase tracking-wide">
                                    Status
                                </th>

                                <th class="w-36 px-4 py-3 text-[10px] font-semibold uppercase tracking-wide">
                                    Updated
                                </th>

                                <th class="w-16 px-4 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-border bg-card">

                            @forelse ($documents as $document)

                                <tr
                                    data-document-row
                                    class="group transition hover:bg-background/60">

                                    @can('manageDocuments')

                                        <td class="w-12 px-4 py-4 align-top">

                                            <input
                                                type="checkbox"
                                                name="document_ids[]"
                                                value="{{ $document->id }}"
                                                form="document-bulk-form"
                                                data-document-select
                                                data-document-status="{{ $document->status }}"
                                                aria-label="Select {{ $document->title }}"
                                                class="mt-2 h-4 w-4 rounded border-border text-primary focus:ring-primary">

                                        </td>

                                    @endcan


                                    {{-- Document + context --}}
                                    <td class="min-w-[360px] px-4 py-4">

                                        <div class="flex items-start gap-3">

                                            <div
                                                @class([
                                                    'flex h-10 w-10 shrink-0 items-center justify-center rounded-xl',
                                                    'bg-accent/10 text-primary' => $document->is_system_generated,
                                                    'bg-primary/5 text-primary' => ! $document->is_system_generated,
                                                ])>

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


                                            <div class="min-w-0 flex-1">

                                                <div class="flex flex-wrap items-center gap-2">

                                                    <a
                                                        href="{{ route('documents.show', $document) }}"
                                                        class="max-w-md truncate font-button text-sm font-semibold text-primary transition hover:text-secondary">

                                                        {{ $document->title }}

                                                    </a>


                                                    @if ($document->is_system_generated)

                                                        <span class="rounded-full bg-accent/10 px-2 py-0.5 text-[9px] font-semibold text-primary">
                                                            System
                                                        </span>

                                                    @else

                                                        <span class="rounded-full bg-primary/5 px-2 py-0.5 text-[9px] font-semibold text-primary">
                                                            v{{ $document->version ?? 1 }}
                                                        </span>

                                                    @endif

                                                </div>


                                                {{-- Core metadata --}}
                                                <div class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-1 text-[11px] text-slate-500">

                                                    <span class="font-medium">
                                                        {{ str($document->category)->headline() }}
                                                    </span>


                                                    @if ($document->file_name)

                                                        <span class="text-slate-300">
                                                            &bull;
                                                        </span>

                                                        <span
                                                            class="max-w-[260px] truncate"
                                                            title="{{ $document->file_name }}">

                                                            {{ $document->file_name }}

                                                        </span>

                                                    @endif


                                                    <span class="text-slate-300">
                                                        &bull;
                                                    </span>

                                                    <span>
                                                        {{
                                                            str(
                                                                $document->confidentiality
                                                                ?? 'general'
                                                            )->headline()
                                                        }}
                                                    </span>

                                                </div>


                                                {{-- Location / related context --}}
                                                @if (
                                                    $document->container
                                                    ||
                                                    $document->relatedModuleLabel()
                                                )

                                                    <div class="mt-2 flex flex-wrap items-center gap-1.5">

                                                        @if ($document->container)

                                                            <a
                                                                href="{{ route('documents.index', ['container' => $document->container->id]) }}"
                                                                class="inline-flex max-w-[260px] items-center gap-1 rounded-md bg-primary/5 px-2 py-1 text-[9px] font-medium text-primary transition hover:bg-primary/10">

                                                                <svg
                                                                    class="h-3 w-3 shrink-0"
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

                                                            <span class="rounded-md bg-background px-2 py-1 text-[9px] text-slate-400">
                                                                Unfiled
                                                            </span>

                                                        @endif


                                                        @if ($document->relatedModuleLabel())

                                                            <span
                                                                class="inline-flex max-w-[280px] items-center gap-1 rounded-md bg-accent/10 px-2 py-1 text-[9px] font-medium text-primary"
                                                                title="{{ $document->relatedRecordLabel() }}">

                                                                <span>
                                                                    {{ $document->relatedModuleLabel() }}
                                                                </span>

                                                                @if ($document->relatedRecordLabel())

                                                                    <span class="text-slate-300">
                                                                        &bull;
                                                                    </span>

                                                                    <span class="truncate">
                                                                        {{ $document->relatedRecordLabel() }}
                                                                    </span>

                                                                @endif

                                                            </span>

                                                        @endif

                                                    </div>

                                                @endif

                                            </div>

                                        </div>

                                    </td>


                                    {{-- Status --}}
                                    <td class="whitespace-nowrap px-4 py-4 align-top">

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


                                    {{-- Updated --}}
                                    <td class="whitespace-nowrap px-4 py-4 align-top">

                                        <p class="text-xs font-medium text-slate-600">
                                            {{ $document->updated_at?->format('M d, Y') }}
                                        </p>

                                        <p class="mt-0.5 text-[10px] text-slate-400">
                                            {{ $document->updated_at?->format('h:i A') }}
                                        </p>

                                    </td>


                                    {{-- Actions --}}
                                    <td class="px-4 py-4 align-top">

                                        <div class="flex justify-end">

                                            @include(
                                                'documents._row-actions',
                                                [
                                                    'document' => $document,
                                                ]
                                            )

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="{{ auth()->user()->can('manageDocuments') ? 5 : 4 }}"
                                        class="px-6 py-14">

                                        <div class="mx-auto max-w-sm text-center">

                                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-accent/10 text-primary">

                                                <svg
                                                    class="h-6 w-6"
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


                                            <h3 class="mt-3 font-heading text-sm font-semibold text-primary">
                                                No documents found
                                            </h3>


                                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                                No documents match the current folder and filters.
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
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form =
        document.getElementById('document-bulk-form');

    if (!form) {
        return;
    }

    const selectAll =
        document.querySelector(
            '[data-document-select-all]'
        );

    const checkboxes =
        Array.from(
            document.querySelectorAll(
                '[data-document-select]'
            )
        );

    const actionInput =
        form.querySelector(
            '[data-document-bulk-action]'
        );

    const archiveButton =
        form.querySelector(
            '[data-document-bulk-archive]'
        );

    const restoreButton =
        form.querySelector(
            '[data-document-bulk-restore]'
        );

    const clearButton =
        form.querySelector(
            '[data-document-bulk-clear]'
        );

    const countLabel =
        form.querySelector(
            '[data-document-bulk-count]'
        );

    const summaryLabel =
        form.querySelector(
            '[data-document-bulk-summary]'
        );


    const selectedDocuments = () => {
        return checkboxes.filter(
            (checkbox) =>
                checkbox.checked
        );
    };


    const updateState = () => {
        const selected =
            selectedDocuments();

        const selectedCount =
            selected.length;

        const archivableCount =
            selected.filter(
                (checkbox) =>
                    checkbox.dataset.documentStatus
                        !== 'archived'
            ).length;

        const restorableCount =
            selected.filter(
                (checkbox) =>
                    checkbox.dataset.documentStatus
                        === 'archived'
            ).length;


        form.classList.toggle(
            'hidden',
            selectedCount === 0
        );


        if (countLabel) {
            countLabel.textContent =
                `${selectedCount} ${
                    selectedCount === 1
                        ? 'document'
                        : 'documents'
                } selected`;
        }


        if (summaryLabel) {
            const parts = [];

            if (archivableCount > 0) {
                parts.push(
                    `${archivableCount} can be archived`
                );
            }

            if (restorableCount > 0) {
                parts.push(
                    `${restorableCount} can be restored`
                );
            }

            summaryLabel.textContent =
                parts.join(' · ');
        }


        if (archiveButton) {

            archiveButton.disabled =
                archivableCount === 0;

            archiveButton.classList.toggle(
                'hidden',
                archivableCount === 0
            );

            archiveButton.classList.toggle(
                'inline-flex',
                archivableCount > 0
            );

        }

        if (restoreButton) {

            restoreButton.disabled =
                restorableCount === 0;

            restoreButton.classList.toggle(
                'hidden',
                restorableCount === 0
            );

            restoreButton.classList.toggle(
                'inline-flex',
                restorableCount > 0
            );

        }


        if (selectAll) {
            selectAll.checked =
                checkboxes.length > 0
                && selectedCount
                    === checkboxes.length;

            selectAll.indeterminate =
                selectedCount > 0
                && selectedCount
                    < checkboxes.length;
        }


        checkboxes.forEach(
            (checkbox) => {
                const row =
                    checkbox.closest(
                        '[data-document-row]'
                    );

                if (!row) {
                    return;
                }

                row.classList.toggle(
                    'bg-accent/5',
                    checkbox.checked
                );
            }
        );
    };


    const clearSelection = () => {
        checkboxes.forEach(
            (checkbox) => {
                checkbox.checked = false;
            }
        );

        if (selectAll) {
            selectAll.checked = false;
            selectAll.indeterminate = false;
        }

        if (actionInput) {
            actionInput.value = '';
        }

        updateState();
    };


    const runBulkAction = (action) => {
        const selected =
            selectedDocuments();

        if (selected.length === 0) {
            return;
        }

        let applicableCount = 0;

        if (action === 'archive') {
            applicableCount =
                selected.filter(
                    (checkbox) =>
                        checkbox.dataset.documentStatus
                            !== 'archived'
                ).length;
        }

        if (action === 'restore') {
            applicableCount =
                selected.filter(
                    (checkbox) =>
                        checkbox.dataset.documentStatus
                            === 'archived'
                ).length;
        }

        if (applicableCount === 0) {
            return;
        }


        const message =
            action === 'archive'
                ? `Archive ${applicableCount} selected document(s)? They will remain stored and can be restored later.`
                : `Restore ${applicableCount} archived document(s) to the active library?`;


        if (!window.confirm(message)) {
            return;
        }


        actionInput.value = action;

        form.requestSubmit();
    };


    selectAll?.addEventListener(
        'change',
        () => {
            checkboxes.forEach(
                (checkbox) => {
                    checkbox.checked =
                        selectAll.checked;
                }
            );

            updateState();
        }
    );


    checkboxes.forEach(
        (checkbox) => {
            checkbox.addEventListener(
                'change',
                updateState
            );
        }
    );


    archiveButton?.addEventListener(
        'click',
        () => {
            runBulkAction('archive');
        }
    );


    restoreButton?.addEventListener(
        'click',
        () => {
            runBulkAction('restore');
        }
    );


    clearButton?.addEventListener(
        'click',
        clearSelection
    );


    document.addEventListener(
        'keydown',
        (event) => {
            if (
                event.key === 'Escape'
                && selectedDocuments().length > 0
            ) {
                clearSelection();
            }
        }
    );


    updateState();
});
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const uploadModal =
        document.querySelector(
            '[data-document-upload-modal]'
        );

    if (!uploadModal) {
        return;
    }

    const openButtons =
        document.querySelectorAll(
            '[data-document-upload-open]'
        );

    const closeButtons =
        uploadModal.querySelectorAll(
            '[data-document-upload-close]'
        );

    const backdrop =
        uploadModal.querySelector(
            '[data-document-upload-backdrop]'
        );

    const panel =
        uploadModal.querySelector(
            '[data-document-upload-panel]'
        );

    let previouslyFocused = null;
    let previousBodyOverflow = '';


    const getFocusableElements = () => {
        return Array.from(
            uploadModal.querySelectorAll(
                'button:not([disabled]), ' +
                'input:not([disabled]), ' +
                'select:not([disabled]), ' +
                'textarea:not([disabled]), ' +
                'a[href]'
            )
        ).filter(
            (element) =>
                element.offsetParent !== null
        );
    };


    const openUploadModal = () => {
        previouslyFocused =
            document.activeElement;

        previousBodyOverflow =
            document.body.style.overflow;

        uploadModal.classList.remove(
            'hidden'
        );

        document.body.style.overflow =
            'hidden';

        window.requestAnimationFrame(
            () => {
                const titleInput =
                    uploadModal.querySelector(
                        'input[name="title"]'
                    );

                titleInput?.focus();
            }
        );
    };


    const closeUploadModal = () => {
        uploadModal.classList.add(
            'hidden'
        );

        document.body.style.overflow =
            previousBodyOverflow;

        if (
            previouslyFocused
            && typeof previouslyFocused.focus
                === 'function'
        ) {
            previouslyFocused.focus();
        }
    };


    openButtons.forEach(
        (button) => {
            button.addEventListener(
                'click',
                (event) => {
                    event.preventDefault();
                    openUploadModal();
                }
            );
        }
    );


    closeButtons.forEach(
        (button) => {
            button.addEventListener(
                'click',
                closeUploadModal
            );
        }
    );


    backdrop?.addEventListener(
        'click',
        closeUploadModal
    );


    panel?.addEventListener(
        'click',
        (event) => {
            event.stopPropagation();
        }
    );


    document.addEventListener(
        'keydown',
        (event) => {
            if (
                uploadModal.classList.contains(
                    'hidden'
                )
            ) {
                return;
            }

            if (event.key === 'Escape') {
                event.preventDefault();
                closeUploadModal();
                return;
            }

            if (event.key !== 'Tab') {
                return;
            }

            const focusable =
                getFocusableElements();

            if (focusable.length === 0) {
                return;
            }

            const first =
                focusable[0];

            const last =
                focusable[
                    focusable.length - 1
                ];

            if (
                event.shiftKey
                && document.activeElement
                    === first
            ) {
                event.preventDefault();
                last.focus();
            } else if (
                !event.shiftKey
                && document.activeElement
                    === last
            ) {
                event.preventDefault();
                first.focus();
            }
        }
    );


    if (
        uploadModal.dataset.openOnError
            === 'true'
    ) {
        openUploadModal();
    }
});
</script>

@endsection