<div>
    <div class="col">
        <div class="mx-2 my-2 border shadow-none card border-light">
            <div class="card-body">
                <div class="my-2 border shadow-none card card-body ">
                    <h5> Instructions</h5>
                    <p class="alert bg-secondary-subtle text-uppercase">Use the Report template
                        &
                        upload your
                        data.</p>

                    <form wire:submit='submitUpload'>


                        <div id="table-form">


                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <div class="mb-3">
                                        <label for="" class="form-label">Organisation</label>
                                        <select
                                            class="form-select @error('selectedOrganisation')
                                            is-invalid
                                        @enderror"
                                            name="" id="" wire:model="selectedOrganisation">
                                            <option value="">Select one</option>
                                            @foreach ($organisations as $organisation)
                                                <option value="{{ $organisation->id }}">{{ $organisation->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('selectedOrganisation')
                                        <x-error class="mb-1">{{ $message }}</x-error>
                                    @enderror

                                </div>
                                <div class="col-12 col-md-4">

                                    <div class="mb-1">
                                        <label for="" class="form-label">Reporting Period</label>
                                        {{-- <x-flatpickr x-model="starting_period" /> --}}


                                        <select disabled
                                            class="form-select @error('selectedReportingPeriod')
                                            is-invalid
                                        @enderror"
                                            name="" id="" wire:model="selectedReportingPeriod"
                                            wire:loading.attr='disabled' wire:target='selectedReportingPeriod'>
                                            <option value="">Select one</option>
                                            @foreach ($reportingPeriod as $month)
                                                <option value="{{ $month->id }}">{{ $month->type }}

                                                </option>
                                            @endforeach
                                        </select>


                                    </div>
                                    @error('selectedReportingPeriod')
                                        <x-error class="mb-1">{{ $message }}</x-error>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="mb-1">
                                        <label for="" class="form-label">Project year</label>
                                        {{-- <x-flatpickr x-model="ending_period" /> --}}

                                        <select
                                            class="form-select @error('selectedFinancialYear')
                                            is-invalid
                                        @enderror"
                                            wire:model="selectedFinancialYear" wire:loading.attr='disabled'
                                            wire:target='selectedProject'>
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
                            <div class="row">

                                <x-alerts />


                            </div>
                        </div>
                        <div class="row justify-content-center">

                            <div class="col-12 ">
                                <x-filepond-single instantUpload="true" wire:model='upload' />
                                @error('upload')
                                    <div class="d-flex justify-content-center">
                                        <x-error class="text-center ">{{ $message }}</x-error>
                                    </div>
                                @enderror
                                <div class="mt-5 d-flex justify-content-center" x-data="{ disableButton: false, openSubmission: $wire.entangle('openSubmission') }">
                                    <button type="submit" @uploading-files.window="disableButton = true"
                                        @finished-uploading.window="disableButton = false"
                                        :disabled="disableButton === true || openSubmission === false"
                                        class="px-5 btn btn-warning ">
                                        Submit data
                                    </button>


                                </div>


                            </div>
                        </div>


                    </form>


                </div>
            </div>
        </div>
    </div>

</div>
