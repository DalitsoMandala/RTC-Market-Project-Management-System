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


        <div class=" row">
            <div class="col-12">
                <div class="row align-items-center">
                    <div class="col">
                        <h4>Summary</h4>
                    </div>
                    <div class="col-2">
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
                    <div class="col-12 col-md-8">
                        <div class="card">
                            <div class="card-header">{{ $data['name'] }}</div>
                            <div class="row">
                                <div class="col" x-data="{
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
                                    <div x-ref="chart"></div>
                                </div>

                                <div class="border col border-left" x-data="{
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
                                    <div x-ref="chart"></div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="row d-flex justify-content-center">
                                    <div class="col">
                                        <p class="mb-1 text-dark fw-semibold">Number of indicators</p>
                                        <h4 class="my-1">{{ $indicatorCount }}</h4>
                                        <hr>
                                        <a name="" id="" class="btn btn-primary btn-sm"
                                            href="indicators" role="button">View Details <i
                                                class="bx bx-arrow-to-right"></i></a>
                                    </div>
                                    <div class="col-auto align-self-center">
                                        <div
                                            class="d-flex justify-content-center align-items-center thumb-md rounded-circle">
                                            <i class="p-1 bx bx-bar-chart-alt-2 h2"></i>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">

                                <div class="row">
                                    <div class="col">

                                        <div id="calendar"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>

            </div>
        </div>






    </div>

    @assets
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.13/index.global.min.js'></script>
    @endassets
    @script
        <script>
            let calendarEl = document.getElementById('calendar');
            let calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    start: 'title', // will normally be on the left. if RTL, will be on the right
                    center: '',
                    end: 'today prev,next'
                }
            });
            calendar.render();
        </script>
    @endscript
</div>
