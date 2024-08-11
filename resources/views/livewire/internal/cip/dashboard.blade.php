<div>
    <div class="container-fluid">


        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <div class="card bg-primary">
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
                                    <a href="/profile" class="btn btn-light">View Profile <i
                                            class="mdi mdi-arrow-right ms-1"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row @if ($showContent) d-none @endif" x-data x-init="$wire.loadData()">
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
                        <div class="col-12 col-md-8 ">
                            <div class="card">
                                <div class="card-header bg-primary-subtle  text-dark py-3 ">
                                    {{ $data['name'] }}
                                </div>
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
                                        <div x-ref="chart"></div>
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
                                        <div x-ref="chart"></div>
                                    </div>
                                </div>

                            </div>


                        </div>
                        <div class="col-12 col-md-4 d-flex align-items-stretch">


                            <div class="card w-100">
                                <div class="card-header bg-primary-subtle   py-3 ">

                                    Calendar

                                </div>
                                <div class="card-body">

                                    <div class="row" x-data x-init="() => {
                                    
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
                                    }">
                                        <div class="col">

                                            <div id="calendar"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>


                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-primary-subtle   py-3">
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

            <div class="row">
                <div class="col-7">
                    <div class="card">
                        <div class="card-header bg-primary-subtle   py-3">
                            Recent attendance register
                        </div>
                        <div class="p-0 card-body">
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead class="table-light">

                                        <tr>
                                            <th scope="col">Name of participant</th>
                                            <th scope="col">Registration Date</th>
                                            <th>Meeting Title</th>
                                            <th scope="col">Type of Meeting</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($attendance as $people)
                                            <tr class="">
                                                <td scope="row">
                                                    <div class="d-flex">
                                                        <img src="{{ asset('assets/images/users/usr.png') }}"
                                                            alt="" class="shadow avatar-sm rounded-circle me-2">
                                                        <span class="text-capitalize"> {{ $people->name }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($people->created_at)->format('d/m/Y') }}
                                                </td>
                                                <td>
                                                    {{ $people->meetingTitle }}
                                                </td>
                                                <td>
                                                    {{ $people->meetingCategory }}
                                                </td>

                                            </tr>
                                        @endforeach

                                        @if (count($attendance) === 0)
                                            <tr>
                                                <td colspan="4">
                                                    <div class="alert alert-light" role="alert">No data available!
                                                    </div>
                                                </td>

                                            </tr>
                                            <tr>
                                                <td colspan="4"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4"></td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>


                        </div>
                    </div>
                </div>
                <div class="col-5">



                    <div class="list-group list-group-fill-success">
                        <a href="#" class="list-group-item list-group-item-action  pe-none bg-primary-subtle "><i
                                class="ri-download-2-fill align-middle me-2"></i>Quick access to forms</a>
                        @foreach ($quickForms as $form)
                            @php
                                $form_name = str_replace(' ', '-', strtolower($form->name));
                                $project = str_replace(' ', '-', strtolower($form->project->name));
                                $link = '';

                            @endphp

                            @if ($form->name == 'REPORT FORM')
                                <div class="d-flex justify-content-between">
                                    <a class="pe-none text-muted"
                                        href="forms/{{ $project }}/{{ $form_name }}/view"
                                        class="list-group-item list-group-item-action">{{ $form->name }}</a>

                                </div>
                            @elseif($form->name == 'ATTENDANCE REGISTER')
                                <div class="d-flex justify-content-between">
                                    <a href="forms/{{ $project }}/{{ $form_name }}"
                                        class="list-group-item list-group-item-action">{{ $form->name }}</a>

                                </div>
                            @else
                                <div class="d-flex justify-content-between">
                                    <a href="forms/{{ $project }}/{{ $form_name }}/view"
                                        class="list-group-item list-group-item-action">{{ $form->name }}</a>

                                </div>
                            @endif
                        @endforeach


                    </div>



                </div>

            </div>

        @endif


    </div>


</div>
