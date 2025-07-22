<div>




    <div class="my-4 ">
        <div class="my-2 row align-items-center" x-data="{

            showContent: $wire.entangle('showContent'),
        }" x-show="showContent">
            <div class="col">
                <h2 class="h2">Summary</h2>
                <p class="text-muted">{{ $name }}</p>
            </div>
            <div class="col ">

                <div class="d-flex justify-content-end" wire:ignore x-data="{
visible: true,
                    selectedReportYear: $wire.entangle('selectedReportYear'),
                    financialYears: $wire.entangle('financialYears'),

                    changeYear(data) {
                      this.visible = false;
                        $wire.dispatch('updateReportYear', {
                            id: data.id,
                        });





                            setTimeout(() => {
                                this.visible = true
                            }, 5000)
                    },

                }">
                    <div class="dropdown card-header-dropdown" :class="{ 'opacity-25 pe-none': visible === false }">
                        <a class="shadow-none dropdown-btn btn btn-warning " href="#"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bx bx-filter me-5 fw-bold"></i> <span> <span id='report_year'
                                    x-text="'Year ' + selectedReportYear"></span> <i
                                    class="mdi mdi-chevron-down ms-1"></i></span>

                        </a>
                        <div class="dropdown-menu dropdown-menu-end" style="">
                            <template x-for="(value, index) in financialYears" :key="value.id">
                                <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                    x-on:click="changeYear(value)"
                                    :class="{
                                        'disabled': value.number === selectedReportYear
                                    }"
                                    x-text="'Year ' + value.number"></a>
                            </template>

                        </div>
                    </div>

                </div>

            </div>
        </div>





        @if (!$showContent)
            <div x-data x-init="() => {
                setTimeout(() => {

                    $wire.dispatch('showCharts');
                }, 5000)
            }">



                @include('placeholders.dashboard')
            </div>
        @else
            <livewire:charts-view :data="$data" />

            @hasanyrole('admin|manager|project_manager|enumarator')
                <div>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="py-3 border-0 card-header fw-bold">
                                    <h5>Submissions progress</h5>
                                    <span class="text-muted small">
                                        Monthly submissions progress based on different types of forms.
                                    </span>
                                </div>
                                <div class="card-body" x-data="{
                                    init() {

                                        let chartData = @js($submissions); // Data from the backend
                                        const months = [
                                            'January', 'February', 'March', 'April', 'May', 'June',
                                            'July', 'August', 'September', 'October', 'November', 'December'
                                        ];
                                        let seriesData = [];
                                        const serieArray = {};

                                        // Process chartData
                                        chartData.forEach((item) => {
                                            // Initialize the type array if not already initialized
                                            if (!serieArray[item.type]) {
                                                serieArray[item.type] = Array(months.length).fill(0);
                                            }

                                            // Find the correct index for the month (1-indexed to 0-indexed)
                                            const monthIndex = item.month - 1;

                                            // Update the total for the specific type and month
                                            if (monthIndex >= 0 && monthIndex < months.length) {
                                                serieArray[item.type][monthIndex] += item.total; // Sum totals for the same type and month
                                            }
                                        });

                                        // Destructure series data
                                        const { batch = [], manual = [], aggregate = [] } = serieArray;

                                        // Output the series data for debugging






                                        options = {
                                            chart: {
                                                type: 'area',
                                                height: '400px'
                                            },
                                            series: [{
                                                    name: 'Batch Submission',
                                                    data: batch
                                                },
                                                {
                                                    name: 'Manual Submission',
                                                    data: manual
                                                },
                                                {
                                                    name: 'Aggregate Submission',
                                                    data: aggregate
                                                },

                                            ],
                                            dataLabels: {
                                                enabled: false
                                            },
                                            stroke: {
                                                curve: 'smooth'
                                            },
                                            xaxis: {
                                                categories: months
                                            },
                                            colors: ['#FC931D', '#FA7070', '#DE8F5F'],
                                            tooltip: {
                                                x: {
                                                    format: 'dd/MM/yy'
                                                },
                                            },
                                        };

                                        let chart = new ApexCharts($refs.chart, options);
                                        chart.render();
                                    }
                                }">

                                    <div x-ref="chart"></div>
                                </div>
                            </div>
                        </div>


                    </div>


                    <div class="row">
                        <div class="col-12 col-xl-7 d-flex align-items-stretch">
                            <div class="card w-100">
                                <div class="card-header">
                                    <div class="align-items-center d-flex justify-content-between">
                                        <div class="">
                                            <h4 class="mb-2 card-title flex-grow-1">Current Users</h4>
                                            <p class="mb-1 text-muted small"> List of active users in the system

                                            </p>

                                        </div>

                                        <a class="btn btn-warning" href="/admin/users" role="button"> View More <i
                                                class="bx bx-right-arrow-alt"></i></a>

                                    </div>
                                </div>

                                <div class="p-0 overflow-scroll card-body">
                                    <div class="table-responsive">
                                        <table class="table align-middle table-striped table-hover table-borderless">
                                            <thead class="table-warning">

                                                <tr>
                                                    <th>Username</th>
                                                    <th>Email</th>
                                                    <th>Phone Number</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @foreach ($users as $user)
                                                    <tr class="">
                                                        <td scope="row">
                                                            <div class="d-flex align-items-center">
                                                                <img src="{{ $user->image == null ? asset('assets/images/users/usr.png') : asset('storage/profiles/' . $user->image) }}"
                                                                    class="avatar-sm rounded-circle " alt="...">
                                                                <span class="ms-2">{{ $user->name }}</span>
                                                            </div>

                                                        </td>
                                                        <td>{{ $user->email }}</td>
                                                        <td>{{ $user->phone_number }}</td>
                                                        <td>{!! $user->deleted_at == null
                                                            ? '<span class="badge bg-success">Active</span>'
                                                            : '<span class="badge bg-danger">Inactive</span>' !!}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                @if (count($users) == 0)
                                                    <tr>
                                                        <td colspan="4">
                                                            <x-no-data />
                                                        </td>
                                                    </tr>
                                                @endif

                                            </tbody>

                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-xl-5 d-flex align-items-stretch">
                            <div class="card w-100">
                                <div class="card-header fw-bold">
                                    <h5>Recent Submissions</h5>
                                    <span class="text-muted small">
                                        Latest submissions with details.
                                    </span>
                                </div>
                                <div class="p-0 card-body">
                                    <div class="table-responsive">
                                        <table class="table align-middle table-striped table-hover table-borderless">
                                            <thead class="table-warning">
                                                <tr>
                                                    <th scope="col">User</th>
                                                    <th scope="col">Form</th>
                                                    <th scope="col">Organisation</th>

                                                    <th scope="col">Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @foreach ($lastSubmission as $item)
                                                    <tr>
                                                        <td scope="row">
                                                            {{ $item->form->name }}
                                                        </td>
                                                        <td>{{ $item->user->organisation->name }}</td>
                                                        <td>{{ $item->user->name }}</td>
                                                        <td>{{ $item->created_at->format('d/m/Y') }}</td>

                                                    </tr>
                                                @endforeach

                                                @if (count($lastSubmission) == 0)
                                                    <tr>
                                                        <td colspan="4">
                                                            <x-no-data />
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>


                                </div>



                            </div>
                        </div>
                    </div>
                </div>
            @endhasanyrole
        @endif


    </div>



</div>
