@extends('layouts.app')
@section('title', 'Records Archive')
@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        @can('manageDocuments')
        <div class="card p-5 lg:col-span-1">
            <h2 class="mb-3 text-sm font-semibold text-slate-700">Archive a Document</h2>
            <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <div>
                    <label class="label">Title</label>
                    <input type="text" name="title" required class="input">
                </div>
                <div>
                    <label class="label">Category</label>
                    <select name="category" class="input">
                        @foreach (['administrative','contract','legal','permit','license','compliance','partnership','financial','operational','other'] as $c)
                            <option value="{{ $c }}">{{ str($c)->headline() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Confidentiality</label>
                    <select name="confidentiality" class="input">
                        <option value="general">General</option>
                        <option value="restricted">Restricted</option>
                        <option value="confidential">Confidential</option>
                    </select>
                </div>
                <div>
                    <label class="label">Owner email</label>
                    <input type="email" name="owner_email" class="input">
                </div>
                <div>
                    <label class="label">File</label>
                    <input type="file" name="file" class="input">
                </div>
                <input type="hidden" name="status" value="active">
                <button type="submit" class="btn-primary w-full justify-center">Upload</button>
            </form>
        </div>
        @endcan

        <div class="{{ auth()->user()->can('manageDocuments') ? 'lg:col-span-2' : 'lg:col-span-3' }}">
            <div class="card overflow-hidden">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Title</th>
                            <th class="px-4 py-3">Category</th>
                            <th class="px-4 py-3">Confidentiality</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($documents as $document)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $document->title }} <span class="text-xs text-slate-400">v{{ $document->version }}</span></td>
                                <td class="px-4 py-3 text-slate-600">{{ str($document->category)->headline() }}</td>
                                <td class="px-4 py-3">
                                    <span @class([
                                        'badge',
                                        'bg-slate-100 text-slate-600' => $document->confidentiality === 'general',
                                        'bg-amber-50 text-amber-700' => $document->confidentiality === 'restricted',
                                        'bg-red-50 text-red-700' => $document->confidentiality === 'confidential',
                                    ])>{{ $document->confidentiality }}</span>
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $document->status }}</td>
                                <td class="px-4 py-3 text-right">
                                    @if ($document->file_uri)
                                        <button type="button"
                                            onclick="fetch('{{ route('documents.request-link', $document) }}', {method:'POST', headers:{'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content}}).then(r=>r.json()).then(d=>{ if(d.signed_url) window.location = d.signed_url; })"
                                            class="text-slate-600 hover:underline">Open</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $documents->links() }}</div>
        </div>
    </div>
@endsection
