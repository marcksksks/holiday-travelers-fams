@extends('layouts.app')

@section('title', 'Records Archive')

@section('content')

<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

        <div>
            <div class="mb-2 h-1 w-12 rounded-full bg-secondary"></div>

            <h2 class="font-heading text-2xl font-bold text-primary">
                Records Archive
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Securely archive, organize, and access administrative documents and organizational records.
            </p>
        </div>

        <div class="rounded-xl border border-border bg-card px-4 py-2.5 shadow-card">
            <p class="text-xs text-slate-500">
                Total Documents
            </p>

            <p class="mt-0.5 font-heading text-lg font-bold text-primary">
                {{ $documents->total() }}
            </p>
        </div>

    </div>


    <div class="grid gap-6 xl:grid-cols-[410px_minmax(0,1fr)]">

        {{-- Upload Document --}}
        @can('manageDocuments')

            <div>

                <div class="card overflow-hidden xl:sticky xl:top-6">

                    {{-- Header --}}
                    <div class="border-b border-border bg-background/60 px-5 py-5">

                        <div class="flex items-center gap-3">

                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-secondary/10 text-secondary">

                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 16V4m0 0L8 8m4-4l4 4M5 20h14a2 2 0 002-2v-3M3 15v3a2 2 0 002 2" />
                                </svg>

                            </div>

                            <div>
                                <h3 class="font-heading text-base font-semibold text-primary">
                                    Archive a Document
                                </h3>

                                <p class="mt-0.5 text-xs text-slate-500">
                                    Add a new file to the secure records repository.
                                </p>
                            </div>

                        </div>

                    </div>


                    <form
                        method="POST"
                        action="{{ route('documents.store') }}"
                        enctype="multipart/form-data"
                        class="space-y-5 p-5">

                        @csrf


                        {{-- Title --}}
                        <div>

                            <label for="title" class="label">
                                Document Title
                                <span class="text-error">*</span>
                            </label>

                            <input
                                id="title"
                                type="text"
                                name="title"
                                value="{{ old('title') }}"
                                required
                                placeholder="e.g. Business Partnership Agreement"
                                class="input @error('title') border-error @enderror">

                            @error('title')
                                <p class="mt-1.5 text-xs font-medium text-error">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        {{-- Category + Confidentiality --}}
                        <div class="grid gap-4 sm:grid-cols-2">

                            <div>

                                <label for="category" class="label">
                                    Category
                                </label>

                                <select
                                    id="category"
                                    name="category"
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

                                <label for="confidentiality" class="label">
                                    Confidentiality
                                </label>

                                <select
                                    id="confidentiality"
                                    name="confidentiality"
                                    class="input">

                                    @foreach ([
                                        'general',
                                        'restricted',
                                        'confidential'
                                    ] as $level)

                                        <option
                                            value="{{ $level }}"
                                            @selected(old('confidentiality', 'general') === $level)>

                                            {{ str($level)->headline() }}

                                        </option>

                                    @endforeach

                                </select>

                            </div>

                        </div>


                        {{-- Department --}}
                        <div>

                            <label for="department" class="label">
                                Department
                            </label>

                            <input
                                id="department"
                                type="text"
                                name="department"
                                value="{{ old('department') }}"
                                placeholder="e.g. Administration"
                                class="input">

                        </div>


                        {{-- Owner --}}
                        <div>

                            <label for="owner_email" class="label">
                                Owner Email
                            </label>

                            <div class="relative">

                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">

                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M3 8l9 6 9-6M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>

                                </div>

                                <input
                                    id="owner_email"
                                    type="email"
                                    name="owner_email"
                                    value="{{ old('owner_email') }}"
                                    placeholder="owner@example.com"
                                    class="input pl-10">

                            </div>

                        </div>


                        {{-- Dates --}}
                        <div class="grid gap-4 sm:grid-cols-2">

                            <div>

                                <label for="document_date" class="label">
                                    Document Date
                                </label>

                                <input
                                    id="document_date"
                                    type="date"
                                    name="document_date"
                                    value="{{ old('document_date') }}"
                                    class="input">

                            </div>


                            <div>

                                <label for="expiration_date" class="label">
                                    Expiration Date
                                </label>

                                <input
                                    id="expiration_date"
                                    type="date"
                                    name="expiration_date"
                                    value="{{ old('expiration_date') }}"
                                    class="input">

                            </div>

                        </div>


                        {{-- Description --}}
                        <div>

                            <label for="description" class="label">
                                Description
                            </label>

                            <textarea
                                id="description"
                                name="description"
                                rows="3"
                                placeholder="Add a short description of this record..."
                                class="input">{{ old('description') }}</textarea>

                        </div>


                        {{-- File --}}
                        <div>

                            <label for="file" class="label">
                                Document File
                            </label>

                            <label
                                for="file"
                                class="flex cursor-pointer items-center gap-3 rounded-xl border border-dashed border-accent/50 bg-accent/5 px-4 py-4 transition hover:border-secondary hover:bg-secondary/5">

                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-accent shadow-sm">

                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 16V4m0 0L8 8m4-4l4 4M5 20h14a2 2 0 002-2v-3M3 15v3a2 2 0 002 2" />
                                    </svg>

                                </div>

                                <div>
                                    <p class="font-button text-sm font-medium text-primary">
                                        Choose a document
                                    </p>

                                    <p class="mt-0.5 text-xs text-slate-400">
                                        Maximum file size: 20 MB
                                    </p>
                                </div>

                            </label>

                            <input
                                id="file"
                                type="file"
                                name="file"
                                class="mt-2 block w-full text-xs text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-primary/10 file:px-3 file:py-2 file:font-medium file:text-primary hover:file:bg-primary/15">

                            @error('file')
                                <p class="mt-1.5 text-xs font-medium text-error">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        <input
                            type="hidden"
                            name="status"
                            value="active">


                        <button
                            type="submit"
                            class="btn-secondary w-full">

                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>

                            Archive Document

                        </button>

                    </form>

                </div>

            </div>

        @endcan


        {{-- Repository --}}
        <div class="min-w-0 space-y-4">

            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

                <div>
                    <h3 class="font-heading text-lg font-semibold text-primary">
                        Document Repository
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Browse archived documents and controlled records.
                    </p>
                </div>


                {{-- Category Filter --}}
                <form
                    method="GET"
                    action="{{ route('documents.index') }}"
                    class="flex gap-2">

                    <select
                        name="category"
                        class="input min-w-[190px]"
                        onchange="this.form.submit()">

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
                            'other'
                        ] as $category)

                            <option
                                value="{{ $category }}"
                                @selected(request('category') === $category)>

                                {{ str($category)->headline() }}

                            </option>

                        @endforeach

                    </select>

                </form>

            </div>


            <div class="table-shell">

                <div class="overflow-x-auto">

                    <table class="min-w-full text-left text-sm">

                        <thead class="table-header">

                            <tr>
                                <th class="px-5 py-4 font-medium">
                                    Document
                                </th>

                                <th class="px-5 py-4 font-medium">
                                    Category
                                </th>

                                <th class="px-5 py-4 font-medium">
                                    Confidentiality
                                </th>

                                <th class="px-5 py-4 font-medium">
                                    Status
                                </th>

                                <th class="px-5 py-4 font-medium">
                                    Updated
                                </th>

                                <th class="px-5 py-4 text-right font-medium">
                                    Actions
                                </th>
                            </tr>

                        </thead>


                        <tbody class="divide-y divide-border bg-card">

                            @forelse ($documents as $document)

                                <tr class="transition hover:bg-sky-50/40">

                                    {{-- Document --}}
                                    <td class="px-5 py-4">

                                        <div class="flex items-center gap-3">

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

                                                <div class="flex items-center gap-2">

                                                    <p class="max-w-[220px] truncate font-button text-sm font-semibold text-primary">
                                                        {{ $document->title }}
                                                    </p>

                                                    <span class="rounded-full bg-accent/10 px-2 py-0.5 text-[10px] font-semibold text-primary">
                                                        v{{ $document->version ?? 1 }}
                                                    </span>

                                                </div>


                                                @if ($document->file_name)

                                                    <p class="mt-0.5 max-w-[240px] truncate text-xs text-slate-400">
                                                        {{ $document->file_name }}
                                                    </p>

                                                @elseif ($document->department)

                                                    <p class="mt-0.5 text-xs text-slate-400">
                                                        {{ $document->department }}
                                                    </p>

                                                @endif

                                            </div>

                                        </div>

                                    </td>


                                    {{-- Category --}}
                                    <td class="px-5 py-4">

                                        <span class="inline-flex rounded-lg bg-primary/5 px-2.5 py-1 text-xs font-medium text-primary">
                                            {{ str($document->category)->headline() }}
                                        </span>

                                    </td>


                                    {{-- Confidentiality --}}
                                    <td class="px-5 py-4">

                                        @switch($document->confidentiality)

                                            @case('general')

                                                <span class="badge badge-info">
                                                    General
                                                </span>

                                                @break


                                            @case('restricted')

                                                <span class="badge badge-warning">
                                                    Restricted
                                                </span>

                                                @break


                                            @case('confidential')

                                                <span class="badge badge-error">
                                                    Confidential
                                                </span>

                                                @break

                                        @endswitch

                                    </td>


                                    {{-- Status --}}
                                    <td class="px-5 py-4">

                                        @switch($document->status)

                                            @case('active')

                                                <span class="badge badge-success">
                                                    <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-success"></span>
                                                    Active
                                                </span>

                                                @break


                                            @case('needs_review')

                                                <span class="badge badge-warning">
                                                    Needs Review
                                                </span>

                                                @break


                                            @case('archived')

                                                <span class="badge bg-slate-100 text-slate-600 ring-1 ring-inset ring-slate-200">
                                                    Archived
                                                </span>

                                                @break


                                            @case('superseded')

                                                <span class="badge bg-slate-100 text-slate-500 ring-1 ring-inset ring-slate-200">
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
                                    <td class="px-5 py-4">

                                        <p class="text-sm text-slate-600">
                                            {{ $document->updated_at->format('M d, Y') }}
                                        </p>

                                        <p class="mt-0.5 text-[10px] text-slate-400">
                                            {{ $document->updated_at->diffForHumans() }}
                                        </p>

                                    </td>


                                    {{-- Actions --}}
                                    <td class="px-5 py-4 text-right">

                                        @if ($document->file_uri)

                                            <button
                                                type="button"
                                                onclick="fetch('{{ route('documents.request-link', $document) }}', {
                                                    method: 'POST',
                                                    headers: {
                                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                                        'Accept': 'application/json'
                                                    }
                                                })
                                                .then(response => response.json())
                                                .then(data => {
                                                    if (data.signed_url) {
                                                        window.location.href = data.signed_url;
                                                    }
                                                })"
                                                class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-white px-3 py-2 font-button text-xs font-medium text-primary transition hover:border-accent hover:bg-accent/5">

                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 5v10m0 0l-4-4m4 4l4-4M5 19h14" />
                                                </svg>

                                                Open

                                            </button>

                                        @else

                                            <span class="text-xs text-slate-400">
                                                No file
                                            </span>

                                        @endif

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="6" class="px-6 py-16">

                                        <div class="mx-auto flex max-w-sm flex-col items-center text-center">

                                            <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-accent/10 text-accent">

                                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M7 3h7l5 5v13H7V3zm7 0v5h5" />
                                                </svg>

                                            </div>

                                            <h3 class="font-heading text-base font-semibold text-primary">
                                                No archived documents
                                            </h3>

                                            <p class="mt-1 text-sm text-slate-500">
                                                Documents added to the archive will appear here.
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

                <div class="rounded-2xl border border-border bg-card px-5 py-4 shadow-card">
                    {{ $documents->withQueryString()->links() }}
                </div>

            @endif

        </div>

    </div>

</div>

@endsection