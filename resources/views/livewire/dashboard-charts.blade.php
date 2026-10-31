<div>




    <div class="my-4 ">
        <div class="my-2 row align-items-center" x-data="{

            showContent: $wire.entangle('showContent'),
        }" x-show="showContent">
            <div class="col-12 col-lg-6">
                <h3 class="mb-0" >Summary</h3>
                <p class="text-muted mb-0">{{ $name }}</p>
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
                        }, 1000)
                    },

                }">

                    <div class="dropdown card-header-dropdown" :class="{ 'opacity-25 pe-none': visible === false }">

                        <a class="shadow-none dropdown-btn btn btn-warning " href="#" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">

                            <span> <i class="bx bx-filter me-5 fw-bold"></i> <span id='report_year'
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
                }, 1000)
            }">



                @include('placeholders.dashboard')
            </div>
        @else
            <livewire:charts-view :data="$data" />

            @hasanyrole('admin')
                <div class="dash-card">
                    <div class="mt-2 row">
                        <div class="col-12">
                            <div class="card">
                                <x-card-header>Submissions progress</x-card-header>
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
                                                type: 'line',
                                                height: '400px',

                                            },
                                            series: [
                                                { name: 'Batch Submission', data: batch },
                                                { name: 'Manual Submission', data: manual },
                                                { name: 'Aggregate Submission', data: aggregate },
                                            ],
                                            dataLabels: { enabled: false },
                                            stroke: { curve: 'smooth', width: 1.5 },
                                            xaxis: { categories: categories },
                                            colors: SystemColors,


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


                    <div class="row g-3 ">
                        {{-- User Table --}}
                        <div class="col-12 col-xl-6 d-flex">
                            <x-table-card title="Current Users" subtitle="Recently registered system users"
                                :url="route('admin-users')">

                                <table class="table table-hover align-middle mb-0 h-100">
                                    <thead class="table-secondary">
                                        <tr>
                                            <th class="  small text-uppercase fw-bold">
                                                Username
                                            </th>
                                            <th class="small text-uppercase fw-bold">
                                                Email Address
                                            </th>
                                            <th class=" small text-uppercase fw-bold">
                                                Organisation
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @forelse ($users as $user)
                                            <tr>
                                                <td class="ps-4 py-3">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <img src="{{ $user->image ? asset('storage/profiles/' . $user->image) : asset('assets/images/users/usr.png') }}"
                                                            class="rounded-circle object-fit-cover flex-shrink-0"
                                                            alt="{{ $user->name }}" width="32" height="32"
                                                            loading="lazy">

                                                        <span class="fw-semibold text-dark">
                                                            {{ $user->name }}
                                                        </span>
                                                    </div>
                                                </td>

                                                <td class="py-3">
                                                    @if ($user->email)
                                                        <a href="mailto:{{ $user->email }}"
                                                            class="text-decoration-none text-body">
                                                            {{ $user->email }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">Not provided</span>
                                                    @endif
                                                </td>

                                                <td class="pe-4 py-3">
                                                    {{ $user->organisation?->name ?? 'Not assigned' }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center py-5">
                                                    <x-no-data />
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                            </x-table-card>
                        </div>

                        {{-- Submissions Table --}}
                        <div class="col-12 col-xl-6 d-flex">
                            <x-table-card title="Recent Submissions" subtitle="Latest forms submitted by partners"
                                :url="route('admin-submissions')">

                                <table class="table table-hover align-middle mb-0 h-100">
                                    <thead class="table-secondary">
                                        <tr>
                                            <th class="align-middle small text-uppercase fw-bold">
                                                Form
                                            </th>
                                            <th class="align-middle small text-uppercase fw-bold">
                                                Partner
                                            </th>
                                            <th class="align-middle small text-uppercase fw-bold">
                                                User
                                            </th>
                                            <th class="align-middle small text-uppercase fw-bold text-nowrap">
                                                Date
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @forelse ($lastSubmission as $item)
                                            <tr>
                                                <td class="ps-4 py-3">
                                                    <span class="fw-semibold text-dark">
                                                        {{ \Illuminate\Support\Str::limit(ucwords(strtolower($item->form?->name ?? 'Unknown form')), 20, '...') }}
                                                    </span>
                                                </td>

                                                <td class="py-3">
                                                    {{ $item->user?->organisation?->name ?? 'Not assigned' }}
                                                </td>

                                                <td class="py-3">
                                                    {{ $item->user?->name ?? 'Unknown user' }}
                                                </td>

                                                <td class="pe-4 py-3 text-nowrap">
                                                    {{ $item->created_at?->format('d/m/Y h:i A') ?? 'N/A' }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-5">
                                                    <x-no-data />
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                            </x-table-card>
                        </div>

                    </div>


                </div>
            @endhasanyrole
        @endif


    </div>



</div>
