<div>
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Reporting</h4>

                    <div class="page-title-right">
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
        <div class="row">
            <div class="col-12">
                <div class="card ">
                    <div class="card-header">
                        <h5 class="card-title">Report Table</h5>
                    </div>

                    @role('admin')
                        <div class="card-header d-flex justify-content-between">
                            <div class="col">
                                <button type="button" class="btn btn-soft-warning btn-sm" wire:click='load'
                                    wire:target='load' wire:loading.attr='disabled'
                                    @if ($loadingData) disabled @endif>
                                    <i class="bx bx-recycle"></i> Update
                                </button> <br>
                                <span class=" text-muted" style="font-size: 10px">Please note that this might take a
                                    while depending on the
                                    number of
                                    records to be calculated.</span>
                            </div>



                        </div>
                    @endrole
                    <div class="px-0 card-body">
                        <div class="my-3 row">
                            <div class="col-md-12">


                                <ul class="mx-1 my-2 nav nav-tabs">
                                    <li class="nav-item">
                                        <a href="{{ $routePrefix }}/reports" class="nav-link active "
                                            aria-current="page">Reporting</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ $routePrefix }}/standard-targets" class="nav-link ">Standard
                                            Targets</a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="{{ $routePrefix }}/targets" class="nav-link ">Targets</a>
                                    </li>

                                </ul>

                            </div>
                        </div>
                        <form wire:submit.debounce.1000ms='filter'>
                            <div class="mx-1 row">



                                <div class="col">

                                    <div class="mb-3">
                                        <label for="" class="form-label">Organisation</label>
                                        <select
                                            class="form-select @error('selectedOrganisation')
                                            is-invalid
                                        @enderror"
                                            name="" id=""
                                            wire:model.live.debounce.600ms="selectedOrganisation">
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

                                <div class="col">

                                    <div class="mb-3">
                                        <label for="" class="form-label">Indicators</label>
                                        <select
                                            class="form-select @error('selectedIndicator')
                                            is-invalid
                                        @enderror"
                                            wire:model.live.debounce.600ms="selectedIndicator"
                                            wire:loading.attr='disabled' wire:loading.class='opacity-25'
                                            wire:target='selectedOrganisation'
                                            @if (!$selectedOrganisation) disabled @endif>
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
                                <div class="col">

                                    <div class="mb-3">
                                        <label for="" class="form-label">Disaggregations</label>
                                        <select
                                            class="form-select @error('selectedDisaggregation')
                                            is-invalid
                                        @enderror"
                                            wire:model.live.debounce.600ms="selectedDisaggregation"
                                            wire:loading.attr='disabled' wire:loading.class='opacity-25'
                                            wire:target='selectedOrganisation, selectedIndicator'
                                            @if (!$selectedOrganisation) disabled @endif>
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
                                        {{-- <x-flatpickr x-model="starting_period" /> --}}


                                        <select
                                            class="form-select @error('selectedReportingPeriod')
                                            is-invalid
                                        @enderror"
                                            name="" id="" x-model="reportingPeriod"
                                            wire:loading.attr='disabled' wire:target='selectedProject'>
                                            <option value="">Select one</option>
                                            @foreach ($reportingPeriod as $month)
                                                <option value="{{ $month->id }}">{{ $month->start_month }} -
                                                    {{ $month->end_month }}
                                                </option>
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
                                        {{-- <x-flatpickr x-model="ending_period" /> --}}

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



                                <div class="mt-3 row">

                                    <div class="mb-1 d-flex justify-content-center" x-data>
                                        <button type="submit"
                                            class="btn btn-warning @if ($loadingData) disabled @endif me-2">
                                            <i class="bx bx-filter"></i> Filter Data
                                        </button>
                                        <button
                                            class="btn btn-warning @if ($loadingData) disabled @endif"
                                            @click="$wire.dispatch('reset-filters')">Reset</button>
                                    </div>


                                </div>

                            </div>

                        </form>
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                {{-- <livewire:tables.reporting-table /> --}}


                                @if ($loadingData)
                                    <div class="p-2 my-2 roow">

                                        <div class="col-12 d-flex justify-content-center ">
                                            <span class="fw-bold me-2"> Updating data...Please wait</span>

                                            <div x-data wire:poll.5000ms='readCache()'
                                                class="d-flex justify-content-center align-items-center">
                                                <div class="spinner-border text-warning spinner-border-sm"
                                                    role="status">
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
            </div>
        </div>
    </div>











</div>
