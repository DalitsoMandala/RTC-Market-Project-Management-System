<div>
    <div class="container-fluid">


        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <div class="card bg-info">
                    <div class="card-body">
                        <div class="py-3 text-center">
                            <ul class="bg-bubbles ps-0">
                                <li><i class="bx bx-grid-alt font-size-24"></i></li>
                                <li><i class="bx bx-tachometer font-size-24"></i></li>
                                <li><i class="bx bx-store font-size-24"></i></li>
                                <li><i class="bx bx-cube font-size-24"></i></li>
                                <li><i class="bx bx-cylinder font-size-24"></i></li>
                                <li><i class="bx bx-command font-size-24"></i></li>
                                <li><i class="bx bx-hourglass font-size-24"></i></li>
                                <li><i class="bx bx-pie-chart-alt font-size-24"></i></li>
                                <li><i class="bx bx-coffee font-size-24"></i></li>
                                <li><i class="bx bx-polygon font-size-24"></i></li>
                            </ul>
                            <div class="main-wid position-relative">


                                <h3 class="mb-0 text-white"> Welcome Back, {{ auth()->user()->name }}!</h3>
                                <h4 class="text-white">({{ auth()->user()->organisation->name }})</h4>
                                <p class="px-4 mt-4 text-white-50"> Explore, engage, and make the most of your
                                    experience. We're thrilled to have you on board!"</p>

                                <div class="pt-2 mt-4 mb-2">
                                    <a href="#" class="btn btn-primary">View Profile <i
                                            class="mdi mdi-arrow-right ms-1"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row my-5 @if ($showContent) d-none @endif" x-data x-init="setTimeout(() => {
            $wire.loadData()
        }, 3000);">
            <div class="col-12">
                <div class="d-flex justify-content-center align-items-center">
                    <div class="spinner-border text-primary spinner-border-lg" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <small class="mx-2 text-muted">Please wait...</small>
                </div>

            </div>


        </div>


        @if ($showContent)
            @if ($openSubmissions > 0)
                <div class="row">
                    <div class="col">

                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                            <strong>Submission are open!</strong> Please submit your data/reports before the closing
                            dates. <a href="/external/submission-periods" class="alert-link">Click Here</a>
                        </div>



                    </div>
                </div>
            @endif
            <div class="row animate__animated  animate__fadeIn">

                <div class="col-12">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4>Summary</h4>
                        </div>
                        <div class="col-2 d-none">
                            <div class="form-group">

                                <select class="form-control" wire:model.live.debounce.lazy='project'>

                                    @foreach ($projects as $prj)
                                        <option value="{{ $prj->id }}">{{ $prj->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 row">
                        <div class="col-12 col-md-8 d-flex align-items-stretch ">
                            <div class="card w-100">
                                <div class="card-header bg-info text-white py-3 ">
                                    {{ $overviewIndicator->indicator_name }}
                                </div>
                                <div class="card-body">
                                    <div class="row ">
                                        <div class="col-12 col-sm-6" x-data="{
                                            chartData: @js($data['actors']),
                                            categories: ['Farmers', 'Processors', 'Traders'],
                                            values: [],
                                            init() {
                                                let data = this.chartData;
                                                this.values = [data['Farmers'], data['Processors'], data['Traders']];



                                                options = {
                                                    chart: {
                                                        type: 'pie',


                                                    },
                                                    labels: this.categories,
                                                    series: this.values,
                                                    colors: ['#006989', '#E88D67', '#FA7070'],
                                                    legend: {
                                                        position: 'top'
                                                    }
                                                }

                                                let chart = new ApexCharts($refs.chart, options);
                                                chart.render();
                                            }
                                        }">

                                            <div
                                                x-show="values.every(value => value === 0) || values.every(value => value === undefined)">
                                                <div>
                                                    <div class="alert alert-info alert-dismissible fade show px-4 mb-0 text-center"
                                                        role="alert">
                                                        <i
                                                            class="mdi mdi-alert-circle-outline d-block display-4 mt-2 mb-3 text-info"></i>
                                                        <h5 class="text-info">Info</h5>
                                                        <p>Data not available at the moment.</p>

                                                    </div>
                                                </div>
                                            </div>
                                            <div x-ref="chart"
                                                x-show="values.every(value => value === 0) || values.every(value => value === undefined)">
                                            </div>
                                        </div>

                                        <div class=" col-12 col-sm-6" x-data="{
                                            chartData: @js($data['actors']),
                                            categories: ['Cassava', 'Potato', 'Sweet potato'],
                                            values: [],
                                            init() {
                                                let data = this.chartData;
                                                this.values = [data['Cassava'], data['Potato'], data['Sweet potato']];
                                                options = {
                                                    chart: {
                                                        type: 'donut',

                                                    },
                                                    labels: this.categories,
                                                    series: this.values,
                                                    colors: ['#006989', '#E88D67', '#FA7070'],
                                                    legend: {
                                                        position: 'top'
                                                    }
                                                }

                                                let chart = new ApexCharts($refs.chart, options);
                                                chart.render();
                                            }
                                        }">
                                            <div
                                                x-show="values.every(value => value === 0) || values.every(value => value === undefined)">
                                                <div>
                                                    <div class="alert alert-info alert-dismissible fade show px-4 mb-0 text-center "
                                                        role="alert">
                                                        <i
                                                            class="mdi mdi-alert-circle-outline d-block display-4 mt-2 mb-3 text-info"></i>
                                                        <h5 class="text-info">Info</h5>
                                                        <p>Data not available at the moment.</p>

                                                    </div>
                                                </div>
                                            </div>
                                            <div x-show="values.some(value => value !== 0 && value !== undefined)"
                                                x-ref="chart"></div>
                                        </div>
                                    </div>
                                </div>


                            </div>


                        </div>

                        <div class="col-12 col-md-4">
                            <div class="row">
                                <div class="col">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm flex-shrink-0">
                                                    <span
                                                        class="avatar-title bg-light text-primary rounded-circle shadow fs-3">
                                                        <i class="bx bx-time"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">
                                                        Pending Submissions</p>
                                                    <h4 class=" mb-0">{{ $pending }}</span></h4>
                                                </div>

                                            </div>
                                        </div><!-- end card body -->
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm flex-shrink-0">
                                                    <span
                                                        class="avatar-title bg-light text-primary rounded-circle shadow fs-3">
                                                        <i class="bx bx-calendar"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <p class="text-uppercase fw-semibold fs-12 text-muted mb-1">
                                                        Today's Submissions</p>
                                                    <h4 class=" mb-0">{{ $today }}</span></h4>
                                                </div>

                                            </div>
                                        </div><!-- end card body -->
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header bg-info text-white py-3">
                                            Submissions progress
                                        </div>
                                        <div class="card-body" x-data="{
                                            init() {

                                                let chartData = @js($submissions);
                                                const months = [
                                                    'January', 'February', 'March', 'April', 'May', 'June',
                                                    'July', 'August', 'September', 'October', 'November', 'December'
                                                ];
                                                let currentYear = new Date().getFullYear();


                                                const seriesData = months.map((month, index) => {
                                                    const data = chartData.find(item => item.month === index + 1);
                                                    return data ? data.total : 0;
                                                });

                                                options = {
                                                    chart: {
                                                        type: 'area',
                                                        height: '400px'
                                                    },
                                                    series: [{
                                                        name: 'Submissions',
                                                        data: seriesData
                                                    }],
                                                    dataLabels: {
                                                        enabled: false
                                                    },
                                                    stroke: {
                                                        curve: 'smooth'
                                                    },
                                                    xaxis: {
                                                        categories: months
                                                    },
                                                    colors: ['#E88D67', '#FA7070'],
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
                        </div>
                    </div>



                </div>
            </div>

        @endif


    </div>

</div>
