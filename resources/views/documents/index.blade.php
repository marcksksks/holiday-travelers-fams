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
                href="#upload-document" data-document-upload-open
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

            {{-- Current document location --}}
            <div class="card overflow-hidden">

                <div class="flex flex-col gap-4 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

                    <div class="min-w-0">

                        <div class="flex flex-wrap items-center gap-1.5 text-xs text-slate-400">

                            <a
                                href="{{ route('documents.index') }}"
                                class="font-medium text-primary transition hover:text-secondary">

                                Document Management

                            </a>

                            @foreach ($breadcrumbs as $breadcrumb)

                                <svg
                                    class="h-3.5 w-3.5 shrink-0 text-slate-300"
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
                                    class="max-w-[180px] truncate font-medium text-slate-500 transition hover:text-primary">

                                    {{ $breadcrumb->name }}

                                </a>

                            @endforeach

                        </div>


                        <div class="mt-2 flex flex-wrap items-center gap-3">

                            <h2 class="font-heading text-lg font-semibold text-primary">

                                @if ($selectedContainer)

                                    {{ $selectedContainer->name }}

                                @elseif (request('status') === 'archived')

                                    Archived Documents

                                @elseif (request('status') === 'needs_review')

                                    Documents Needing Review

                                @elseif (request('status') === 'active')

                                    Active Documents

                                @elseif (request('status') === 'superseded')

                                    Superseded Documents

                                @else

                                    All Documents

                                @endif

                            </h2>

                            <span class="rounded-full bg-primary/5 px-2.5 py-1 text-xs font-semibold text-primary">
                                {{ number_format($documents->total()) }}
                            </span>

                        </div>


                        <p class="mt-1 text-xs text-slate-500">

                            @if ($documents->total() > 0)

                                Showing
                                {{ number_format($documents->firstItem()) }}
                                –
                                {{ number_format($documents->lastItem()) }}
                                of
                                {{ number_format($documents->total()) }}
                                documents

                            @else

                                No documents in the current view

                            @endif

                        </p>

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
                            class="inline-flex items-center gap-1.5 self-start rounded-lg px-3 py-2 text-xs font-semibold text-slate-500 transition hover:bg-background hover:text-primary sm:self-auto">

                            <svg
                                class="h-4 w-4"
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

            </div>

            {{-- Search and filters --}}
            <form
                method="GET"
                action="{{ route('documents.index') }}"
                class="card p-4">

                @if ($selectedContainer)

                    <input
                        type="hidden"
                        name="container"
                        value="{{ $selectedContainer->id }}">

                @endif


                <div class="flex flex-col gap-3 lg:flex-row lg:items-center">

                    <div class="relative min-w-0 flex-1">

                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">

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
                            placeholder="Search by title, filename, reference, or related record..."
                            class="input pl-10">

                    </div>


                    <div class="flex flex-col gap-2 sm:flex-row">

                        <details class="group relative">

                            <summary
                                class="btn-outline flex cursor-pointer list-none items-center justify-center gap-2">

                                <svg
                                    class="h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 4h18l-7 9v6l-4 2v-8L3 4z" />

                                </svg>

                                Filters

                                @if (
                                    request()->filled('category')
                                    || request()->filled('status')
                                )

                                    <span class="flex h-5 min-w-5 items-center justify-center rounded-full bg-secondary px-1.5 text-[10px] font-bold text-white">

                                        {{
                                            (request()->filled('category') ? 1 : 0)
                                            +
                                            (request()->filled('status') ? 1 : 0)
                                        }}

                                    </span>

                                @endif


                                <svg
                                    class="h-3.5 w-3.5 transition group-open:rotate-180"
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


                            <div class="relative z-30 mt-2 w-full rounded-xl border border-border bg-card p-4 shadow-soft sm:absolute sm:right-0 sm:w-80">

                                <div class="space-y-4">

                                    <div>

                                        <label class="label">
                                            Category
                                        </label>

                                        <select
                                            name="category"
                                            class="input">

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


                                    <div>

                                        <label class="label">
                                            Status
                                        </label>

                                        <select
                                            name="status"
                                            class="input">

                                            <option value="">
                                                All Statuses
                                            </option>

                                            <option
                                                value="active"
                                                @selected(request('status') === 'active')>

                                                Active

                                            </option>

                                            <option
                                                value="needs_review"
                                                @selected(request('status') === 'needs_review')>

                                                Needs Review

                                            </option>

                                            <option
                                                value="archived"
                                                @selected(request('status') === 'archived')>

                                                Archived

                                            </option>

                                            <option
                                                value="superseded"
                                                @selected(request('status') === 'superseded')>

                                                Superseded

                                            </option>

                                        </select>

                                    </div>


                                    <button
                                        type="submit"
                                        class="btn-secondary w-full justify-center">

                                        Apply Filters

                                    </button>

                                </div>

                            </div>

                        </details>


                        <button
                            type="submit"
                            class="btn-primary justify-center">

                            Search

                        </button>

                    </div>

                </div>


                @if (
                    request()->filled('search')
                    || request()->filled('category')
                    || request()->filled('status')
                )

                    <div class="mt-3 flex flex-wrap items-center gap-2 border-t border-border pt-3">

                        <span class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                            Active filters
                        </span>


                        @if (request()->filled('search'))

                            <span class="inline-flex max-w-xs items-center truncate rounded-full bg-primary/5 px-2.5 py-1 text-xs font-medium text-primary">
                                Search: “{{ request('search') }}”
                            </span>

                        @endif


                        @if (request()->filled('category'))

                            <span class="inline-flex items-center rounded-full bg-accent/10 px-2.5 py-1 text-xs font-medium text-primary">
                                {{ str(request('category'))->headline() }}
                            </span>

                        @endif


                        @if (request()->filled('status'))

                            <span class="inline-flex items-center rounded-full bg-secondary/10 px-2.5 py-1 text-xs font-medium text-secondary">
                                {{ str(request('status'))->headline() }}
                            </span>

                        @endif

                    </div>

                @endif

            </form>

            {{-- Upload document modal --}}
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


                    {{-- Modal positioning --}}
                    <div class="relative flex min-h-full items-center justify-center p-3 sm:p-6">

                        <div
                            data-document-upload-panel
                            class="flex max-h-[calc(100vh-1.5rem)] w-full max-w-4xl flex-col overflow-hidden rounded-2xl border border-border bg-card shadow-2xl sm:max-h-[calc(100vh-3rem)]">

                            {{-- Modal header --}}
                            <div class="flex shrink-0 items-start justify-between gap-4 border-b border-border px-5 py-4 sm:px-6">

                                <div class="flex min-w-0 items-start gap-3">

                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary">

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

                                        <h2
                                            id="upload-document-title"
                                            class="font-heading text-lg font-semibold text-primary">

                                            Upload Document

                                        </h2>

                                        <p class="mt-1 text-xs leading-relaxed text-slate-500 sm:text-sm">
                                            Add a manually uploaded record to Document Management.
                                        </p>

                                        @if ($selectedContainer)

                                            <div class="mt-2 inline-flex max-w-full items-center gap-1.5 rounded-full bg-accent/10 px-2.5 py-1 text-xs font-medium text-primary">

                                                <svg
                                                    class="h-3.5 w-3.5 shrink-0"
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


                                {{-- Scrollable form body --}}
                                <div class="min-h-0 flex-1 overflow-y-auto px-5 py-5 sm:px-6">

                                    <div class="space-y-6">

                                        @if ($errors->any() && old('category') !== null)

                                            <div class="rounded-xl border border-error/20 bg-error/5 p-4">

                                                <div class="flex items-start gap-3">

                                                    <div class="mt-0.5 text-error">

                                                        <svg
                                                            class="h-5 w-5"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            viewBox="0 0 24 24">

                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 9v3m0 4h.01M10.3 3.6L2.5 17a2 2 0 001.7 3h15.6a2 2 0 001.7-3L13.7 3.6a2 2 0 00-3.4 0z" />

                                                        </svg>

                                                    </div>

                                                    <div>

                                                        <p class="text-sm font-semibold text-error">
                                                            Please correct the following:
                                                        </p>

                                                        <ul class="mt-2 list-disc space-y-1 pl-5 text-xs text-error">

                                                            @foreach ($errors->all() as $error)

                                                                <li>{{ $error }}</li>

                                                            @endforeach

                                                        </ul>

                                                    </div>

                                                </div>

                                            </div>

                                        @endif


                                        {{-- Basic document information --}}
                                        <section>

                                            <div class="mb-4">

                                                <h3 class="font-heading text-sm font-semibold text-primary">
                                                    Document Information
                                                </h3>

                                                <p class="mt-1 text-xs text-slate-500">
                                                    Enter the basic filing information for this document.
                                                </p>

                                            </div>


                                            <div class="grid gap-4 md:grid-cols-2">

                                                <div class="md:col-span-2">

                                                    <label class="label">
                                                        Document Title
                                                        <span class="text-error">*</span>
                                                    </label>

                                                    <input
                                                        name="title"
                                                        value="{{ old('title') }}"
                                                        placeholder="Enter a descriptive document title"
                                                        required
                                                        autofocus
                                                        class="input">

                                                </div>


                                                <div>

                                                    <label class="label">
                                                        Container
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
                                                                    ) === (string) $container->id
                                                                )>

                                                                {{ str_replace('/', ' / ', $container->path) }}

                                                            </option>

                                                        @endforeach

                                                    </select>

                                                </div>


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


                                                <div>

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

                                        </section>


                                        <div class="border-t border-border"></div>


                                        {{-- Related record --}}
                                        <section>

                                            <div class="mb-4">

                                                <h3 class="font-heading text-sm font-semibold text-primary">
                                                    Related System Record
                                                </h3>

                                                <p class="mt-1 text-xs text-slate-500">
                                                    Optional. Connect this file to an existing contract, legal record, reservation, or visitor.
                                                </p>

                                            </div>


                                            <div class="rounded-xl border border-border bg-background/50 p-4">

                                                <div class="grid gap-4 md:grid-cols-2">

                                                    <div>

                                                        <label class="label">
                                                            Related Module
                                                        </label>

                                                        <select
                                                            id="related_type"
                                                            name="related_type"
                                                            class="input">

                                                            <option value="">
                                                                None
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


                                                    <div>

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
                                                                        && (string) old('related_id') === (string) $contract->id
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
                                                                        && (string) old('related_id') === (string) $legalRecord->id
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
                                                                        && (string) old('related_id') === (string) $reservation->id
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
                                                                        && (string) old('related_id') === (string) $visitor->id
                                                                    )>

                                                                    #{{ $visitor->id }}
                                                                    - {{ $visitor->full_name }}

                                                                </option>

                                                            @endforeach

                                                        </select>

                                                    </div>

                                                </div>

                                            </div>

                                        </section>


                                        <div class="border-t border-border"></div>


                                        {{-- File and description --}}
                                        <section>

                                            <div class="mb-4">

                                                <h3 class="font-heading text-sm font-semibold text-primary">
                                                    File & Description
                                                </h3>

                                                <p class="mt-1 text-xs text-slate-500">
                                                    Attach the document file and add any useful filing notes.
                                                </p>

                                            </div>


                                            <div class="space-y-4">

                                                <div>

                                                    <label class="label">
                                                        File
                                                    </label>

                                                    <div class="rounded-xl border border-dashed border-border bg-background/40 p-4">

                                                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">

                                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">

                                                                <svg
                                                                    class="h-5 w-5"
                                                                    fill="none"
                                                                    stroke="currentColor"
                                                                    viewBox="0 0 24 24">

                                                                    <path
                                                                        stroke-linecap="round"
                                                                        stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M12 4v12m0-12L7 9m5-5l5 5M5 20h14" />

                                                                </svg>

                                                            </div>

                                                            <div class="min-w-0 flex-1">

                                                                <input
                                                                    type="file"
                                                                    name="file"
                                                                    class="block w-full text-sm text-slate-500 file:mr-4 file:rounded-lg file:border-0 file:bg-primary/10 file:px-4 file:py-2 file:text-xs file:font-semibold file:text-primary hover:file:bg-primary/15">

                                                                <p class="mt-2 text-xs text-slate-400">
                                                                    Choose the document file you want to store.
                                                                </p>

                                                            </div>

                                                        </div>

                                                    </div>

                                                </div>


                                                <div>

                                                    <label class="label">
                                                        Description
                                                    </label>

                                                    <textarea
                                                        name="description"
                                                        rows="4"
                                                        placeholder="Add notes or a short description of this document..."
                                                        class="input resize-y">{{ old('description') }}</textarea>

                                                </div>

                                            </div>

                                        </section>

                                    </div>

                                </div>


                                {{-- Modal footer --}}
                                <div class="flex shrink-0 flex-col-reverse gap-2 border-t border-border bg-background/40 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">

                                    <p class="text-xs text-slate-400">
                                        Fields marked with
                                        <span class="text-error">*</span>
                                        are required.
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

            @endcan

            @can('manageDocuments')

                <form
                    id="document-bulk-form"
                    method="POST"
                    action="{{ route('documents.bulk-action') }}"
                    data-document-bulk-toolbar
                    class="hidden overflow-hidden rounded-xl border border-accent/25 bg-accent/5 shadow-sm">

                    @csrf

                    <input
                        type="hidden"
                        name="action"
                        data-document-bulk-action>


                    <div class="flex flex-col gap-3 px-4 py-3 lg:flex-row lg:items-center lg:justify-between">

                        <div class="flex min-w-0 items-center gap-3">

                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary text-white">

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


                            <div>

                                <p
                                    data-document-bulk-count
                                    class="font-button text-sm font-semibold text-primary">

                                    0 documents selected

                                </p>

                                <p
                                    data-document-bulk-summary
                                    class="mt-0.5 text-xs text-slate-500">

                                    Select documents to manage their archive status.

                                </p>

                            </div>

                        </div>


                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">

                            <button
                                type="button"
                                data-document-bulk-archive
                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-error/20 bg-error/5 px-4 py-2.5 text-sm font-semibold text-error transition hover:bg-error/10 disabled:cursor-not-allowed disabled:opacity-40">

                                <svg
                                    class="h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 8h14M9 12h6m-8 8h10a2 2 0 002-2V8H5v10a2 2 0 002 2zM8 4h8l1 4H7l1-4z" />

                                </svg>

                                Archive Selected

                            </button>


                            <button
                                type="button"
                                data-document-bulk-restore
                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-success/20 bg-success/5 px-4 py-2.5 text-sm font-semibold text-success transition hover:bg-success/10 disabled:cursor-not-allowed disabled:opacity-40">

                                <svg
                                    class="h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 4v6h6M20 20v-6h-6M5.1 15a8 8 0 0013.8 1M18.9 9A8 8 0 005.1 8" />

                                </svg>

                                Restore Selected

                            </button>


                            <button
                                type="button"
                                data-document-bulk-clear
                                class="inline-flex items-center justify-center rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-500 transition hover:bg-card hover:text-primary">

                                Clear

                            </button>

                        </div>

                    </div>

                </form>

            @endcan

            {{-- Table --}}
            <div class="table-shell">

                <div class="overflow-x-auto">

                    <table class="min-w-full text-left text-sm">

                        <thead class="table-header sticky top-0 z-10">

                            <tr>

                                @can('manageDocuments')

                                    <th class="w-12 px-5 py-4">

                                        <input
                                            type="checkbox"
                                            data-document-select-all
                                            aria-label="Select all documents on this page"
                                            class="h-4 w-4 rounded border-border text-primary focus:ring-primary">

                                    </th>

                                @endcan

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

                                <tr data-document-row class="transition hover:bg-background/70">

                                    @can('manageDocuments')

                                        <td class="w-12 px-5 py-4 align-top">

                                            <input
                                                type="checkbox"
                                                name="document_ids[]"
                                                value="{{ $document->id }}"
                                                form="document-bulk-form"
                                                data-document-select
                                                data-document-status="{{ $document->status }}"
                                                aria-label="Select {{ $document->title }}"
                                                class="mt-1 h-4 w-4 rounded border-border text-primary focus:ring-primary">

                                        </td>

                                    @endcan

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
                                    <td
                                        colspan="{{ auth()->user()->can('manageDocuments') ? 7 : 6 }}"
                                        class="px-6 py-16">

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
        }

        if (restoreButton) {
            restoreButton.disabled =
                restorableCount === 0;
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