@can('manageAppointments')

<div
    data-appointment-edit-modal
    class="fixed inset-0 z-[90] hidden opacity-0 transition-opacity duration-200"
    role="dialog"
    aria-modal="true"
    aria-labelledby="appointment-edit-title">

    <div
        data-appointment-edit-backdrop
        class="absolute inset-0 bg-slate-950/50 backdrop-blur-[2px]">
    </div>


    <div class="relative flex min-h-full items-center justify-center p-4 sm:p-6">

        <div
            data-appointment-edit-panel
            class="max-h-[calc(100vh-2rem)] w-full max-w-2xl translate-y-2 scale-[0.98] overflow-y-auto rounded-2xl border border-border bg-card shadow-2xl transition duration-200">


            {{-- Header --}}
            <div class="sticky top-0 z-10 flex items-start justify-between border-b border-border bg-card px-6 py-5">

                <div>

                    <p class="text-xs font-semibold uppercase tracking-wider text-secondary">
                        Appointment Management
                    </p>

                    <h3
                        id="appointment-edit-title"
                        class="mt-1 font-heading text-xl font-bold text-primary">

                        Edit Appointment

                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Update visitor and scheduling information.
                    </p>

                </div>


                <button
                    type="button"
                    data-appointment-edit-close
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-slate-400 transition hover:bg-background hover:text-primary"
                    aria-label="Close edit appointment">

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

                </button>

            </div>


            <form
                data-appointment-edit-form
                method="POST"
                action=""
                class="space-y-6 p-6">

                @csrf
                @method('PUT')

                <input
                    type="hidden"
                    name="_appointment_edit_context"
                    value="edit">

                <input
                    type="hidden"
                    name="_appointment_edit_id"
                    data-edit-id
                    value="">


                {{-- Visitor --}}
                <div>

                    <div class="mb-4">
                        <h4 class="font-heading text-sm font-semibold text-primary">
                            Visitor Information
                        </h4>

                        <p class="mt-1 text-xs text-slate-500">
                            Basic information about the expected visitor.
                        </p>
                    </div>


                    <div class="grid gap-4 sm:grid-cols-2">

                        <div class="sm:col-span-2">

                            <label class="label">
                                Visitor Name
                                <span class="text-error">*</span>
                            </label>

                            <input
                                data-edit-name
                                type="text"
                                name="visitor_name"
                                required
                                maxlength="255"
                                class="input"
                                placeholder="Full name">

                        </div>


                        <div>

                            <label class="label">
                                Organization
                            </label>

                            <input
                                data-edit-organization
                                type="text"
                                name="visitor_organization"
                                maxlength="255"
                                class="input"
                                placeholder="Company or organization">

                        </div>


                        <div>

                            <label class="label">
                                Visitor Type
                                <span class="text-error">*</span>
                            </label>

                            <select
                                data-edit-type
                                name="visitor_type"
                                required
                                class="input">

                                <option value="customer">Customer</option>
                                <option value="business_partner">Business Partner</option>
                                <option value="supplier">Supplier</option>
                                <option value="government">Government</option>
                                <option value="applicant">Applicant</option>
                                <option value="guest">Guest</option>
                                <option value="other">Other</option>

                            </select>

                        </div>


                        <div>

                            <label class="label">
                                Email
                            </label>

                            <input
                                data-edit-email
                                type="email"
                                name="visitor_email"
                                class="input"
                                placeholder="visitor@example.com">

                        </div>


                        <div>

                            <label class="label">
                                Contact Number
                            </label>

                            <input
                                data-edit-contact
                                type="text"
                                name="visitor_contact"
                                maxlength="50"
                                class="input"
                                placeholder="Contact number">

                        </div>

                    </div>

                </div>


                <div class="border-t border-border"></div>


                {{-- Host --}}
                <div>

                    <h4 class="mb-4 font-heading text-sm font-semibold text-primary">
                        Host Information
                    </h4>

                    <div class="grid gap-4 sm:grid-cols-2">

                        <div>

                            <label class="label">
                                Host Name
                            </label>

                            <input
                                data-edit-host-name
                                type="text"
                                name="host_name"
                                maxlength="255"
                                class="input"
                                placeholder="Employee or host name">

                        </div>


                        <div>

                            <label class="label">
                                Host Email
                            </label>

                            <input
                                data-edit-host-email
                                type="email"
                                name="host_email"
                                class="input"
                                placeholder="host@example.com">

                        </div>

                    </div>

                </div>


                <div class="border-t border-border"></div>


                {{-- Schedule --}}
                <div>

                    <h4 class="mb-4 font-heading text-sm font-semibold text-primary">
                        Schedule
                    </h4>


                    <div class="grid gap-4 sm:grid-cols-2">

                        <div>

                            <label class="label">
                                Date
                                <span class="text-error">*</span>
                            </label>

                            <input
                                data-edit-date
                                type="date"
                                name="date"
                                min="{{ today()->format('Y-m-d') }}"
                                required
                                class="input">

                        </div>


                        <div>

                            <label class="label">
                                Facility
                            </label>

                            <select
                                data-edit-facility
                                name="facility_id"
                                class="input">

                                <option value="">
                                    No facility assigned
                                </option>

                                @foreach ($facilities as $facility)

                                    <option value="{{ $facility->id }}">
                                        {{ $facility->name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>


                        <div>

                            <label class="label">
                                Start Time
                                <span class="text-error">*</span>
                            </label>

                            <input
                                data-edit-start
                                type="time"
                                name="start_time"
                                required
                                class="input">

                        </div>


                        <div>

                            <label class="label">
                                End Time
                            </label>

                            <input
                                data-edit-end
                                type="time"
                                name="end_time"
                                class="input">

                        </div>

                    </div>

                </div>


                <div class="border-t border-border"></div>


                {{-- Purpose --}}
                <div>

                    <label class="label">
                        Purpose
                    </label>

                    <textarea
                        data-edit-purpose
                        name="purpose"
                        rows="3"
                        class="input resize-y"
                        placeholder="Purpose of the appointment"></textarea>

                </div>


                {{-- Notes --}}
                <div>

                    <label class="label">
                        Notes
                    </label>

                    <textarea
                        data-edit-notes
                        name="notes"
                        rows="3"
                        class="input resize-y"
                        placeholder="Additional instructions or notes"></textarea>

                </div>


                <div class="sticky bottom-0 -mx-6 -mb-6 flex justify-end gap-3 border-t border-border bg-card px-6 py-4">

                    <button
                        type="button"
                        data-appointment-edit-close
                        class="btn-outline">

                        Cancel

                    </button>


                    <button
                        type="submit"
                        class="btn-secondary">

                        Save Changes

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<script>
document.addEventListener('DOMContentLoaded', () => {

    const modal =
        document.querySelector(
            '[data-appointment-edit-modal]'
        );

    const panel =
        document.querySelector(
            '[data-appointment-edit-panel]'
        );

    const form =
        document.querySelector(
            '[data-appointment-edit-form]'
        );

    const backdrop =
        document.querySelector(
            '[data-appointment-edit-backdrop]'
        );

    const buttons =
        document.querySelectorAll(
            '[data-appointment-edit]'
        );


    if (!modal || !panel || !form) {
        return;
    }


    let trigger = null;
    let previousOverflow = '';


    const field = selector =>
        modal.querySelector(selector);


    const fillFromButton = button => {

        form.action =
            button.dataset.updateUrl || '';

        field('[data-edit-id]').value =
            button.dataset.appointmentId || '';

        field('[data-edit-name]').value =
            button.dataset.name || '';

        field('[data-edit-organization]').value =
            button.dataset.organization || '';

        field('[data-edit-email]').value =
            button.dataset.email || '';

        field('[data-edit-contact]').value =
            button.dataset.contact || '';

        field('[data-edit-type]').value =
            button.dataset.type || 'guest';

        field('[data-edit-host-name]').value =
            button.dataset.hostName || '';

        field('[data-edit-host-email]').value =
            button.dataset.hostEmail || '';

        field('[data-edit-date]').value =
            button.dataset.date || '';

        field('[data-edit-start]').value =
            button.dataset.start || '';

        field('[data-edit-end]').value =
            button.dataset.end || '';

        field('[data-edit-facility]').value =
            button.dataset.facilityId || '';

        field('[data-edit-purpose]').value =
            button.dataset.purpose || '';

        field('[data-edit-notes]').value =
            button.dataset.notes || '';
    };


    const openModal = button => {

        trigger = button;

        fillFromButton(button);

        previousOverflow =
            document.body.style.overflow;

        modal.classList.remove('hidden');

        modal.setAttribute(
            'aria-hidden',
            'false'
        );

        document.body.style.overflow =
            'hidden';


        requestAnimationFrame(() => {

            modal.classList.remove(
                'opacity-0'
            );

            panel.classList.remove(
                'translate-y-2',
                'scale-[0.98]'
            );

            field('[data-edit-name]')?.focus();

        });
    };


    const closeModal = () => {

        modal.classList.add(
            'opacity-0'
        );

        panel.classList.add(
            'translate-y-2',
            'scale-[0.98]'
        );


        setTimeout(() => {

            modal.classList.add('hidden');

            modal.setAttribute(
                'aria-hidden',
                'true'
            );

            document.body.style.overflow =
                previousOverflow;

            trigger?.focus();

        }, 200);
    };


    buttons.forEach(button => {

        button.addEventListener(
            'click',
            () => openModal(button)
        );

    });


    modal
        .querySelectorAll(
            '[data-appointment-edit-close]'
        )
        .forEach(button => {

            button.addEventListener(
                'click',
                closeModal
            );

        });


    backdrop?.addEventListener(
        'click',
        closeModal
    );


    document.addEventListener(
        'keydown',
        event => {

            if (
                event.key === 'Escape'
                &&
                !modal.classList.contains(
                    'hidden'
                )
            ) {
                closeModal();
            }
        }
    );

});
</script>

@endcan