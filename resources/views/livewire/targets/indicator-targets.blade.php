<div>
    @section('title', 'Indicator Targets')

    <div class="container-fluid">

        {{-- Page title --}}
        <div class="row">
            <x-breadcrumbs/>
        </div>

        {{-- Targets form --}}
        @hasanyrole('admin|manager')
            <div class="row">
                <div class="col-12">
                    <form wire:submit="save">
                        <x-table-card title="Indicator Targets" subtitle="Add or update targets for selected indicator"
                            :isContent="true">
                            <div>
                                <x-alerts />

                                {{-- Selection --}}
                                <div class="row">
                                    <div class="col-xxl-6 col-md-6">
                                        <div class="mb-3">
                                            <label for="selectedIndicator" class="form-label">Indicator</label>
                                            <select id="selectedIndicator" wire:model.live="selectedIndicator"
                                                wire:loading.attr="disabled"
                                                wire:target="selectedIndicator,selectedFinancialYear"
                                                class="form-select @error('selectedIndicator') is-invalid @enderror">
                                                <option value="">Select an indicator</option>
                                                @foreach ($this->indicators as $indicator)
                                                    <option value="{{ $indicator->id }}">
                                                        ({{ $indicator->indicator_no }}) {{ $indicator->indicator_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('selectedIndicator')
                                                <x-error class="mb-1">{{ $message }}</x-error>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-xxl-6 col-md-6">
                                        <div class="mb-3">
                                            <label for="selectedFinancialYear" class="form-label">Project year</label>
                                            <select id="selectedFinancialYear" wire:model.live="selectedFinancialYear"
                                                wire:loading.attr="disabled"
                                                wire:target="selectedIndicator,selectedFinancialYear"
                                                class="form-select @error('selectedFinancialYear') is-invalid @enderror">
                                                <option value="">Select a project year</option>
                                                @foreach ($this->financialYears as $year)
                                                    <option value="{{ $year->id }}">{{ $year->number }}</option>
                                                @endforeach
                                            </select>
                                            @error('selectedFinancialYear')
                                                <x-error class="mb-1">{{ $message }}</x-error>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- Targets table --}}
                                <div class="row" wire:loading.class="opacity-25 pe-none"
                                    wire:target="selectedIndicator,selectedFinancialYear,restoreTargets">
                                    <div class="col-12 table-responsive">
                                        <table class="table table-bordered table-striped table-hover align-middle">
                                            <thead>
                                                <tr>
                                                    <th>Target Name</th>
                                                    <th>Value</th>
                                                    <th style="width: 70px;"></th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @forelse ($targets as $index => $target)
                                                    <tr wire:key="target-row-{{ $index }}">
                                                        <td>
                                                            <select wire:model="targets.{{ $index }}.name"
                                                                class="form-select @error("targets.$index.name") is-invalid @enderror">
                                                                <option value="">Select a target</option>
                                                                @foreach ($this->disaggregations as $dsg)
                                                                    <option value="{{ $dsg->name }}">{{ $dsg->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error("targets.$index.name")
                                                                <x-error class="mb-1">{{ $message }}</x-error>
                                                            @enderror
                                                        </td>

                                                        <td>
                                                            <input type="number" step="any" min="0"
                                                                wire:model="targets.{{ $index }}.value"
                                                                class="form-control @error("targets.$index.value") is-invalid @enderror"
                                                                placeholder="Enter value..." />
                                                            @error("targets.$index.value")
                                                                <x-error class="mb-1">{{ $message }}</x-error>
                                                            @enderror
                                                        </td>

                                                        <td class="text-center">
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm custom-tooltip"
                                                                title="Remove target"
                                                                wire:click="removeTarget({{ $index }})">
                                                                <i class="bx bx-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="text-center text-muted fw-semibold py-4">
                                                            {{ $selectedIndicator && $selectedFinancialYear
                                                                ? 'No targets available. Add a target to get started.'
                                                                : 'Select an indicator and project year to manage targets.' }}
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>

                                            @if ($selectedIndicator && $selectedFinancialYear)
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="3">
                                                            <div class="d-flex flex-wrap gap-2">
                                                                <button type="button" wire:click="addTarget"
                                                                    class="btn btn-warning btn-sm custom-tooltip"
                                                                    title="Add target">
                                                                    Add Targets <i class="bx bx-plus"></i>
                                                                </button>

                                                                <button type="button" wire:click="restoreTargets"
                                                                    class="btn btn-secondary btn-sm custom-tooltip"
                                                                    title="Restore saved targets">
                                                                    Restore Targets <i class="bx bx-recycle"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer border-top-0">
                                <div class="mb-3 d-flex justify-content-center">
                                    <button type="submit" class="px-5 btn btn-warning" wire:loading.attr="disabled">
                                        Submit data
                                    </button>
                                </div>
                            </div>
                        </x-table-card>
                    </form>
                </div>
            </div>
        @endhasanyrole

        {{-- Saved targets list --}}
        <div class="row">
            <div class="col-12">
                <x-table-card title="Indicator Targets List" subtitle="Partner allocated targets" :isResponsive="false">
                    <livewire:tables.submission-target-table />
                </x-table-card>
            </div>
        </div>

        {{-- Partner allocation modal (opened via the `open-targets-modal` event) --}}
        <div class="modal fade" wire:ignore.self data-bs-backdrop="static" id="showTargets" tabindex="-1"
            role="dialog" aria-labelledby="showTargetsTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="showTargetsTitle">Assign Targets</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <x-alpine-alerts />

                        <form wire:submit="saveTargets" wire:loading.class="opacity-25 pe-none">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Partner</th>
                                            <th>Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($organisationTargets as $index => $target)
                                            <tr wire:key="org-target-{{ $target['id'] }}">
                                                <td>{{ $target['name'] }}</td>
                                                <td>
                                                    <input type="number" step="any" min="0"
                                                        class="form-control @error("organisationTargets.$index.value") is-invalid @enderror"
                                                        wire:model="organisationTargets.{{ $index }}.value" />
                                                    @error("organisationTargets.$index.value")
                                                        <x-error class="mb-1">{{ $message }}</x-error>
                                                    @enderror
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" class="text-center">
                                                <button type="submit" class="px-5 btn btn-warning">Submit data</button>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@script
    <script>
        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('showTargets'));

        $wire.on('open-targets-modal', () => modal.show());
        $wire.on('targets-saved', () => modal.hide());
    </script>
@endscript
