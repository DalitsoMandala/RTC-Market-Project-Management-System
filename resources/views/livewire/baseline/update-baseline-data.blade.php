<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Update Baseline Values</h4>

                <div class="page-title-right">
                    <ol class="m-0 breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">Update Baseline Values</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <x-alerts />
    <!-- end page title -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Update Baseline Data</h5>
                </div>
                <div class="card-body" x-data="{

                    indicator_id: $wire.entangle('indicator_id'),
                    baseline_value: $wire.entangle('baseline_value'),

                }">
                    <form wire:submit.prevent="confirmUpdate">
                        <div class="mb-3">
                            <label for="indicator_id" class="form-label">Indicator</label>
                            <select id="indicator_id" wire:model.live.debounce.600ms="indicator_id"
                                class="form-control @error('indicator_id') is-invalid @enderror">
                                <option value="">Select Indicator</option>
                                @foreach ($indicators as $indicator)
                                    <option value="{{ $indicator->id }}">
                                        ({{ $indicator->indicator_no }})
                                        {{ $indicator->indicator_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('indicator_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="baseline_value" class="form-label">Baseline Value</label>
                            <input type="number" step="any" id="baseline_value" wire:model="baseline_value"
                                class="form-control @error('baseline_value') is-invalid @enderror">
                            @error('baseline_value')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                            data-bs-target="#confirmModal">
                            Update
                        </button>
                    </form>
                </div>
                <div class="card-footer" wire:ignore>
                    <livewire:tables.baseline-table />
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div wire:ignore.self class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Confirm Update</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to update this baseline data?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" wire:click="save"
                        data-bs-dismiss="modal">Confirm</button>
                </div>
            </div>
        </div>
    </div>
</div>
