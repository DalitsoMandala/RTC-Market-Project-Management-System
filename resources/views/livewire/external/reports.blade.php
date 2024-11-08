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
                            <li class="breadcrumb-item active">Reports</li>
                        </ol>
                    </div>

                </div>
            </div>

        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <div class="card ">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-12">
                                <ul class="nav nav-tabs">
                                    <li class="nav-item">
                                        <a href="{{ $routePrefix }}/reports" class="nav-link active"
                                            aria-current="page">Reporting</a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="{{ $routePrefix }}/targets" class="nav-link">Targets</a>
                                    </li>

                                </ul>

                            </div>
                        </div>
                    </div>

                    @role('admin')
                        <div class="card-header d-flex justify-content-between">
                            <div class="col">
                                <button type="button" class="btn btn-success btn-sm" wire:click='load'
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
                    <div class="card-body">

                        <form wire:submit.debounce.500ms='filter'>
                            <div class="row">



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



                                <div class="row mt-3">

                                    <div class="mb-1 d-flex  justify-content-center" x-data>
                                        <button type="submit"
                                            class="btn btn-primary @if ($loadingData) disabled @endif me-2">
                                            <i class="bx bx-filter"></i> Filter Data
                                        </button>
                                        <button
                                            class="btn btn-primary @if ($loadingData) disabled @endif"
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
                                    <div class="row border  rounded-2 p-2 my-2">
                                        <div class="col-9">
                                            <div class="progress my-2">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated"
                                                    role="progressbar" style="width: {{ $progress }}%;"
                                                    aria-valuenow="{{ $progress }}" aria-valuemin="0"
                                                    aria-valuemax="100">

                                                </div>


                                            </div>
                                        </div>
                                        <div class="col-3 d-flex ">
                                            <span class="text-primary fw-bold me-2"> {{ $progress }}%</span>

                                            <div x-data wire:poll.5000ms='readCache()'
                                                class="d-flex justify-content-center align-items-center">
                                                <div class="spinner-border text-primary spinner-border-sm"
                                                    role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif


                                <!-- <div class="table-responsive" wire:ignore x-data="{ show: $wire.entangle('loadingData') }"
                                    :class="{ 'pe-none opacity-25': show === true }">
                                    <table class="table table-striped table-bordered " id="reports">
                                        <thead class="text-uppercase table-primary">
                                            <tr style="font-size: 12px;" class="text-uppercase">
                                                <th scope="col" style="color: #6b6a6a;">ID</th>
                                                <th scope="col" style="color: #6b6a6a;">Dissagregation</th>
                                                <th scope="col" style="color: #6b6a6a;">Value</th>
                                                <th style="color: #6b6a6a;">Indicator Name</th>
                                                <th style="color: #6b6a6a;">Indicator Number</th>
                                                <th style="color: #6b6a6a;">Project</th>
                                                <th style="color: #6b6a6a;">Reporting Period</th>
                                                <th style="color: #6b6a6a;">Project Year</th>
                                            </tr>
                                        </thead>
                                        <tbody style="font-size: 12px">

                                        </tbody>
                                    </table>
                                </div>
 -->







                                <div x-data="{ show: $wire.entangle('loadingData') }" :class="{ 'pe-none opacity-25': show === true }">
                                    <livewire:tables.rtcmarket.report-table />
                                </div>







                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>







</div>


@script
    <script>
        Alpine.store('loadData', {
            indicators: @json($indicators),

        });

        $(document).ready(function() {

            //  $wire.load();


            // $('#reports').DataTable();
        });
        $wire.on('loaded-data', (e) => {


            loadData(e.data);
        });

        function loadData(data) {
            if ($.fn.DataTable.isDataTable('#reports')) {
                $('#reports').DataTable().clear().destroy();
            }
            let tbody = $('#reports tbody');
            tbody.empty(); // Clear any existing data

            data.forEach(function(row) {
                let tr = $('<tr>');
                tr.append($('<td>').text(row.id));
                tr.append($('<td>').text(row.name));
                tr.append($('<td>').text(row.value));
                tr.append($('<td>').text(row.indicator_name));
                tr.append($('<td>').text(row.number));
                tr.append($('<td>').text(row.project));
                tr.append($('<td>').text(row.reporting_period));
                tr.append($('<td>').text(row.financial_year));
                tbody.append(tr);
            });

            //             // Initialize or reinitialize DataTable

            let today = new Date();
            let dd = String(today.getDate()).padStart(2, '0');
            let mm = String(today.getMonth() + 1).padStart(2, '0'); // January is 0!
            let yyyy = today.getFullYear();
            today = mm + '_' + dd + '_' + yyyy;
            $('#reports').DataTable({
                // Your DataTable options here
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i> Export',
                    titleAttr: 'Excel',
                    title: 'Report ' + today,
                    className: 'btn btn-soft-dark waves-effect waves-light'
                }],
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                scroller: true
            });

        }
    </script>
@endscript


</div>
