
<div>
    @section('title')
        Submission period
    @endsection

    <div class="container-fluid">

        <div class="row">
            <x-breadcrumbs />
        </div>

        <div class="row">
            <div class="col-12">

                <x-alerts />

                <x-table-card
                    :title="'Submission Period Form'"
                    :subtitle="'Manage submission periods for partners'"
                    :isContent="true"
                >

                    <div x-data="submissionForm">

                        <form
                            class="@if (auth()->user()->hasAnyRole('monitor')) pe-none opacity-50 @endif"
                            @edit-period.window="scrollToTop()"
                            wire:submit.debounce.1000ms="save"
                            id="form"
                        >

                            {{-- Project --}}
                            <div class="mb-3">
                                <label for="project-select" class="form-label">
                                    Choose Project
                                </label>

                                <select
                                    id="project-select"
                                    class="form-select form-select-md @error('selectedProject') is-invalid @enderror"
                                    wire:model.live.debounce.1000ms="selectedProject"
                                >
                                    <option value="">Select one</option>

                                    @foreach ($projects as $project)
                                        <option value="{{ $project->id }}">
                                            {{ $project->name }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('selectedProject')
                                    <x-error>{{ $message }}</x-error>
                                @enderror
                            </div>

                            {{-- Select all indicators --}}
                            <div class="my-2 form-check d-none">
                                <input
                                    class="form-check-input"
                                    disabled
                                    x-model="selectAllIndicators"
                                    type="checkbox"
                                    id="select-all-indicators"
                                />

                                <label class="form-check-label" for="select-all-indicators">
                                    Select All indicators
                                </label>
                            </div>

                            {{-- Reporting period --}}
                            <div class="@if (!$selectedProject) pe-none opacity-25 @endif">
                                <div
                                    class="mb-3"
                                    wire:loading.class="opacity-25"
                                    wire:target="selectedProject, selectedIndicator, selectedForm"
                                    wire:loading.attr="disabled"
                                >
                                    <label for="month-select" class="form-label">
                                        Choose Reporting Period
                                    </label>

                                    <select
                                        id="month-select"
                                        class="form-select form-select-md @error('selectedMonth') is-invalid @enderror"
                                        wire:model.live.debounce.700ms="selectedMonth"
                                    >
                                        <option value="">Select one</option>

                                        @foreach ($months as $month)
                                            <option
                                                value="{{ $month->id }}"
                                                wire:key="month-{{ $month->id }}"
                                                @disabled($month->type === 'UNSPECIFIED')
                                            >
                                                {{ $month->start_month . '-' . $month->end_month }}
                                                ({{ $month->type }})
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('selectedMonth')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>
                            </div>

                            {{-- Financial year --}}
                            <div class="@if (!$selectedProject) pe-none opacity-25 @endif">
                                <div
                                    class="mb-3"
                                    wire:loading.class="opacity-25"
                                    wire:target="selectedProject, selectedIndicator, selectedForm"
                                    wire:loading.attr="disabled"
                                >
                                    <label for="year-select" class="form-label">
                                        Choose Project Year
                                    </label>

                                    <select
                                        id="year-select"
                                        class="form-select form-select-md @error('selectedFinancialYear') is-invalid @enderror"
                                        wire:model.live.debounce.700ms="selectedFinancialYear"
                                    >
                                        <option value="">Select one</option>

                                        @foreach ($financialYears as $year)
                                            <option value="{{ $year->id }}">
                                                {{ $year->number }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('selectedFinancialYear')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>
                            </div>

                            {{-- Start date --}}
                            <div class="mb-3">
                                <label for="start-period" class="form-label">
                                    Start of submissions
                                </label>

                                <x-flatpickr
                                    :enableTime="true"
                                    :class="$errors->has('start_period') ? 'is-invalid' : ''"
                                    id="start-period"
                                    wire:model.debounce.700ms="start_period"
                                    type="date"
                                    :time="'00:00:00'"
                                />

                                @error('start_period')
                                    <x-error>{{ $message }}</x-error>
                                @enderror
                            </div>

                            {{-- End date --}}
                            <div class="mb-3">
                                <label for="end-period" class="form-label">
                                    End of submissions
                                </label>

                                <x-flatpickr
                                    :enableTime="true"
                                    :class="$errors->has('end_period') ? 'is-invalid' : ''"
                                    id="end-period"
                                    wire:model.debounce.700ms="end_period"
                                    type="date"
                                    :time="'23:59:59'"
                                />

                                @error('end_period')
                                    <x-error>{{ $message }}</x-error>
                                @enderror
                            </div>

                            {{-- Organisations --}}
                            <div class="mb-3" x-show="!selectAllIndicators">
                                <label for="organisations" class="form-label">
                                    Organisations
                                </label>

                                <select
                                    id="organisations"
                                    class="form-select form-select-md"
                                    x-model="selectedOrganisations"
                                    multiple
                                >
                                    <option value="0">All</option>

                                    @foreach ($organisations as $organ)
                                        <option value="{{ $organ->id }}">
                                            {{ $organ->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Submission status settings --}}
                            @if ($rowId)

                                {{-- Open for submissions --}}
                                <div class="mb-3" dir="ltr">
                                    <label for="is-open" class="form-label">
                                        Open for submissions
                                    </label>

                                    <div class="square-switch d-flex align-items-baseline">
                                        <input
                                            type="checkbox"
                                            wire:model.live="is_open"
                                            id="is-open"
                                            switch="none"
                                            @disabled((bool) $is_expired)
                                        >

                                        <label
                                            for="is-open"
                                            data-on-label="Yes"
                                            data-off-label="No"
                                        ></label>
                                    </div>
                                    @error('is_open')
    <x-error>{{ $message }}</x-error>
@enderror
                                </div>

                                {{-- Restricted --}}
                                <div class="mb-3" dir="ltr">
                                    <label for="is-restricted" class="form-label">
                                        Restricted (Only Admins/Managers can submit)
                                    </label>

                                    <div class="square-switch d-flex align-items-baseline">
                                        <input
                                            type="checkbox"
                                            wire:model.live="is_restricted"
                                            id="is-restricted"
                                            switch="none"
                                            @disabled((bool) $is_expired)
                                        >

                                        <label
                                            for="is-restricted"
                                            data-on-label="Yes"
                                            data-off-label="No"
                                        ></label>
                                    </div>
                                </div>

                            @endif

                            {{-- Notify users --}}
                            <div
                                class="mb-3 @if ($rowId) pe-none opacity-50 @endif"
                                dir="ltr"
                            >
                                <label for="notify-users" class="form-label">
                                    Notify Users (Send Email Notification)
                                </label>

                                <div class="square-switch d-flex align-items-baseline">
                                    <input
                                        type="checkbox"
                                        x-model="notifyUsers"
                                        id="notify-users"
                                        switch="none"
                                    >

                                    <label
                                        for="notify-users"
                                        data-on-label="Yes"
                                        data-off-label="No"
                                    ></label>
                                </div>
                            </div>

                            {{-- Bypass notifications --}}
                            <div class="mb-3" dir="ltr">
                                <label for="bypass-notifications" class="form-label">
                                    Bypass Notifications
                                </label>

                                <div class="square-switch d-flex align-items-baseline">
                                    <input
                                        type="checkbox"
                                        x-model="bypassNotifications"
                                        id="bypass-notifications"
                                        switch="none"
                                    >

                                    <label
                                        for="bypass-notifications"
                                        data-on-label="Yes"
                                        data-off-label="No"
                                    ></label>
                                </div>
                            </div>

                            {{-- Expiration --}}
                            @if ($rowId)
                                <div class="mb-3" dir="ltr">
                                    <label for="is-expired" class="form-label">
                                        Cancel/Set to Expire
                                    </label>

                                    <div class="square-switch d-flex align-items-baseline">
                                        <input
                                            type="checkbox"
                                            wire:model.live="is_expired"
                                            id="is-expired"
                                            switch="none"
                                        >

                                        <label
                                            for="is-expired"
                                            data-on-label="Yes"
                                            data-off-label="No"
                                        ></label>
                                    </div>
                                </div>
                            @endif

                            {{-- Actions --}}
                            <div class="d-flex gap-2">
                                <button
                                    class="btn btn-warning"
                                    type="submit"
                                    wire:loading.attr="disabled"
                                    @click="scrollToTop()"
                                >
                                    {{ $rowId ? 'Update' : 'Submit' }}
                                </button>

                                <button
                                    class="btn btn-secondary"
                                    type="button"
                                    wire:click.debounce.1000ms="resetData"
                                    wire:loading.attr="disabled"
                                >
                                    Reset
                                </button>
                            </div>

                        </form>

                    </div>

                </x-table-card>

                {{-- Submission period table --}}
                @php
                    $route = Route::current()->getPrefix();
                @endphp

                <x-table-card
                    :title="'Submission Period Table'"
                    :subtitle="'Submission Period Data'"
                >
                    <livewire:tables.submission-period-table
                        :currentRoutePrefix="$route"
                    />
                </x-table-card>

            </div>
        </div>

        @script
            <script>
                Alpine.data('submissionForm', () => ({
                    selectAllIndicators: @entangle('selectAllIndicators'),
                    skipTargets: @entangle('skipTargets'),
                    selectedOrganisations: @entangle('selectedOrganisations'),
                    notifyUsers: @entangle('notifyUsers'),
                    bypassNotifications: @entangle('bypassNotifications'),
                    rowId: @entangle('rowId'),

                    init() {
                        this.$watch('selectedOrganisations', () => {
                            this.toggleSelection();
                        });
                    },

                    toggleSelection() {
                        const selected = Array.isArray(this.selectedOrganisations)
                            ? this.selectedOrganisations.map(String)
                            : [];

                        if (selected.includes('0')) {
                            this.selectedOrganisations = ['0'];
                        } else {
                            this.selectedOrganisations = selected.filter(
                                id => id !== '0'
                            );
                        }
                    },

                    scrollToTop() {
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    }
                }));

                $('.goUp').on('click', () => {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            </script>
        @endscript

    </div>
</div>
