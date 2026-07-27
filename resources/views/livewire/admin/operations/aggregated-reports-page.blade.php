<div>

    @section('title')
        Aggregated Reports
    @endsection
    <style>
        .cursor-pointer {
            cursor: pointer;
        }
    </style>
    <div class="container-fluid">

        {{-- Breadcrumb --}}
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <div class="page-title-left col-12">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active">Aggregated Reports</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- ALERTS --}}
        <div class="row">
            <div class="col">
                <x-alerts />
            </div>
        </div>



        {{-- ═══════════════════════════════════════════════════════════════════
             CREATE FORM
        ════════════════════════════════════════════════════════════════════ --}}

        <div class="card mb-1 " x-data="{ showUploadForm: true }">
            <!-- Card Header -->
            <div class="px-3 py-2 card-header d-flex align-items-center justify-content-between cursor-pointer "
                :style="showUploadForm ? 'border-bottom: 1px solid #dee2e6;' : 'border-bottom: none;'"
                @click="showUploadForm = !showUploadForm">

                <div class="gap-2 d-flex align-items-center">
                    <div class="ar-form-icon">
                        <i class="bi bi-plus-circle-fill"></i>
                    </div>
                    <div>
                        <span class="fw-semibold text-body">Upload Report</span>
                        <span class="ms-2 text-muted small"
                            x-text="showUploadForm ? '(Click to collapse)' : '(Click to expand)'"></span>
                    </div>
                </div>

                <i class="bx text-muted" :class="showUploadForm ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
            </div>

            <!-- Card Body -->
            <div class="card-body" x-show="showUploadForm" x-cloak>
                <!-- Put your form or content here -->
                <livewire:admin.operations.upload-agg-report />
            </div>
        </div>
        <div class="border-0 shadow-sm card" x-data="{ showCreateForm: false }">

            <div class="px-3 py-2 card-header d-flex align-items-center justify-content-between cursor-pointer"
                :style="showCreateForm ? 'border-bottom: 1px solid #dee2e6;' : 'border-bottom: none;'"
                @click="showCreateForm = !showCreateForm">
                <div class="gap-2 d-flex align-items-center">
                    <div class="ar-form-icon">
                        <i class="bi bi-plus-circle-fill"></i>
                    </div>
                    <div>
                        <span class="fw-semibold text-body">Add New Record</span>
                        <span class="ms-2 text-muted small">(Click to
                            <span x-text="showCreateForm ? 'collapse' : 'expand'"></span>)</span>
                    </div>
                </div>
                <i class="bx text-muted" :class="showCreateForm ? 'bx-chevron-up' : 'bx-chevron-down'"></i>
            </div>

            {{-- Uses x-show for smooth DOM toggling --}}
            <div x-show="showCreateForm" x-cloak>
                <div class="card-body">
                    <form wire:submit.prevent="submitCreate">
                        @if ($duplicateId)
                            <div class="gap-3 p-3 mb-4 rounded-3 ar-duplicate-banner d-flex align-items-start">
                                <div class="flex-shrink-0 ar-duplicate-icon">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-1 fw-semibold text-warning-emphasis">A record with this combination
                                        already exists</p>
                                    <p class="mb-2 small text-warning-emphasis" style="opacity:.8;">
                                        The combination of Indicator, Disaggregation, Organisation, Financial Year,
                                        Reporting Period and Enterprise already has a value in the database.
                                        You cannot create a duplicate — please edit the existing record instead.
                                    </p>
                                    <button type="button" wire:click="jumpToDuplicate" class="btn btn-warning btn-sm">
                                        <i class="bi bi-arrow-down-circle me-1"></i>Go to existing record &amp; edit
                                    </button>
                                </div>
                                <button type="button" wire:click="$set('duplicateId', null)"
                                    class="flex-shrink-0 btn-close" title="Dismiss"></button>
                            </div>
                        @endif

                        <div class="row g-3">

                            <div class="col-12 col-md-6 col-xl-4">
                                <label class="mb-1 form-label small fw-medium">Indicator <span
                                        class="text-danger">*</span></label>
                                <select wire:model="formIndicatorId"
                                    class="form-select form-select-md @error('formIndicatorId') is-invalid @enderror">
                                    <option value="">— Select indicator —</option>
                                    @foreach ($indicators as $ind)
                                        <option value="{{ $ind->id }}">({{ $ind->indicator_no }})
                                            {{ Str::limit($ind->indicator_name, 50) }}</option>
                                    @endforeach
                                </select>
                                @error('formIndicatorId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6 col-xl-4">
                                <label class="mb-1 form-label small fw-medium">Disaggregation <span
                                        class="text-danger">*</span></label>
                                <select wire:model="formDisaggregationId"
                                    class="form-select form-select-md @error('formDisaggregationId') is-invalid @enderror">
                                    <option value="">— Select disaggregation —</option>
                                    @foreach ($disaggregationModels as $d)
                                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                                    @endforeach
                                </select>
                                @error('formDisaggregationId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6 col-xl-4">
                                <label class="mb-1 form-label small fw-medium">Organisation <span
                                        class="text-danger">*</span></label>
                                <select wire:model="formOrganisationId"
                                    class="form-select form-select-md @error('formOrganisationId') is-invalid @enderror">
                                    <option value="">— Select organisation —</option>
                                    @foreach ($organisations as $org)
                                        <option value="{{ $org->id }}">{{ $org->name }}</option>
                                    @endforeach
                                </select>
                                @error('formOrganisationId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6 col-xl-3">
                                <label class="mb-1 form-label small fw-medium">Project Year <span
                                        class="text-danger">*</span></label>
                                <select wire:model="formFinancialYearId"
                                    class="form-select form-select-md @error('formFinancialYearId') is-invalid @enderror">
                                    <option value="">— Select year —</option>
                                    @foreach ($financialYears as $fy)
                                        <option value="{{ $fy->id }}">Year {{ $fy->number }}</option>
                                    @endforeach
                                </select>
                                @error('formFinancialYearId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6 col-xl-3">
                                <label class="mb-1 form-label small fw-medium">Reporting Period <span
                                        class="text-danger">*</span></label>
                                <select wire:model="formReportingPeriodId"
                                    class="form-select form-select-md @error('formReportingPeriodId') is-invalid @enderror">
                                    <option value="">— Select period —</option>
                                    @foreach ($reportingPeriods as $rp)
                                        <option value="{{ $rp['id'] }}">{{ $rp['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('formReportingPeriodId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6 col-xl-3">
                                <label class="mb-1 form-label small fw-medium">Enterprise</label>
                                <select wire:model="formCrop"
                                    class="form-select form-select-md @error('formCrop') is-invalid @enderror">
                                    <option value="">None</option>
                                    <option value="Cassava">Cassava</option>
                                    <option value="Potato">Potato</option>
                                    <option value="Sweet potato">Sweet potato</option>
                                </select>
                                @error('formCrop')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6 col-xl-3">
                                <label class="mb-1 form-label small fw-medium">Value <span
                                        class="text-danger">*</span></label>
                                <input type="number" min="0" step="any" wire:model="formValue"
                                    class="form-control form-control-md @error('formValue') is-invalid @enderror"
                                    placeholder="0">
                                @error('formValue')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <div class="gap-2 mt-4 d-flex align-items-center">
                            <button wire:loading.attr="disabled" type="submit" class="px-4 btn btn-warning btn-md">
                                <span wire:loading.remove wire:target="submitCreate"><i
                                        class="bx bx-save me-1"></i>Save Record</span>
                                <span wire:loading wire:target="submitCreate">
                                    <span class="spinner-border spinner-border-sm"></span> Saving…
                                </span>
                            </button>
                            {{-- Updated Cancel button to trigger Alpine close directly --}}
                            <button type="button" @click="showCreateForm = false"
                                class="btn btn-outline-danger btn-md">
                                <i class="bx bx-x me-1"></i>Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        {{-- ═══════════════════════════════════════════════════════════════════
             FILTERS + TABLE
        ════════════════════════════════════════════════════════════════════ --}}

        {{--
            FIX: Removed the x-data with confirmOpen from this wrapper entirely.
            Each modal now owns its own isolated x-data scope below, so they
            cannot interfere with each other or get clobbered by Livewire re-renders.
        --}}
        <div id="aggregated-report-table-top"
            x-on:scroll-to-table.window="$el.scrollIntoView({ behavior: 'smooth', block: 'start' })">

            {{-- ── Toolbar ── --}}
            <div class="mb-3 border-0 shadow-sm card">
                <div class="py-3 card-body">
                    <div class="mb-3 row g-2 align-items-end">
                        <div class="col-12 col-md-5 d-none">
                            <label class="mb-1 form-label small text-muted">Search</label>
                            <div class="input-group input-group-sm">
                                <span class="bg-white input-group-text border-end-0">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input type="text" wire:model.live.debounce.400ms="search"
                                    class="form-control border-start-0 ps-0"
                                    placeholder="Indicator name, number, organisation…">
                                @if ($search)
                                    <button wire:click="$set('search', '')" class="btn btn-outline-secondary btn-sm"
                                        title="Clear search">
                                        <i class="bi bi-x"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="col-12 col-md-3 ms-auto text-md-end align-content-center">
                            <button type="button" wire:click="resetPage"
                                class="btn btn-outline-secondary btn-sm me-3">Reset Page</button>
                            <label class="mb-1 form-label small text-muted">Per page</label>
                            <select wire:model.live="perPage" class="form-select form-select-md"
                                style="width:auto;display:inline-block;">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-6 col-md-4 col-xl-2">
                            <label class="mb-1 form-label small text-muted">Indicator</label>
                            <select wire:model.live="filterIndicator" class="form-select form-select-md">
                                <option value="">All indicators</option>
                                @foreach ($indicators as $ind)
                                    <option value="{{ $ind->id }}">({{ $ind->indicator_no }})
                                        {{ Str::limit($ind->indicator_name, 40) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-4 col-xl-2">
                            <label class="mb-1 form-label small text-muted">Organisation</label>
                            <select wire:model.live="filterOrganisation" class="form-select form-select-md">
                                <option value="">All organisations</option>
                                @foreach ($organisations as $org)
                                    <option value="{{ $org->id }}">{{ $org->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-4 col-xl-2">
                            <label class="mb-1 form-label small text-muted">Project year</label>
                            <select wire:model.live="filterFinancialYear" class="form-select form-select-md">
                                <option value="">All years</option>
                                @foreach ($financialYears as $fy)
                                    <option value="{{ $fy->id }}">Year {{ $fy->number }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-4 col-xl-2">
                            <label class="mb-1 form-label small text-muted">Period</label>
                            <select wire:model.live="filterReportingPeriod" class="form-select form-select-md">
                                <option value="">All periods</option>
                                @foreach ($reportingPeriods as $rp)
                                    <option value="{{ $rp['id'] }}">{{ $rp['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-4 col-xl-2">
                            <label class="mb-1 form-label small text-muted">Disaggregation</label>
                            <select wire:model.live="filterDisaggregation" class="form-select form-select-md">
                                <option value="">All</option>
                                @foreach ($disaggregations as $d)
                                    <option value="{{ $d }}">{{ $d }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-4 col-xl-2">
                            <label class="mb-1 form-label small text-muted">Enterprise</label>
                            <select wire:model.live="filterCrop" class="form-select form-select-md">
                                <option value="">-</option>
                                <option value="null">All crop types</option>
                                <option value="Cassava">Cassava</option>
                                <option value="Potato">Potato</option>
                                <option value="Sweet potato">Sweet potato</option>
                            </select>
                        </div>
                    </div>

                    @php
                        $hasFilters =
                            $filterIndicator ||
                            $filterOrganisation ||
                            $filterFinancialYear ||
                            $filterReportingPeriod ||
                            $filterDisaggregation ||
                            $filterCrop ||
                            $search;
                    @endphp
                    @if ($hasFilters)
                        <div class="flex-wrap gap-2 mt-3 d-flex align-items-center">
                            <span class="text-muted small">Active filters:</span>
                            @if ($search)
                                <span
                                    class="border badge bg-secondary-subtle text-secondary border-secondary-subtle">Search:
                                    "{{ $search }}"</span>
                            @endif
                            @if ($filterIndicator)
                                <span
                                    class="border badge bg-secondary-subtle text-secondary border-secondary-subtle">Indicator</span>
                            @endif
                            @if ($filterOrganisation)
                                <span
                                    class="border badge bg-secondary-subtle text-secondary border-secondary-subtle">Organisation</span>
                            @endif
                            @if ($filterFinancialYear)
                                <span
                                    class="border badge bg-secondary-subtle text-secondary border-secondary-subtle">Year</span>
                            @endif
                            @if ($filterReportingPeriod)
                                <span
                                    class="border badge bg-secondary-subtle text-secondary border-secondary-subtle">Period</span>
                            @endif
                            @if ($filterDisaggregation)
                                <span
                                    class="border badge bg-secondary-subtle text-secondary border-secondary-subtle">{{ $filterDisaggregation }}</span>
                            @endif
                            @if ($filterCrop)
                                <span
                                    class="border badge bg-secondary-subtle text-secondary border-secondary-subtle">{{ $filterCrop === 'null' ? 'All crops' : $filterCrop }}</span>
                            @endif
                            <button wire:click="clearFilters" class="p-0 btn btn-link btn-sm text-danger ms-1">
                                <i class="bx bx-x-circle me-1"></i>Clear all
                            </button>
                        </div>
                    @endif
                </div>
            </div>


            {{-- ── Table ── --}}
            <div class="border-0 shadow-sm card position-relative">
                <div class="p-0 card-body">

                    <div wire:loading class="pg-loading-overlay">
                        <div class="spinner-border text-secondary" role="status">
                            <span class="visually-hidden">Loading…</span>
                        </div>
                    </div>

                    <div class="table-responsive rounded-3">
                        <table class="table mb-0 align-middle table-hover table-bordered small">
                            <thead class="table-secondary">
                                <tr>
                                    <th style="min-width:300px;" class="sortable-th">Indicator</th>
                                    <th>Disaggregation</th>
                                    <th class="sortable-th" wire:click="sortBy('organisation')">
                                        Organisation
                                        <i class="bx {{ $sortField === 'organisation' ? ($sortDirection === 'asc' ? 'bx-chevron-up' : 'bx-chevron-down') : 'bx-chevron-down' }} ms-1"
                                            style="font-size:.8rem;opacity:{{ $sortField === 'organisation' ? '.75' : '.25' }};"></i>
                                    </th>
                                    <th class="sortable-th" wire:click="sortBy('financial_year')">
                                        Year
                                        <i class="bx {{ $sortField === 'financial_year' ? ($sortDirection === 'asc' ? 'bx-chevron-up' : 'bx-chevron-down') : 'bx-chevron-down' }} ms-1"
                                            style="font-size:.8rem;opacity:{{ $sortField === 'financial_year' ? '.75' : '.25' }};"></i>
                                    </th>
                                    <th>Period</th>
                                    <th class="sortable-th" wire:click="sortBy('crop')">
                                        Enterprise
                                        <i class="bx {{ $sortField === 'crop' ? ($sortDirection === 'asc' ? 'bx-chevron-up' : 'bx-chevron-down') : 'bx-chevron-down' }} ms-1"
                                            style="font-size:.8rem;opacity:{{ $sortField === 'crop' ? '.75' : '.25' }};"></i>
                                    </th>
                                    <th class="sortable-th" style="width:160px;" wire:click="sortBy('value')">
                                        Value
                                        <i class="bx {{ $sortField === 'value' ? ($sortDirection === 'asc' ? 'bx-chevron-up' : 'bx-chevron-down') : 'bx-chevron-down' }} ms-1"
                                            style="font-size:.8rem;opacity:{{ $sortField === 'value' ? '.75' : '.25' }};"></i>
                                    </th>
                                    <th style="width:150px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rows as $row)
                                    <tr wire:key="row-{{ $row->id }}"
                                        class="{{ $editingId === $row->id ? 'table-warning ar-editing-row' : '' }}"
                                        id="row-{{ $row->id }}">

                                        <td>
                                            <span
                                                class="text-muted me-1 small">({{ $row->systemReport->indicator->indicator_no ?? '—' }})</span>
                                            {{ $row->systemReport->indicator->indicator_name ?? '—' }}
                                        </td>
                                        <td>{{ $row->name ?? '—' }}</td>
                                        <td>{{ $row->systemReport->organisation->name ?? '—' }}</td>
                                        <td class="text-center">
                                            @if ($row->systemReport->financialYear)
                                                <span class="badge bg-secondary">Yr
                                                    {{ $row->systemReport->financialYear->number }}</span>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>
                                            {{ $row->systemReport->reportingPeriod ? $row->systemReport->reportingPeriod->start_month . '–' . $row->systemReport->reportingPeriod->end_month : '—' }}
                                        </td>
                                        <td>{{ $row->systemReport->crop ?? 'All crops' }}</td>

                                        {{-- Value cell --}}
                                        <td>
                                            @if ($editingId === $row->id)
                                                <input type="number" wire:model="editValue"
                                                    class="form-control form-control-sm @error('editValue') is-invalid @enderror"
                                                    min="0" step="any" style="max-width:120px;" autofocus>
                                                @error('editValue')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            @else
                                                <span class="fw-semibold">{{ $row->value ?? '—' }}</span>
                                            @endif
                                        </td>

                                        {{-- Actions cell --}}
                                        <td wire:loading.class='pe-none opacity-50'>
                                            @if ($editingId === $row->id)
                                                <div class="gap-1 d-flex">
                                                    {{--
                                                        FIX: No wire:loading.attr="disabled" here.
                                                        That attribute was disabling the button the instant it was
                                                        clicked (before Alpine could open the modal), so the modal
                                                        never appeared. Loading feedback lives on the modal's own
                                                        confirm button instead.
                                                    --}}
                                                    <button wire:click="requestSave" class="btn btn-success btn-sm"
                                                        title="Save changes">
                                                        <i class="bx bx-check"></i>
                                                    </button>
                                                    <button wire:click="cancelEdit" wire:loading.attr="disabled"
                                                        wire:target="cancelEdit,confirmSave"
                                                        class="btn btn-outline-danger btn-sm" title="Cancel">
                                                        <i class="bx bx-x"></i>
                                                    </button>
                                                </div>
                                            @else
                                                <div class="gap-1 d-flex">
                                                    <button wire:click="startEdit({{ $row->id }})"
                                                        class="btn btn-outline-warning btn-sm" title="Edit value"
                                                        @if ($editingId && $editingId !== $row->id) disabled @endif>
                                                        <i class="bx bx-pencil"></i>
                                                        <span class="d-none d-xl-inline ms-1">Edit</span>
                                                    </button>
                                                    <button wire:click="requestDelete({{ $row->id }})"
                                                        class="btn btn-outline-danger btn-sm" title="Delete record"
                                                        @if ($editingId) disabled @endif>
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="py-5 text-center text-muted">
                                            <i class="mb-2 bi bi-inbox fs-2 d-block"></i>
                                            No records found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div
                        class="gap-2 px-3 py-2 d-flex flex-column flex-md-row align-items-md-center justify-content-between border-top">
                        <p class="mb-0 text-muted small">
                            Showing
                            <strong>{{ $rows->firstItem() ?? 0 }}</strong>–<strong>{{ $rows->lastItem() ?? 0 }}</strong>
                            of <strong>{{ $rows->total() }}</strong> records
                        </p>
                        <div x-data>{{ $rows->links() }}</div>
                    </div>

                </div>
            </div>

        </div>{{-- /#aggregated-report-table-top --}}


        {{-- ═══════════════════════════════════════════════════════════════════
             MODALS — bootstrap.Modal JS via showModal Livewire event
        ════════════════════════════════════════════════════════════════════ --}}
        <div x-data x-init="$wire.on('showModal', (e) => {
        
            const el = document.getElementById(e.name)
            if (el) bootstrap.Modal.getOrCreateInstance(el).show()
        });
        $wire.on('hideModal', (e) => {
            const el = document.getElementById(e.name)
            if (el) bootstrap.Modal.getInstance(el)?.hide()
        });">

            {{-- ── Confirm Save ── --}}
            <x-modal id="confirm-save-modal" title="Confirm Update">
                <div class="gap-3 d-flex align-items-start">
                    <span
                        class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle bg-warning-subtle text-warning"
                        style="width:38px;height:38px;font-size:1rem;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </span>
                    <div>
                        <p class="mb-1 fw-medium">Are you sure you want to save this change?</p>
                        <p class="mb-0 text-muted small">The existing report value will be overwritten immediately in
                            the database.</p>
                    </div>
                </div>

                @if ($editValue !== null)
                    <div class="p-3 mt-3 border rounded-2 bg-success-subtle border-success-subtle">
                        <span class="text-muted small">New value to be saved:</span>
                        <span class="fw-bold ms-2 fs-5 text-success">{{ $editValue }}</span>
                    </div>
                @endif

                <div class="gap-2 px-0 pb-0 mt-4 modal-footer border-top-0" wire:loading.class='pe-none'>
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-success" wire:click="confirmSave"
                        wire:loading.attr="disabled" wire:target="confirmSave">
                        <div wire:loading.remove wire:target="confirmSave">
                            <i class="bx bx-check me-1"></i>Yes, Save
                        </div>
                        <div wire:loading wire:target="confirmSave">
                            <span class="spinner-border spinner-border-sm"></span>Saving…
                        </div>
                    </button>
                </div>
            </x-modal>

            {{-- ── Confirm Delete ── --}}
            <x-modal id="confirm-delete-modal" title="Confirm Delete">
                <div class="gap-3 d-flex align-items-start">
                    <span
                        class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle bg-danger-subtle text-danger"
                        style="width:38px;height:38px;font-size:1rem;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </span>
                    <div>
                        <p class="mb-1 fw-medium">Are you sure you want to delete this record?</p>
                        <p class="mb-0 text-muted small">
                            This aggregated report will be permanently removed from the database.
                            This action cannot be undone.
                        </p>
                    </div>
                </div>

                <div class="gap-2 px-0 pb-0 mt-4 modal-footer border-top-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-danger" wire:click="confirmDelete"
                        wire:loading.attr="disabled" wire:target="confirmDelete">
                        <span wire:loading.remove wire:target="confirmDelete">
                            <i class="bx bx-trash me-1"></i>Yes, Delete
                        </span>
                        <span wire:loading wire:target="confirmDelete">
                            <span class="spinner-border spinner-border-sm"></span>Deleting…
                        </span>
                    </button>
                </div>
            </x-modal>

        </div>

    </div>{{-- /.container-fluid --}}


    @push('styles')
        <style>
            .sortable-th {
                cursor: pointer;
                user-select: none;
                white-space: nowrap;
            }

            .sortable-th:hover {
                background-color: rgba(0, 0, 0, .04);
            }

            .pg-loading-overlay {
                position: absolute;
                inset: 0;
                background: rgba(255, 255, 255, .65);
                z-index: 10;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: inherit;
            }

            /* Create form */
            .ar-form-icon {
                width: 32px;
                height: 32px;
                border-radius: 8px;
                background: #dbeafe;
                color: #2563eb;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1rem;
            }

            /* Duplicate banner */
            .ar-duplicate-banner {
                background: #fffbeb;
                border: 1.5px solid #fcd34d;
                border-left: 4px solid #f59e0b;
            }

            .ar-duplicate-icon {
                width: 36px;
                height: 36px;
                border-radius: 50%;
                background: #fef3c7;
                color: #d97706;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1rem;
                flex-shrink: 0;
            }

            /* Editing row */
            .ar-editing-row {
                box-shadow: inset 3px 0 0 #ffc107;
            }
        </style>
    @endpush

</div>
