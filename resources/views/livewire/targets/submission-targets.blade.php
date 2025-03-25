<div>
    @section('title')
        Standard Targets
    @endsection
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Dashboard</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ $routePrefix }}/reports">Reporting</a></li>
                            <li class="breadcrumb-item active">Submission
                                Targets</li>

                            <li class="breadcrumb-item"><a href="{{ $routePrefix }}/targets">Targets</a></li>

                        </ol>
                    </div>


                </div>
            </div>
        </div>
        <!-- end page title -->
        @hasanyrole('admin|manager')
            <div class="row">
                <div class="col-12">

                    <form wire:submit='save'>
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Submission Targets Form</h5>
                            </div>
                            <div class="card-body" x-data="{
                                selectedIndicator: $wire.entangle('selectedIndicator'),
                                selectedFinancialYear: $wire.entangle('selectedFinancialYear'),
                                showButton: false,
                            
                            
                            }" x-init="() => {
                            
                            
                                $watch(() => [selectedIndicator, selectedFinancialYear], ([indicator, year]) => {
                                    if (indicator && year) {
                                        showButton = true;
                                        $wire.dispatch('update-targets');
                                    } else {
                                        showButton = false;
                                    }
                                });
                            
                            
                            
                            }">

                                <x-alerts />

                                <div class="row">
                                    <div class="col-xxl-6">

                                        <div class="mb-3">
                                            <label for="" class="form-label">Indicators</label>
                                            <select
                                                class="form-select @error('selectedIndicator')
                                    is-invalid
                                @enderror"
                                                x-model="selectedIndicator">
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


                                <div x-show="showButton" class="my-2 ">
                                    <h5 class="card-title">Submission Targets</h5>
                                    <div class="gap-1 d-flex justify-content-end">
                                        <a wire:click='addTarget' title="Add input"
                                            class="btn btn-warning btn-sm custom-tooltip " href="#" role="button"><i
                                                class="bx bx-plus"></i></a>

                                        <a wire:click="$dispatch('update-targets')" wire:loading.attr='disabled'
                                            title="Refill targets" class="btn btn-success btn-sm custom-tooltip"
                                            href="#" role="button"><i class="bx bx-recycle"></i></a>
                                    </div>

                                </div>



                                <div class="row">
                                    @foreach ($targets as $index => $target)
                                        <hr>
                                        <div class="col-xxl-4 ">



                                            <div class="mb-3">
                                                <label for="" class="form-label">Select Disaggregation <span
                                                        class="count badge bg-warning-subtle text-warning">{{ $index + 1 }}</span></label>
                                                <select
                                                    class="form-select @error('targets.' . $index . '.name') is-invalid @enderror"
                                                    wire:model="targets.{{ $index }}.name"
                                                    wire:loading.attr='disabled' wire:loading.class='opacity-25'
                                                    wire:target='selectedIndicator'>
                                                    <option value="">Select one</option>
                                                    @foreach ($disaggregations->unique('name') as $dsg)
                                                        <option value="{{ $dsg->name }}">{{ $dsg->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @error('targets.' . $index . '.name')
                                                <x-error class="mb-1">{{ $message }}</x-error>
                                            @enderror





                                        </div>
                                        <div class="col-xxl-4">


                                            <div class="mb-3">
                                                <label for="" class="form-label">Value</label>
                                                <input type="text" id=""
                                                    class="form-control @error('targets.' . $index . '.value') is-invalid @enderror"
                                                    wire:model='targets.{{ $index }}.value'
                                                    placeholder="Enter Value..." aria-describedby="helpId" />

                                            </div>

                                            @error('targets.' . $index . '.value')
                                                <x-error class="mb-1">{{ $message }}</x-error>
                                            @enderror

                                        </div>
                                        <div class=" col-xxl-2 d-flex align-items-center">
                                            <button class="btn btn-theme-red"
                                                wire:click.prevent="removeTarget({{ $index }})"> <i
                                                    class="bx bx-trash-alt"></i></button>
                                        </div>
                                    @endforeach
                                </div>


                            </div>
                            <div class="card-footer">
                                <div class="d-flex justify-content-center">

                                    <button type="submit" class="btn btn-warning">
                                        Submit Data
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
                    <div class="card-header">
                        <h5 class="card-title">Submission Targets Table</h5>
                    </div>
                    <div class="card-body">
                        <livewire:tables.submission-target-table />
                    </div>
                </div>
            </div>
        </div>



        {{--  <div x-data x-init="$wire.on('showModal', (e) => {

            const myModal = new bootstrap.Modal(document.getElementById(e.name), {})
            myModal.show();
        })
        $wire.on('hideModal', (e) => {
            const modals = document.querySelectorAll('.modal.show');

            // Iterate over each modal and hide it using Bootstrap's modal hide method
            modals.forEach(modal => {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            });
        })
        ">


            <x-modal id="view-indicator-modal" title="edit">
                <form>
                    <div class="mb-3">

                        <x-text-input placeholder="Name of indicator..." />
                    </div>

                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>

                    </div>
                </form>
            </x-modal>

        </div> --}}




    </div>

</div>
