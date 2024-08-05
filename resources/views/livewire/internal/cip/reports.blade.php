<div>
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Reporting</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
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
                    <div class="card-body">

                        <form wire:submit.debounce.500ms='filter'>
                            <div class="row">


                                <div class="col-3">
                                    <div class="mb-1" wire:ignore x-data="{
                                        selected: null,
                                        myInput(data) {
                                            this.selected = data;
                                            setTimeout(() => {
                                                $wire.set('selectedProject', this.selected)
                                            }, 600)
                                        },
                                    }" x-init=" const input = $refs.selectElement;
                                     const selectInput = new Choices($refs.selectElement, {
                                         shouldSort: false,
                                         placeholder: true,
                                    
                                         choices: @js($projects->map(fn($option) => ['value' => $option->id, 'label' => $option->name])) // Adjust as per your model fields
                                     });
                                    
                                    
                                    
                                     input.addEventListener(
                                         'change',
                                         function(event) {
                                    
                                             myInput(event.detail.value);
                                    
                                    
                                    
                                         },
                                         false,
                                     );
                                     $wire.on('reset-filters', () => {
                                    
                                    
                                         selectInput.removeActiveItems(); // Clear the selected item
                                         selectInput.setChoiceByValue('');
                                    
                                     })">
                                        <label for="" class="form-label">Project</label>
                                        <select class="form-select form-select-sm " x-ref="selectElement">
                                            <option value="" disabled selected>Choose an option</option>
                                        </select>


                                    </div>
                                    @error('selectedProject')
                                        <x-error class="mb-1">{{ $message }}</x-error>
                                    @enderror
                                </div>
                                <div class="col-9">


                                    <div class="mb-1" wire:ignore x-data="{
                                        selected: $wire.entangle('selectedIndicators'),
                                        myInput(data) {
                                            this.selected = data;
                                        },
                                    
                                    }" x-init=" const input = $refs.selectElementIndicator;
                                     const selectInput = new Choices($refs.selectElementIndicator, {
                                         shouldSort: false,
                                         removeItemButton: true,
                                         placeholder: true,
                                         placeholderValue: 'Select indicators here...',
                                         choices: @js($indicators->map(fn($option) => ['value' => $option->id, 'label' => '(' . $option->indicator_no . ') ' . $option->indicator_name])) // Adjust as per your model fields
                                     });
                                    
                                     input.addEventListener(
                                         'change',
                                         function(event) {
                                    
                                    
                                             let selectedValues = selectInput.getValue(true);
                                    
                                    
                                             myInput(selectedValues);
                                    
                                    
                                         },
                                         false,
                                     );
                                     $wire.on('reset-filters', () => {
                                    
                                    
                                         selectInput.removeActiveItems(); // Clear the selected item
                                    
                                    
                                     })">


                                        <label for="" class="form-label">Indicator</label>
                                        <select class="form-select form-select-md" multiple
                                            x-ref="selectElementIndicator" id="selectElementIndicator">

                                        </select>


                                    </div>
                                    @error('selectedIndicators')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror

                                </div>
                                <div class="col-3" x-data="{ reportingPeriod: $wire.entangle('selectedReportingPeriod') }">
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
                                                    {{ $month->end_month }}</option>
                                            @endforeach
                                        </select>


                                    </div>
                                    @error('selectedReportingPeriod')
                                        <x-error class="mb-1">{{ $message }}</x-error>
                                    @enderror
                                </div>
                                <div class="col-3" x-data="{ financial_year: $wire.entangle('selectedFinancialYear') }">
                                    <div class="mb-1">
                                        <label for="" class="form-label">Financial year</label>
                                        {{-- <x-flatpickr x-model="ending_period" /> --}}

                                        <select
                                            class="form-select @error('selectedFinancialYear')
                                            is-invalid
                                        @enderror"
                                            name="" id="" x-model="financial_year"
                                            wire:loading.attr='disabled' wire:target='selectedProject'>
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
                                <div class="col-3 align-self-end">
                                    <div class="mb-1" x-data>
                                        <button type="submit"
                                            class="btn btn-primary @if ($loadingData) disabled @endif">
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
                                    <div x-data wire:poll.2000ms='readCache()'
                                        class="d-flex justify-content-center align-items-center">
                                        <div class="spinner-border text-primary spinner-border-lg" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                @endif
                                <div class="table-responsive" wire:ignore x-data="{ show: $wire.entangle('loadingData') }"
                                    :class="{ 'pe-none opacity-25': show === true }">
                                    <table class="table table-striped table-bordered " id="reports">
                                        <thead class="text-uppercase table-primary">
                                            <tr style="font-size: 12px;" class="text-uppercase">
                                                <th scope="col" style="color: #6b6a6a;">ID</th>
                                                <th scope="col" style="color: #6b6a6a;">Dissagregation</th>
                                                <th scope="col" style="color: #6b6a6a;">Value</th>
                                                <th style="color: #6b6a6a;">Indicator Name</th>
                                                <th style="color: #6b6a6a;">Project</th>
                                                <th style="color: #6b6a6a;">Reporting Period</th>
                                                <th style="color: #6b6a6a;">Project Year</th>
                                            </tr>
                                        </thead>
                                        <tbody style="font-size: 12px">

                                        </tbody>
                                    </table>
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

            $wire.load();


            $('#reports').DataTable();
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
                    text: '<i class="fas fa-file-excel"></i> Export to Excel',
                    titleAttr: 'Excel',
                    title: 'Report ' + today,
                    className: 'bg-primary btn-sm'
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
