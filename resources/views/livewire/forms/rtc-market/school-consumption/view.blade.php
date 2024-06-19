<div>
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Dashboard</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="../../forms">Forms</a></li>
                            <li class="breadcrumb-item active">View</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header" x-data="{
                        is_open: false
                    }">


                        <a class="btn btn-primary " href="add" role="button">Add
                            Data +</a>
                        <a class="btn btn-primary" href="#" data-toggle="modal" role="button"
                            @click="is_open = !is_open">
                            Import <i class="bx bx-upload"></i> </a>



                        <div class="my-2 border shadow-none card card-body" x-show="is_open">
                            <h5> Instructions</h5>
                            <p class="alert bg-info-subtle">Download the
                                School RTC
                                consumption template & uploading your
                                data.</p>

                            <form wire:submit='submitUpload'>
                                <div x-data>
                                    <a class="btn btn-soft-primary" href="#" data-toggle="modal" role="button"
                                        @click="$wire.downloadTemplate()">
                                        Download template <i class="bx bx-download"></i> </a>
                                    <hr>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        @if (session()->has('error'))
                                            <x-error-alert>{{ session()->get('error') }}</x-error-alert>
                                        @endif
                                        @if (session()->has('success'))
                                            <x-success-alert>{{ session()->get('success') }}</x-success-alert>
                                        @endif
                                    </div>
                                </div>
                                <div id="table-form">
                                    <div class="row justify-content-center">
                                        <div class=" col-12 col-md-8">


                                            <div class="mb-3">

                                                <label for="" class="form-label">Choose Project</label>
                                                <select class="form-select form-select-md"
                                                    wire:model.live.debounce.500ms="selectedProject" disabled>
                                                    <option selected value="">Select one</option>


                                                    @foreach ($projects as $project)
                                                        <option value="{{ $project->id }}">{{ $project->name }}

                                                        </option>
                                                    @endforeach
                                                </select>

                                                @error('selectedProject')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>



                                            <div class="mb-3" wire:loading.class='opacity-25'
                                                wire:target="selectedProject" wire:loading.attr='disabled'>

                                                <label for="" class="form-label">Choose Form</label>
                                                <select class="form-select form-select-md "
                                                    wire:model.live.debounce.500ms="selectedForm" disabled>
                                                    <option selected value="">Select one</option>
                                                    <div x-data="{ selectedProject: $wire.entangle('selectedProject') }" x-show="selectedProject">
                                                        @foreach ($forms as $form)
                                                            <option value="{{ $form->id }}">{{ $form->name }}

                                                            </option>
                                                        @endforeach
                                                    </div>




                                                </select>

                                                @error('selectedForm')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>


                                            <div class="mb-3" wire:loading.class='opacity-25'
                                                wire:target="selectedProject" wire:loading.attr='disabled'>

                                                <label for="" class="form-label">Choose Reporting Period</label>



                                                <select class="form-select form-select-md "
                                                    wire:model.live.debounce.500ms='selectedMonth'>

                                                    <option value="">Select one</option>

                                                    <div x-data="{ selectedProject: $wire.entangle('selectedProject') }" x-show="selectedProject">
                                                        @foreach ($months as $month)
                                                            <option wire:key='{{ $month->id }}'
                                                                value="{{ $month->id }}">
                                                                {{ $month->start_month . '-' . $month->end_month }}
                                                            </option>
                                                        @endforeach
                                                    </div>


                                                </select>


                                                @error('selectedMonth')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>

                                            <div class="mb-3" wire:loading.class='opacity-25'
                                                wire:target="selectedProject" wire:loading.attr='disabled'>

                                                <label for="" class="form-label">Choose Financial Year</label>
                                                <select
                                                    class="form-select form-select-md "wire:model.live.debounce.500ms='selectedFinancialYear'>

                                                    <option value="">Select one</option>
                                                    <div x-data="{ selectedProject: $wire.entangle('selectedProject') }" x-show="selectedProject">
                                                        @foreach ($financialYears as $year)
                                                            <option value="{{ $year->id }}">{{ $year->number }}
                                                            </option>
                                                        @endforeach

                                                    </div>

                                                </select>

                                                @error('selectedFinancialYear')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <x-filepond-single instantUpload="true" wire:model='upload' />
                                            @error('upload')
                                                <div class="d-flex justify-content-center">
                                                    <x-error class="text-center ">{{ $message }}</x-error>
                                                </div>
                                            @enderror
                                            <div class="mt-2 d-flex justify-content-center" x-data="{ disableButton: false, openSubmission: $wire.entangle('openSubmission') }">
                                                <button type="submit" @uploading-files.window="disableButton = true"
                                                    @finished-uploading.window="disableButton = false"
                                                    :disabled="disableButton === true || openSubmission === false"
                                                    class="btn btn-primary">
                                                    Submit data
                                                </button>


                                            </div>
                                            <div
                                                class="d-flex justify-content-center @if ($openSubmission) d-none @endif">
                                                <x-error class="text-center ">You can not submit a form right now
                                                    because submissions are closed for the moment!
                                                </x-error>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </form>

                            <small></small>
                        </div>
                    </div>
                    <div class="card-body" id="#datatable">

                        <livewire:tables.rtc-market.school-consumption-table />
                    </div>
                </div>

            </div>
        </div>



        {{--  <div x-data x-init="$wire.on('showModal', (e) => {

            const myModal = new bootstrap.Modal(document.getElementById(e.name), {})
            myModal.show();
        })">


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
