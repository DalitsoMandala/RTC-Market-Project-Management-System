<div>
    @section('title')
       Enterprise Management
    @endsection

    {{-- ── Toast Notification ─────────────────────────────────── --}}
    <div
        x-data="{ show: false, message: '', type: 'success' }"
        x-on:notify.window="message = $event.detail.message; type = $event.detail.type; show = true; setTimeout(() => show = false, 3500)"
        class="bottom-0 p-3 position-fixed end-0"
        style="z-index:9999"
    >
        <div
            x-show="show"
            x-transition.opacity
            :class="`toast show align-items-center text-white border-0 bg-${type}`"
        >
            <div class="d-flex">
                <div class="toast-body fw-semibold" x-text="message"></div>
                <button class="m-auto btn-close btn-close-white me-2" x-on:click="show = false"></button>
            </div>
        </div>
    </div>

    <div class="container-fluid">

        {{-- ── Breadcrumb ───────────────────────────────────────── --}}
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <div class="page-title-left col-12">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active">Manage Crops</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Stats Row ─────────────────────────────────────────── --}}
        <div class="mb-4 row g-3">
            <div class="col-6 col-md-3">
                <div class="py-3 text-center border-0 shadow-sm card">
                    <div class="fs-2 fw-bold text-success">{{ $this->stats['total'] }}</div>
                    <div class="small text-muted">Total Crops</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="py-3 text-center border-0 shadow-sm card">
                    <div class="fs-2 fw-bold text-warning">{{ $this->stats['varieties'] }}</div>
                    <div class="small text-muted">Total Varieties</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="py-3 text-center border-0 shadow-sm card">
                    <div class="fs-2 fw-bold text-warning">{{ $this->stats['default'] }}</div>
                    <div class="small text-muted">Default Crops</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="py-3 text-center border-0 shadow-sm card">
                    <div class="fs-2 fw-bold text-secondary">{{ $this->stats['custom'] }}</div>
                    <div class="small text-muted">Custom Crops</div>
                </div>
            </div>
        </div>

        {{-- ── Main Card ─────────────────────────────────────────── --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 card-title">Crops &amp; Varieties</h5>
                        <button class="btn btn-warning btn-sm" wire:click="openAddModal">
                            <i class="bx bx-plus-lg me-1"></i>Add Crop
                        </button>
                    </div>
                    <div class="card-body">

                        {{-- Search & Filter --}}
                        <div class="mb-3 row g-2 align-items-center">
                            <div class="col-md-5">
                                <div class="input-group">
                                    <span class="bg-white input-group-text border-end-0">
                                        <i class="bx bx-search text-muted"></i>
                                    </span>
                                    <input type="text"
                                           wire:model.live.debounce.300ms="search"
                                           class="form-control border-start-0 ps-0"
                                           placeholder="Search crops or varieties…"/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <select wire:model.live="typeFilter" class="form-select">
                                    <option value="">All Crops</option>
                                    <option value="default">Default Crops Only</option>
                                    <option value="custom">Custom Crops Only</option>
                                </select>
                            </div>
                            <div class="col-md-3 text-md-end">
                                <span class="text-muted small">
                                    Showing {{ $this->crops->count() }} of {{ $this->crops->total() }} crop(s)
                                </span>
                            </div>
                        </div>

                        {{-- Table --}}
                        <div class="rounded-3 table-responsive">
                            <table class="table mb-0 align-middle table-hover">
                                <thead class="table-secondary">
                                    <tr>
                                        <th>#</th>
                                        <th>Crop Name</th>
                                        <th>Type</th>
                                        <th>Varieties</th>
                                        <th class="text-center">Count</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($this->crops as $crop)
                                    <tr wire:key="crop-{{ $crop->id }}">
                                        <td class="text-muted fw-semibold">
                                            {{ ($this->crops->currentPage() - 1) * $this->crops->perPage() + $loop->iteration }}
                                        </td>
                                        <td class="fw-semibold">{{ $crop->name }}</td>
                                        <td>
                                            @if($crop->is_default)
                                                <span class="border badge bg-warning-subtle text-warning-emphasis border-warning-subtle">
                                                    <i class="bx bx-lock me-1"></i>Default
                                                </span>
                                            @else
                                                <span class="border badge bg-success-subtle text-success-emphasis border-success-subtle">
                                                    <i class="bx bx-lock-open me-1"></i>Custom
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="flex-wrap gap-1 d-flex">
                                                @forelse($crop->varieties as $variety)
                                                    <span class="border badge bg-warning-subtle text-warning-emphasis border-warning-subtle">
                                                        {{ $variety->name }}
                                                    </span>
                                                @empty
                                                    <span class="text-muted small fst-italic">No varieties</span>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary rounded-pill">
                                                {{ $crop->varieties_count }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="gap-2 d-flex justify-content-center">
                                                <button class="btn btn-sm btn-outline-warning"
                                                        wire:click="openVarietyModal({{ $crop->id }})"
                                                        title="Manage Varieties">
                                                    <i class="bx bx-tag me-1"></i>Varieties
                                                </button>
                                                @if(!$crop->is_default)
                                                    <button class="btn btn-sm btn-outline-warning"
                                                            wire:click="openEditModal({{ $crop->id }})"
                                                            title="Edit Crop">
                                                        <i class="bx bx-pencil"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger"
                                                            wire:click="openDeleteModal({{ $crop->id }})"
                                                            title="Delete Crop">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline-secondary"
                                                            disabled title="Protected">
                                                        <i class="bx bx-lock"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="py-5 text-center text-muted">
                                            <i class="mb-2 bx bx-sad fs-2 d-block"></i>
                                            No crops found.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-3 d-flex justify-content-between align-items-center">
                            <p class="mb-0 text-muted small">
                                <i class="bx bx-lock-fill text-warning me-1"></i>
                                Default crops are system-protected and cannot be deleted.
                            </p>
                            {{ $this->crops->links('pagination::bootstrap-5') }}
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
             Modal wiring — uses your existing x-modal component
             and your template's showModal / hideModal events
        ══════════════════════════════════════════════════════ --}}
        <div x-data x-init="
            $wire.on('showModal', (e) => {
                const myModal = new bootstrap.Modal(document.getElementById(e.name), {});
                myModal.show();
            });
            $wire.on('hideModal', () => {
                document.querySelectorAll('.modal.show').forEach(modal => {
                    const instance = bootstrap.Modal.getInstance(modal);
                    if (instance) instance.hide();
                });
            });
        ">

            {{-- ── Add Crop Modal ───────────────────────────────── --}}
            <x-modal id="add-crop-modal" title="Add New Crop">
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Crop Name <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           wire:model="newCropName"
                           class="form-control @error('newCropName') is-invalid @enderror"
                           placeholder="e.g. Maize"/>
                    @error('newCropName')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <label class="form-label fw-semibold">Varieties</label>
                <p class="mb-2 text-muted small">Optionally add variety names for this crop.</p>

                @foreach($newVarieties as $i => $v)
                <div class="mb-2 input-group" wire:key="nv-{{ $i }}">
                    <span class="input-group-text bg-light">
                        <i class="bx bx-tag text-success"></i>
                    </span>
                    <input type="text"
                           wire:model="newVarieties.{{ $i }}"
                           class="form-control"
                           placeholder="Variety name (e.g. Kinigi)"/>
                    @if(count($newVarieties) > 1)
                        <button class="btn btn-outline-danger btn-sm"
                                type="button"
                                wire:click="removeVarietyField({{ $i }})">
                            <i class="bx bx-x"></i>
                        </button>
                    @endif
                </div>
                @endforeach

                <button class="mt-1 btn btn-outline-success btn-sm" wire:click="addVarietyField">
                    <i class="bx bx-plus me-1"></i>Add another variety
                </button>

                <div class="px-0 pb-0 mt-3 modal-footer border-top-0">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" wire:click="saveCrop">
                        <i class="bx bx-check me-1"></i>Save Crop
                    </button>
                </div>
            </x-modal>

            {{-- ── Edit Crop Modal ──────────────────────────────── --}}
            <x-modal id="edit-crop-modal" title="Edit Crop">
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Crop Name <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           wire:model="editCropName"
                           wire:keydown.enter="updateCrop"
                           class="form-control @error('editCropName') is-invalid @enderror"/>
                    @error('editCropName')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="px-0 pb-0 mt-3 modal-footer border-top-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" wire:click="updateCrop">
                        <i class="bx bx-check-lg me-1"></i>Update
                    </button>
                </div>
            </x-modal>

            {{-- ── Delete Confirm Modal ─────────────────────────── --}}
            <x-modal id="delete-crop-modal" title="Delete Crop">
                <div class="py-2 text-center">
                    <i class="mb-3 bx bx-exclamation-triangle-fill text-danger fs-2 d-block"></i>
                    <p class="mb-1">
                        Are you sure you want to delete
                        <strong>{{ $deleteCropName }}</strong>
                        and all its varieties?
                    </p>
                    <p class="mb-0 small text-muted">This action cannot be undone.</p>
                </div>

                <div class="px-0 pb-0 mt-3 modal-footer border-top-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteCrop">
                        <i class="bx bx-trash me-1"></i>Delete
                    </button>
                </div>
            </x-modal>

            {{-- ── Variety Manager Modal ────────────────────────── --}}
            <x-modal id="variety-modal" title="Manage Varieties" size="modal-lg">
                {{-- Dynamic title via wire:ignore workaround --}}
                <p class="mb-3 text-muted small">
                    Managing varieties for <strong>{{ $activeCropName }}</strong>
                </p>

                {{-- Add Variety --}}
                <div class="mb-4 input-group">
                    <span class="input-group-text bg-light">
                        <i class="bx bx-tag text-warning"></i>
                    </span>
                    <input type="text"
                           wire:model="newVarietyName"
                           wire:keydown.enter="addVariety"
                           class="form-control @error('newVarietyName') is-invalid @enderror"
                           placeholder="New variety name…"/>
                    <button class="btn btn-warning" wire:click="addVariety">
                        <i class="bx bx-plus-lg me-1"></i>Add
                    </button>
                    @error('newVarietyName')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Varieties Table --}}
                @if($this->activeVarieties->count())
                <div class="table-responsive">
                    <table class="table mb-0 align-middle table-bordered table-hover">
                        <thead class="table-secondary">
                            <tr>
                                <th style="width:50px">#</th>
                                <th>Variety Name</th>
                                <th class="text-center" style="width:160px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this->activeVarieties as $variety)
                            <tr wire:key="variety-{{ $variety->id }}">
                                <td class="text-muted fw-semibold">{{ $loop->iteration }}</td>
                                <td>
                                    @if($editVarietyId === $variety->id)
                                        <input type="text"
                                               wire:model="editVarietyName"
                                               wire:keydown.enter="updateVariety"
                                               wire:keydown.escape="cancelEditVariety"
                                               class="form-control form-control-sm @error('editVarietyName') is-invalid @enderror"/>
                                        @error('editVarietyName')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    @else
                                        {{ $variety->name }}
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="gap-2 d-flex justify-content-center">
                                        @if($editVarietyId === $variety->id)
                                            <button class="btn btn-sm btn-success"
                                                    wire:click="updateVariety"
                                                    title="Save">
                                                <i class="bx bx-check"></i>
                                            </button>
                                            <button class="btn btn-sm btn-secondary"
                                                    wire:click="cancelEditVariety"
                                                    title="Cancel">
                                                <i class="bx bx-x"></i>
                                            </button>
                                        @else
                                            <button class="btn btn-sm btn-warning"
                                                    wire:click="openEditVariety({{ $variety->id }})"
                                                    title="Edit">
                                                <i class="bx bx-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger"
                                                    wire:click="deleteVariety({{ $variety->id }})"
                                                    title="Remove">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                    <div class="py-4 text-center text-muted">
                        <i class="mb-2 bx bx-tags fs-2 d-block"></i>
                        No varieties added yet.
                    </div>
                @endif

                <div class="px-0 pb-0 mt-3 modal-footer border-top-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </x-modal>

        </div>{{-- /x-data --}}
    </div>{{-- /container-fluid --}}
</div>
