<div>
    @section('title')
        View Household Consumption Data
    @endsection
    <div class="container-fluid">
        <style>
            td {
                color: red;
            }
        </style>
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">View Data</h4>

                    <div class="page-title-right" wire:ignore>
                        @php
                            $routePrefix = \Illuminate\Support\Facades\Route::current()->getPrefix();
                        @endphp
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ $routePrefix }}/forms">Forms</a></li>
                            <li class="breadcrumb-item active">View Data</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">


            <div class="col-12">


                <x-alerts />

                <div class="card">
                    <div class="card-header">
                        <h4 class="text-center text-warning text-uppercase">Household Consumption Table @if ($batch_no)
                                [Batch : {{ $batch_no }}]
                            @endif
                        </h4>
                    </div>

                    <div class="px-0 card-body">

                        <livewire:tables.rtc-market.household-rtc-consumption-table />

                        {{-- @if ($loadingData)
                        <div wire:poll.keep-alive.5s='checkJobStatus()' class="d-flex justify-content-center align-items-center">
                            <div class="spinner-border text-warning spinner-border-lg" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>

                        </div>
                        @endif --}}


                        {{-- <div class="pb-5 table-responsive col-md-12" style="margin: 10px 0 10px;" wire:ignore
                            x-data="{ show: $wire.entangle('loadingData') }"
                            :class="{ 'pe-none opacity-25': show === true }">
                            <table class="table align-middle table-striped nowrap w-100" id="hrc">
                                <thead class="table-primary text-uppercase text-secondary">
                                    <tr style="font-size: 12px;color:#6b6a6a;">
                                        <th scope="col">
                                            Id</th>
                                        <th scope="col">Enterprise</th>
                                        <th scope="col">District</th>
                                        <th scope="col">EPA</th>
                                        <th scope="col">Section</th>
                                        <th scope="col">Date of assessment</th>
                                        <th scope="col">Actor type</th>
                                        <th scope="col">Rtc group platform</th>
                                        <th scope="col">Producer organisation</th>
                                        <th scope="col">Actor name</th>
                                        <th scope="col">Age group</th>
                                        <th scope="col">Sex</th>
                                        <th scope="col">Phone number</th>
                                        <th scope="col">Household size</th>
                                        <th scope="col">Under 5 in household</th>
                                        <th scope="col">Rtc consumers</th>
                                        <th scope="col">Rtc consumers/Potato</th>
                                        <th scope="col">Rtc consumers/Sweet Potato</th>
                                        <th scope="col">Rtc consumers/Cassava</th>
                                        <th scope="col">Rtc consumption frequency</th>
                                        <th scope="col">RTC MAIN FOOD/CASSAVA</th>
                                        <th scope="col">RTC MAIN FOOD/POTATO</th>
                                        <th scope="col">RTC MAIN FOOD/SWEET POTATO</th>
                                        <th scope="col">Submission Date</th>
                                        <th scope="col">Submitted By</th>
                                        <th scope="col">UUID</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 11px" class="animate__animated animate__fadeIn">

                                </tbody>
                            </table>


                        </div> --}}


                    </div>
                </div>

            </div>
        </div>




    </div>



</div>


@script
    <script>
        $('#hrc').DataTable();





        // $wire.on('loaded-data', (e) => {
        //     clearInterval(intervalId);
        //     loadData(e.data);
        // });



        function loadData(data) {
            if ($.fn.DataTable.isDataTable('#hrc')) {
                $('#hrc').DataTable().clear().destroy();
            }

            populateTable(data);

            //             // Initialize or reinitialize DataTable

            let today = new Date();
            let dd = String(today.getDate()).padStart(2, '0');
            let mm = String(today.getMonth() + 1).padStart(2, '0'); // January is 0!
            let yyyy = today.getFullYear();
            today = mm + '_' + dd + '_' + yyyy;
            $('#hrc').DataTable({
                // Your DataTable options here
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel" data-bs-toggle="tooltip" data-bs-placement="top" title="Export"></i>',
                    titleAttr: 'Excel',
                    title: 'Household consumption ' + today,
                    className: 'bg-warning'
                }],
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                responsive: true,

            });

        }

        function populateTable(data) {
            let tbody = $('#hrc tbody');
            tbody.empty(); // Clear any existing data

            data.forEach(function(row) {
                let tr = $('<tr>');
                tr.append($('<td>').text(row.id));
                tr.append($('<td>').text(row.enterprise));
                tr.append($('<td>').text(row.district));
                tr.append($('<td>').text(row.epa));
                tr.append($('<td>').text(row.section));
                tr.append($('<td>').text(row.date_of_assessment_formatted));
                tr.append($('<td>').text(row.actor_type));
                tr.append($('<td>').text(row.rtc_group_platform));
                tr.append($('<td>').text(row.producer_organisation));
                tr.append($('<td>').text(row.actor_name));
                tr.append($('<td>').text(row.age_group));
                tr.append($('<td>').text(row.sex));
                tr.append($('<td>').text(row.phone_number));
                tr.append($('<td>').text(row.household_size));
                tr.append($('<td>').text(row.under_5_in_household));
                tr.append($('<td>').text(row.rtc_consumers));
                tr.append($('<td>').text(row.potato_count));
                tr.append($('<td>').text(row.rtc_consumers_sw_potato));
                tr.append($('<td>').text(row.rtc_consumers_cassava));
                tr.append($('<td>').text(row.rtc_consumption_frequency));
                tr.append($('<td>').text(row.cassava_count));
                tr.append($('<td>').text(row.potato_count));
                tr.append($('<td>').text(row.sweet_potato_count));
                tr.append($('<td>').text(row.submission_date));
                tr.append($('<td>').text(row.submitted_by));
                tr.append($('<td>').text(row.uuid));
                tbody.append(tr);
            });
        }
    </script>
@endscript
