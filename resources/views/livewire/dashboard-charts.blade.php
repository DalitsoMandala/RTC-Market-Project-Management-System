<div>




    <div class="my-4 ">
        <div class="my-2 row align-items-center" x-data="{

            showContent: $wire.entangle('showContent'),
        }" x-show="showContent">
            <div class="col-12 col-lg-6">
                <h2 class="h2">Summary</h2>
                <p class="text-muted">{{ $name }}</p>
            </div>
            <div class="col-12 col-lg-6">

                <div class="d-flex justify-content-start justify-content-lg-end" wire:ignore x-data="{
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
                        <a class="shadow-none dropdown-btn btn btn-warning " href="#" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
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

            @hasanyrole('admin')
                <div>
                    <div class="mt-2 row">
                        <div class="col-12">
                            <div class="card">
                                <div class="py-3 border-0 card-header fw-bold">
                                    <h5>Submissions progress</h5>

                                </div>
                                <div class="card-body" x-data="{
                                    init() {
                                        let chartData = @js($submissions); // Data from backend
                                        let categories = []; // Will hold month-year labels
                                        let serieArray = {}; // { batch: [], manual: [], aggregate: [] }

                                        // Sort data by year & month to keep chart in order
                                        chartData.sort((a, b) => (a.year - b.year) || (a.month - b.month));

                                        // Create unique month-year keys
                                        chartData.forEach((item) => {
                                            const monthName = new Date(item.year, item.month - 1).toLocaleString('default', { month: 'long' });
                                            const monthYear = `${monthName} ${item.year}`;

                                            // Add category if not already in list
                                            if (!categories.includes(monthYear)) {
                                                categories.push(monthYear);
                                            }
                                        });



                                        // Initialize serieArray for each type
                                        const types = [...new Set(chartData.map(i => i.type))];
                                        types.forEach(type => {
                                            serieArray[type] = Array(categories.length).fill(0);
                                        });

                                        // Fill serieArray with totals
                                        chartData.forEach((item) => {
                                            const monthName = new Date(item.year, item.month - 1).toLocaleString('default', { month: 'long' });
                                            const monthYear = `${monthName} ${item.year}`;
                                            const index = categories.indexOf(monthYear);
                                            serieArray[item.type][index] += item.total;
                                        });

                                        // Extract data
                                        const { batch = [], manual = [], aggregate = [] } = serieArray;

                                        // ApexCharts options
                                        let options = {
                                            chart: {
                                                type: 'area',
                                                height: '400px'
                                            },
                                            series: [
                                                { name: 'Batch Submission', data: batch },
                                                { name: 'Manual Submission', data: manual },
                                                { name: 'Aggregate Submission', data: aggregate },
                                            ],
                                            dataLabels: { enabled: false },
                                            stroke: { curve: 'smooth' },
                                            xaxis: { categories: categories },
                                            colors: ['#FC931D', '#FA7070', '#DE8F5F'],
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
                                            <h4 class=" card-title flex-grow-1">Current Users</h4>


                                        </div>

                                        <a class="btn btn-warning btn-sm" href="{{ route('admin-users') }}" role="button"> View More <i
                                                class="bx bx-right-arrow-alt"></i></a>

                                    </div>
                                </div>

                                <div class="p-0 overflow-scroll card-body" style="max-height: 400px">
                                    <div class="table-responsive">
                                        <table class="table align-middle table-striped table-hover ">
                                            <thead class="table-secondary">

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
                                                        <td scope="row" class="py-4">
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
                                    <div class="align-items-center d-flex justify-content-between">
                                        <div>

                                            <h4 class=" card-title flex-grow-1">Recent Submissions</h4>

                                        </div>

                                        <a class="btn btn-warning btn-sm" href="{{ route('admin-submissions') }}" role="button"> View More <i
                                                class="bx bx-right-arrow-alt"></i></a>
                                    </div>

                                </div>
                                <div class="p-0 overflow-scroll card-body" style="max-height: 400px">
                                    <div class="table-responsive">
                                        <table class="table align-middle table-striped table-hover ">
                                            <thead class="table-secondary">
                                                <tr>
                                                    <th scope="col">Form</th>
                                                    <th scope="col">Partner</th>
                                                    <th scope="col">User</th>

                                                    <th scope="col">Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @foreach ($lastSubmission as $item)
                                                    <tr>
                                                        <td scope="row" class="py-4">
                                                            <div class="d-flex align-items-center">
                                                                <div
                                                                    class="text-white avatar-sm bg-warning rounded-circle me-1 d-flex align-items-center justify-content-center">
                                                                    <i class="bx bx-folder-plus"></i></div>
                                                                <span class="ms-2">
                                                                    {{ \Str::limit($item->form->name, 20, '...') }}</span>
                                                            </div>

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
