<div>
    @section('title')
        Reports
    @endsection
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">


                    <div class="page-title-left col-12">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active">Reporting</li>
                        </ol>
                    </div>

                </div>
            </div>

        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col">
                <x-alerts />
            </div>
        </div>

        <div class="card ">
            <x-card-header>Reports</x-card-header>

            <div class=" card-body">

                <div x-data="{ show: $wire.entangle('loadingData') }" :class="{ 'pe-none opacity-25': show === true }">
                    <form wire:submit.debounce.1000ms='filter' x-data="{ show: $wire.entangle('loadingData') }"
                        :class="{ 'pe-none opacity-25': show === true }">
                        <div class=" row">
                            <div class="col">

                                <div class="mb-3">
                                    <label for="" class="form-label">Indicators</label>
                                    <select
                                        class="form-select @error('selectedIndicator')
                                            is-invalid
                                        @enderror"
                                        wire:model.live.debounce.600ms="selectedIndicator" wire:loading.attr='disabled'
                                        wire:loading.class='opacity-25'
                                        wire:target='selectedOrganisation, selectedIndicator, selectedDisaggregation,selectedCrop'>
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

                            @hasanyrole('admin|manager')
                                <div class="col">

                                    <div class="mb-3">
                                        <label for="" class="form-label">Organisation</label>
                                        <select
                                            class="form-select @error('selectedOrganisation')
                                            is-invalid
                                        @enderror"
                                            name="" id=""
                                            wire:model.live.debounce.600ms="selectedOrganisation"
                                            wire:loading.attr='disabled' wire:loading.class='opacity-25'
                                            wire:target='selectedOrganisation, selectedIndicator, selectedDisaggregation,selectedCrop'>
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
                            @endhasanyrole

                            <div class="col">

                                <div class="mb-3">
                                    <label for="" class="form-label">Disaggregations</label>
                                    <select
                                        class="form-select @error('selectedDisaggregation')
                                            is-invalid
                                        @enderror"
                                        wire:model.live.debounce.600ms="selectedDisaggregation"
                                        wire:loading.attr='disabled' wire:loading.class='opacity-25'
                                        wire:target='selectedOrganisation, selectedIndicator, selectedDisaggregation,selectedCrop'
                                        @if (!$selectedIndicator) disabled @endif>
                                        <option value="">Select one</option>
                                        @foreach ($disaggregations->unique('name') as $dsg)
                                            <option value="{{ $dsg->name }}">{{ $dsg->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('selectedDisaggregation')
                                    <x-error class="mb-1">{{ $message }}</x-error>
                                @enderror

                            </div>


                            <div class="col" x-data="{ reportingPeriod: $wire.entangle('selectedReportingPeriod') }">
                                <div class="mb-1">
                                    <label for="" class="form-label">Reporting Period</label>
                                    {{--
                                    <x-flatpickr x-model="starting_period" /> --}}


                                    <select
                                        class="form-select @error('selectedReportingPeriod')
                                            is-invalid
                                        @enderror"
                                        name="" id="" x-model="reportingPeriod"
                                        wire:loading.attr='disabled' wire:target='selectedProject'>
                                        <option value="">Select one</option>
                                        @foreach ($reportingPeriod as $month)
                                            @if ($month->type === 'UNSPECIFIED')
                                                <option value="{{ $month->id }}">{{ $month->type }}
                                                </option>
                                            @else
                                                <option value="{{ $month->id }}">{{ $month->start_month }}
                                                    -
                                                    {{ $month->end_month }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>


                                </div>
                                @error('selectedReportingPeriod')
                                    <x-error class="mb-1">{{ $message }}</x-error>
                                @enderror
                            </div>
                            <div class="col" x-data="{ financial_year: $wire.entangle('selectedFinancialYear') }">
                                <div class="mb-1">
                                    <label for="" class="form-label">Project year</label>
                                    {{--
                                    <x-flatpickr x-model="ending_period" /> --}}

                                    <select
                                        class="form-select @error('selectedFinancialYear')
                                            is-invalid
                                        @enderror"
                                        x-model="financial_year" wire:loading.attr='disabled'
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

                            <div class="col">

                                <div class="mb-3">
                                    <label for="" class="form-label">Enterprise</label>
                                    <select
                                        class="form-select @error('selectedCrop')
                                            is-invalid
                                        @enderror"
                                        wire:model.live.debounce.600ms="selectedCrop" wire:loading.attr='disabled'
                                        wire:loading.class='opacity-25'
                                        wire:target='selectedOrganisation, selectedIndicator, selectedDisaggregation,selectedCrop'>

                                        @foreach ($crops as $crop)
                                            @if ($crop === null)
                                                <option value="">All crops
                                                </option>
                                            @else
                                                <option value="{{ $crop }}">{{ $crop }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                @error('selectedCrop')
                                    <x-error class="mb-1">{{ $message }}</x-error>
                                @enderror

                            </div>
                        </div>
                        <div class="mt-3 row align-items-end">

                            <div class="col">
                                <div class="mb-1 d-flex justify-content-start" x-data>
                                    <button type="submit"
                                        class="btn btn-warning @if ($loadingData) disabled @endif me-2">
                                        <i class="bx bx-filter"></i> Filter Data
                                    </button>
                                    <button class="btn btn-secondary @if ($loadingData) disabled @endif"
                                        @click="$wire.dispatch('reset-filters')">Reset</button>
                                </div>

                            </div>

                            @hasanyrole('admin|manager')
                                <div class="col">
                                    <div class="mb-1 d-flex justify-content-end">
                                        <div class="text-end">
                                            <button type="button"
                                                class="btn btn-secondary  @if ($loadingData) disabled @endif "
                                                wire:click='load' wire:loading.attr='disabled'>
                                                <i class="bx bx-refresh"></i> Update Data
                                            </button> <br>

                                        </div>

                                    </div>
                                </div>
                            @endhasanyrole

                        </div>




                    </form>
                </div>
                <hr>

                @if ($loadingData)
                    <div class="p-2 my-2 row">
                        <div class="col-12 d-flex justify-content-center align-items-center">
                            <span class="fw-bold me-2">Updating data... Please wait</span>
                            <div wire:poll.5000ms='readCache'
                                class="d-flex justify-content-center align-items-center">
                                <div class="spinner-border text-warning spinner-border-sm" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif




                <div x-data="{ show: $wire.entangle('loadingData') }" :class="{ 'pe-none opacity-25': show === true }">
                    <livewire:tables.rtc-market.report-table />
                </div>








            </div>
        </div>

    </div>











</div>
