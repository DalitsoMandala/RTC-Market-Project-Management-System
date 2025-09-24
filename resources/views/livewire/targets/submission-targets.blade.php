<div>
    @section('title')
        Indicator Targets
    @endsection
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">


                    <div class="page-title-left col-12">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active">Indicator
                                Targets</li>

                            <li class="breadcrumb-item"><a href="{{ $routePrefix }}/targets">View Targets</a></li>

                        </ol>
                    </div>


                </div>
            </div>
        </div>
        <!-- end page title -->
        @hasanyrole('admin|manager')
            <div class="row">
                <div class="col-12" x-data="{
                    selectedIndicator: $wire.entangle('selectedIndicator'),
                    selectedFinancialYear: $wire.entangle('selectedFinancialYear'),
                }">

                    <form wire:submit='save'>
                        <div class="card">
                    <x-card-header>Indicator Targets</x-card-header>
                            <div class="card-body" x-data="{

                                showButton: false,


                            }"
                                x-effect="
                              if (selectedIndicator && selectedFinancialYear) {
                                        showButton = true;
                                        $wire.dispatch('update-targets');
                                    } else {
                                        showButton = false;
                                    }
                            ">

                                <x-alerts />

                                <div class="row">
                                    <div class="col-xxl-6">

                                        <div class="mb-3">
                                            <label for="" class="form-label">Indicators</label>
                                            <select
                                                class="form-select @error('selectedIndicator')
                                    is-invalid
                                @enderror"
                                                x-model="selectedIndicator" wire:loading.attr='disabled'>
                                                <option value="">Select one</option>
                                                @foreach ($indicators as $indicator)
                                                    <option value="{{ $indicator->id }}">
                                                        ({{ $indicator->indicator_no }})
                                                        {{ $indicator->indicator_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('selectedIndicator')
                                            <x-error class="mb-1">{{ $message }}</x-error>
                                        @enderror

                                    </div>


                                    <div class="col-xxl-6">
                                        <div class="mb-1">
                                            <label for="" class="form-label">Project year</label>


                                            <select
                                                class="form-select @error('selectedFinancialYear')
                                    is-invalid
                                @enderror"
                                                x-model="selectedFinancialYear" wire:loading.attr='disabled'
                                                wire:target='selectedIndicator'>
                                                <option value="">Select one</option>
                                                @foreach ($financialYears as $year)
                                                    <option value="{{ $year->id }}">{{ $year->number }}</option>
                                                @endforeach
                                            </select>

                                        </div>
                                        @error('selectedFinancialYear')
                                            <x-error class="mb-1">{{ $message }}</x-error>
                                        @enderror
                                    </div>


                                </div>




                                <div class="row" x-data="{
                                    targets: $wire.entangle('targets'),
                                    selectedIndicator: $wire.entangle('selectedIndicator'),
                                    selectedFinancialYear: $wire.entangle('selectedFinancialYear'),
                                    organisationTargets: $wire.entangle('organisationTargets'),
                                    getPreviousTargetValue(organisationTarget, mainTarget) {
                                        if (!(mainTarget.name === null) && !(mainTarget.value === null)) {
                                            const items = {
                                                selectedIndicator: selectedIndicator,
                                                selectedFinancialYear: selectedFinancialYear,
                                                organisationTarget: organisationTarget,
                                                mainTarget: mainTarget,

                                            };
                                            const oldValue = $wire.oldTargets(items);
                                            return oldValue;
                                        }
                                    }

                                }" wire:loading.class='opacity-25 pe-none'>
                                    <table class="table table-bordered table-striped table-hover">

                                        <thead>
                                            <tr>
                                                <th>Target Name</th>
                                                <th>Value</th>

                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody x-show="targets.length ===0">
                                            <tr>
                                                <td colspan="3" class="text-center text-muted fw-bolder">
                                                    No targets available
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tbody x-show="targets.length > 0">
                                            @foreach ($targets as $index => $target)
                                                <tr>
                                                    <td scope="row"> <select
                                                            class="form-select col-3 @error('targets.' . $index . '.name') is-invalid @enderror"
                                                            wire:model="targets.{{ $index }}.name"
                                                            wire:loading.attr='disabled' wire:loading.class='opacity-25'
                                                            wire:target='selectedIndicator'>
                                                            <option value="">Select one</option>
                                                            @foreach ($disaggregations as $dsg)
                                                                <option @if ($targets[$index]['name'] == $dsg->name) selected @endif
                                                                    value="{{ $dsg->name }}">{{ $dsg->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('targets.' . $index . '.name')
                                                            <x-error class="mb-1">{{ $message }}</x-error>
                                                        @enderror
                                                    </td>
                                                    <td>

                                                        <input type="text" id=""
                                                            class="form-control @error('targets.' . $index . '.value') is-invalid @enderror"
                                                            wire:model='targets.{{ $index }}.value'
                                                            placeholder="Enter Value..." aria-describedby="helpId" />

                                                        @error('targets.' . $index . '.value')
                                                            <x-error class="mb-1">{{ $message }}</x-error>
                                                        @enderror
                                                    </td>


                                                    <td>
                                                        <button class="btn btn-danger btn-sm custom-tooltip" title="Remove target"
                                                            @if ($targets[$index]['restricted'] == true) disabled @endif
                                                            wire:click.prevent="removeTarget({{ $index }})"> <i class="bx bx-trash"></i></button>

                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>

                                                <td colspan="3">
                                                    <div class="gap-1 d-flex justify-content-start">
                                                        <a wire:click='addTarget' title="Add input"
                                                            x-show="selectedIndicator && selectedFinancialYear"
                                                            class="btn btn-warning btn-sm custom-tooltip " href="#"
                                                            role="button">Add Targets <i class="bx bx-plus"></i></a>

                                                        <a wire:click="$dispatch('update-targets')"
                                                            x-show="targets.length > 0" wire:loading.attr='disabled'
                                                            title="Refill targets"
                                                            class="btn btn-secondary btn-sm custom-tooltip" href="#"
                                                            role="button">Restore Targets <i class="bx bx-recycle"></i></a>
                                                    </div>

                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>

                                </div>


                            </div>
                            <div class="card-footer border-top-0">
                                <div class="mb-3 d-flex justify-content-center">

                                    <button type="submit" class="px-5 btn btn-warning" wire:loading.attr='disabled'>
                                        Submit data
                                    </button>



                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        @endhasanyrole

        <div class="row">
            <div class="col-12">
                <div class="card ">
                <x-card-header>Indicator Targets List</x-card-header>
                    <div class=" card-body">
                        <livewire:tables.submission-target-table />
                    </div>
                </div>
            </div>
        </div>


        <div x-data="{
            row: $wire.entangle('rowId'),
            showModal() {
                $wire.getTargets(this.row);
                $('#showTargetBtn').click();


            },
            init() {
                $wire.on('show-targets', (event) => {
                    this.row = event.rowId;
                    this.showModal();
                })
            }
        }">


            <!-- Button trigger modal -->
            <button type="button" hidden id="showTargetBtn" class="btn btn-primary btn-lg" data-bs-toggle="modal"
                data-bs-target="#showTargets">
                Launch
            </button>

            <!-- Modal -->
            <div class="modal fade" wire:ignore.self data-bs-backdrop="static" id="showTargets" tabindex="-1"
                role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalTitleId">
                                Assign Targets
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <x-alpine-alerts />
                            <form wire:submit='saveTargets' wire:loading.class='opacity-25 pe-none'>
                                <div class="table-responsive">
                                <table class="table table-bordered ">
                                    <thead>
                                        <tr>
                                            <th>Partner</th>
                                            <th>Value</th>

                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($organisationTargets as $index => $target)
                                            <tr>
                                                <td>
                                                    <p>{{ $target['name'] }}</p>
                                                    <input type="hidden" class="form-control"
                                                        wire:model="organisationTargets.{{ $index }}.name" />

                                                </td>
                                                <td>

                                                    <input type="number"
                                                        class="form-control @error('organisationTargets.' . $index . '.value') is-invalid @enderror"
                                                        wire:model="organisationTargets.{{ $index }}.value" />

                                                    @error('organisationTargets.' . $index . '.value')
                                                        <x-error class="mb-1">{{ $message }}</x-error>
                                                    @enderror
                                                </td>

                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" class="text-center"> <button type="submit"
                                                    class="px-5 btn btn-warning">Submit data</button>
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

</div>
