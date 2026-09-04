@extends('layouts.app')

@section('title', 'Disposal Approval')

@section('content')

<div class="mx-auto max-w-4xl space-y-6">

    <div>

        <a
            href="{{ route('retention.index') }}"
            class="mb-3 inline-flex text-sm font-medium text-slate-500 hover:text-primary">

            &larr; Back to Records Retention

        </a>

        <div class="mb-2 h-1 w-12 rounded-full bg-secondary"></div>

        <h1 class="font-heading text-2xl font-bold text-primary">
            Disposal Approval
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Review a pending record disposition request.
        </p>

    </div>


    @if ($errors->any())

        <div class="rounded-xl border border-error/20 bg-error/5 p-4">

            @foreach ($errors->all() as $error)
                <p class="text-sm text-error">
                    {{ $error }}
                </p>
            @endforeach

        </div>

    @endif


    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">

        <section class="card overflow-hidden">

            <div class="border-b border-border px-6 py-5">

                <div class="flex flex-wrap items-center justify-between gap-3">

                    <div>
                        <h2 class="font-heading text-lg font-semibold text-primary">
                            {{ $retention->record_title }}
                        </h2>

                        <p class="mt-1 text-xs text-slate-500">
                            {{ str($retention->record_type)->headline() }}
                        </p>
                    </div>

                    <span class="badge badge-warning">
                        Pending Disposal Approval
                    </span>

                </div>

            </div>


            <div class="space-y-5 p-6">

                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-400">
                        Requested By
                    </p>

                    <p class="mt-1 text-sm font-medium text-primary">
                        {{ $retention->disposition_requested_by }}
                    </p>
                </div>


                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-400">
                        Requested At
                    </p>

                    <p class="mt-1 text-sm font-medium text-primary">
                        {{ $retention->disposition_requested_at?->format('M d, Y h:i A') }}
                    </p>
                </div>


                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-400">
                        Reason
                    </p>

                    <p class="mt-1 text-sm leading-6 text-slate-600">
                        {{ $retention->disposition_reason ?: 'No reason provided.' }}
                    </p>
                </div>


                <div class="rounded-xl border border-warning/30 bg-warning/5 p-4">

                    <p class="font-semibold text-amber-700">
                        Approval does not delete the file
                    </p>

                    <p class="mt-1 text-xs leading-5 text-slate-500">
                        Approval only authorizes this record for a future controlled disposition process.
                    </p>

                </div>


                @if ($retention->disposition_requested_by !== auth()->user()->email)
                <form
                    method="POST"
                    action="{{ route('retention.disposition.approve', $retention) }}"
                    class="space-y-4 rounded-xl border border-border p-4">

                    @csrf

                    <label class="label">
                        Approval Notes
                    </label>

                    <textarea
                        name="decision_notes"
                        rows="3"
                        class="input"
                        placeholder="Optional approval notes..."></textarea>

                    <button
                        type="submit"
                        class="btn-primary w-full justify-center"
                        onclick="return confirm('Approve this disposal request? The file will NOT be deleted.')">

                        Approve Disposal Request

                    </button>

                </form>


                <form
                    method="POST"
                    action="{{ route('retention.disposition.reject', $retention) }}"
                    class="space-y-4 rounded-xl border border-error/20 bg-error/5 p-4">

                    @csrf

                    <label class="label">
                        Rejection Reason *
                    </label>

                    <textarea
                        name="decision_notes"
                        rows="3"
                        required
                        class="input"
                        placeholder="Explain why this disposal request is rejected..."></textarea>

                    <button
                        type="submit"
                        class="btn-outline w-full justify-center">

                        Reject Disposal Request

                    </button>

                </form>

                @else

                    <div class="rounded-xl border border-warning/30 bg-warning/5 p-5">

                        <p class="font-button text-sm font-semibold text-amber-700">
                            Separation of Duties
                        </p>

                        <p class="mt-1 text-xs leading-5 text-slate-500">
                            You submitted this disposal request, so another authorized approver must approve or reject it.
                        </p>

                    </div>

                @endif

            </div>

        </section>


        <aside class="space-y-5">

            <section class="card p-5">

                <h2 class="font-heading text-sm font-semibold text-primary">
                    Retention Information
                </h2>

                <div class="mt-4 space-y-4">

                    <div>
                        <p class="text-xs uppercase text-slate-400">
                            Policy
                        </p>

                        <p class="mt-1 text-sm font-medium text-primary">
                            {{ $retention->policy_name ?: 'No policy' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs uppercase text-slate-400">
                            Review Date
                        </p>

                        <p class="mt-1 text-sm font-medium text-primary">
                            {{ $retention->review_date?->format('M d, Y') ?: 'None' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs uppercase text-slate-400">
                            Compliance
                        </p>

                        <p class="mt-1 text-sm font-medium text-primary">
                            {{ str($retention->compliance_status)->headline() }}
                        </p>
                    </div>

                </div>

            </section>


            @if ($document)

                <section class="card p-5">

                    <h2 class="font-heading text-sm font-semibold text-primary">
                        Linked Document
                    </h2>

                    <p class="mt-3 text-sm font-semibold text-primary">
                        {{ $document->title }}
                    </p>

                    <p class="mt-1 text-xs text-slate-500">
                        {{ str($document->status)->headline() }}
                    </p>

                    <a
                        href="{{ route('documents.show', $document) }}"
                        class="btn-outline mt-4 flex w-full justify-center">

                        View Document

                    </a>

                </section>

            @endif

        </aside>

    </div>

</div>

@endsection