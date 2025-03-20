<div>
    @section('title')
        Dashboard
    @endsection
    <div class="container-fluid">

        @include('layouts.dashboard-layout')


        <livewire:dashboard-charts />

        <div class="row">
            <div class="col-7">
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
                            console.log('Batch:', batch);
                            console.log('Manual:', manual);
                            console.log('Aggregate:', aggregate);
                    
                    
                    
                    
                    
                    
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

            <div class="col-5 d-flex align-items-stretch">
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
                                        <th scope="col">Form</th>
                                        <th scope="col">Organisation</th>
                                        <th scope="col">Submitted By</th>
                                        <th scope="col">Date Submitted</th>
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


        <div class="row">
            <div class="col-lg-12 col-md-12 d-flex align-items-stretch">
                <div class="card w-100">
                    <div class="card-header">
                        <div class="align-items-center d-flex justify-content-between">
                            <div class="">
                                <h4 class="mb-2 card-title flex-grow-1">Current Users</h4>
                                <p class="mb-1 text-muted small"> Active Users:
                                    <b class="badge bg-success-subtle text-success">{{ $topData['activeUsers'] }}</b>
                                </p>

                            </div>

                            <a class="btn btn-soft-warning" href="/admin/users" role="button"> <i
                                    class="bx bx-show"></i> View More</a>

                        </div>
                    </div>

                    <div class="p-0 overflow-scroll card-body" style="max-height: 250px;">
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

        </div>

    </div>

</div>
