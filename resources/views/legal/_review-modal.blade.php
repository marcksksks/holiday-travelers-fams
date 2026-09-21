<div
    id="legal-review-{{ $record->id }}"
    data-legal-modal
    class="fixed inset-0 z-[140] hidden"
    role="dialog"
    aria-modal="true"
    aria-labelledby="legal-review-title-{{ $record->id }}">

    <button
        type="button"
        data-legal-modal-backdrop
        class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm"
        aria-label="Close legal review dialog">
    </button>


    <div class="relative flex min-h-full items-center justify-center p-4">

        <div class="w-full max-w-xl overflow-hidden rounded-2xl border border-border bg-card shadow-2xl">

            <div class="flex items-start justify-between gap-4 border-b border-border bg-background/50 px-5 py-4">

                <div>

                    <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-secondary">
                        Legal Review
                    </p>

                    <h3
                        id="legal-review-title-{{ $record->id }}"
                        class="mt-1 font-heading text-lg font-semibold text-primary">
                        {{ $record->title }}
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Record the current legal assessment and required follow-up.
                    </p>

                </div>


                <button
                    type="button"
                    data-legal-modal-close
                    class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-background hover:text-primary"
                    aria-label="Close">

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>

                </button>

            </div>


            <form
                method="POST"
                action="{{ route('legal.review', $record) }}">

                @csrf

                <input
                    type="hidden"
                    name="form_context"
                    value="review-{{ $record->id }}">

                <input
                    type="hidden"
                    name="review_record_id"
                    value="{{ $record->id }}">


                <div class="space-y-5 p-5">

                    <div>

                        <label
                            for="legal_review_status_{{ $record->id }}"
                            class="label">

                            Review Status
                            <span class="text-error">*</span>

                        </label>

                        <select
                            id="legal_review_status_{{ $record->id }}"
                            name="review_status"
                            required
                            class="input">

                            @foreach ([
                                'not_reviewed',
                                'in_review',
                                'reviewed',
                                'action_required'
                            ] as $review)

                                <option
                                    value="{{ $review }}"
                                    @selected(
                                        old('review_record_id') == $record->id
                                            ? old('review_status', $record->review_status) === $review
                                            : $record->review_status === $review
                                    )>

                                    {{ str($review)->headline() }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div>

                        <label
                            for="legal_review_notes_{{ $record->id }}"
                            class="label">
                            Legal Assessment / Notes
                        </label>

                        <textarea
                            id="legal_review_notes_{{ $record->id }}"
                            name="legal_notes"
                            rows="6"
                            placeholder="Record legal findings, recommendations, obligations, or required actions..."
                            class="input min-h-[150px]">{{ old('review_record_id') == $record->id ? old('legal_notes', $record->legal_notes) : $record->legal_notes }}</textarea>

                    </div>


                    <div class="rounded-xl border border-warning/20 bg-warning/5 p-3">

                        <p class="text-xs leading-5 text-slate-600">
                            Phase 1 keeps the latest legal assessment on the record while the audit trail preserves the review action. Dedicated review history will be added in the next workflow phase.
                        </p>

                    </div>

                </div>


                <div class="flex justify-end gap-2 border-t border-border bg-background/40 px-5 py-4">

                    <button
                        type="button"
                        data-legal-modal-close
                        class="btn-outline">
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="btn-primary">
                        Save Review
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>