<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-4">
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
                                    <a href="/profile" class="btn btn-success">View Profile <i
                                            class="mdi mdi-arrow-right ms-1"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-8 gy-2">
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="card">
                            <div class="card-body d-flex justify-content-between align-items-center">

                                <div>

                                    <div class="avatar-md flex-shrink-0">
                                        <span class="avatar-title bg-primary rounded-circle fs-2">
                                            <i class='bx bx-user'></i>
                                        </span>
                                    </div>

                                </div>
                                <div>
                                    <p class="text-uppercase fw-medium text-muted text-truncate ">
                                        Users</p>
                                    <div class="d-flex align-items-center mb-3 text-end text-end">
                                        <h4 class="fs-4 flex-grow-1 mb-0"><span class="counter-value"
                                                data-target="{{ $topData['users'] }}">{{ $topData['users'] }}</span>
                                        </h4>

                                    </div>



                                </div>


                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card">
                            <div class="card-body d-flex justify-content-between align-items-center">

                                <div>

                                    <div class="avatar-md flex-shrink-0">
                                        <span class="avatar-title bg-success rounded-circle fs-2">
                                            <i class='bx bx-user-check'></i>
                                        </span>
                                    </div>

                                </div>
                                <div>
                                    <p class="text-uppercase fw-medium text-muted text-truncate ">
                                        Active Users</p>
                                    <div class="d-flex align-items-center mb-3 text-end">
                                        <h4 class="fs-4 flex-grow-1 mb-0"><span class="counter-value"
                                                data-target="{{ $topData['activeUsers'] }}">{{ $topData['activeUsers'] }}</span>
                                        </h4>

                                    </div>



                                </div>


                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card">
                            <div class="card-body d-flex justify-content-between align-items-center">

                                <div>

                                    <div class="avatar-md flex-shrink-0">
                                        <span class="avatar-title bg-danger rounded-circle fs-2">
                                            <i class='bx bx-user-x'></i>
                                        </span>
                                    </div>

                                </div>
                                <div>
                                    <p class="text-uppercase fw-medium text-muted text-truncate ">
                                        Inactive Users</p>
                                    <div class="d-flex align-items-center mb-3 text-end">
                                        <h4 class="fs-4 flex-grow-1 mb-0"><span class="counter-value"
                                                data-target="{{ $topData['inactiveUsers'] }}">{{ $topData['inactiveUsers'] }}</span>
                                        </h4>

                                    </div>



                                </div>


                            </div>
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="card">
                            <div class="card-body d-flex justify-content-between align-items-center">

                                <div>

                                    <div class="avatar-md flex-shrink-0">
                                        <span class="avatar-title bg-warning rounded-circle fs-2">
                                            <i class='bx bx-bookmarks'></i>
                                        </span>
                                    </div>

                                </div>
                                <div>
                                    <p class="text-uppercase fw-medium text-muted text-truncate ">
                                        Indicators</p>
                                    <div class="d-flex align-items-center mb-3 text-end">
                                        <h4 class="fs-4 flex-grow-1 mb-0"><span class="counter-value"
                                                data-target="{{ $topData['indicators'] }}">{{ $topData['indicators'] }}</span>
                                        </h4>

                                    </div>



                                </div>


                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card">
                            <div class="card-body d-flex justify-content-between align-items-center">

                                <div>

                                    <div class="avatar-md flex-shrink-0">
                                        <span class="avatar-title bg-info rounded-circle fs-2">
                                            <i class='bx bx-briefcase'></i>
                                        </span>
                                    </div>

                                </div>
                                <div>
                                    <p class="text-uppercase fw-medium text-muted text-truncate ">
                                        Projects</p>
                                    <div class="d-flex align-items-center mb-3 text-end">
                                        <h4 class="fs-4 flex-grow-1 mb-0"><span class="counter-value"
                                                data-target="{{ $topData['projects'] }}">{{ $topData['projects'] }}</span>
                                        </h4>

                                    </div>



                                </div>


                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card">
                            <div class="card-body d-flex justify-content-between align-items-center">

                                <div>

                                    <div class="avatar-md flex-shrink-0">
                                        <span class="avatar-title bg-secondary rounded-circle fs-2">
                                            <i class='bx bx-book-open'></i>
                                        </span>
                                    </div>

                                </div>
                                <div>
                                    <p class="text-uppercase fw-medium text-muted text-truncate ">
                                        Forms</p>
                                    <div class="d-flex align-items-center mb-3 text-end">
                                        <h4 class="fs-4 flex-grow-1 mb-0"><span class="counter-value"
                                                data-target="{{ $topData['forms'] }}">{{ $topData['forms'] }}</span>
                                        </h4>

                                    </div>



                                </div>


                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="row @if ($showContent) d-none @endif" x-data x-init="setTimeout(() => {
            $wire.loadData()
        }, 5000)">

            <div class="col-12 mb-5">
                <div class="d-flex justify-content-center align-items-center">
                    <div class="spinner-border text-primary spinner-border-lg" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>

                </div>

            </div>




        </div>
        @if ($showContent)
            <div class="row">
                <div class="col-8">
                    <div class="card">
                        <div class="card-header fw-bold   py-3">
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

                <div class="col-4 d-flex align-items-stretch">
                    <div class="card w-100" x-data="{
                    
                        init() {
                            let submissionCategories = @js($submissionCategories);
                            // Convert string values to numbers
                            let pending = parseInt(submissionCategories.pending);
                            let approved = parseInt(submissionCategories.approved);
                            let denied = parseInt(submissionCategories.denied);
                    
                            options = {
                                series: [pending, approved, denied],
                                chart: {
                                    width: '100%',
                    
                                    type: 'donut',
                                },
                                labels: ['Pending', 'Approved', 'Denied'],
                    
                                colors: ['#006989', '#34A287', '#FA7070'],
                                legend: {
                                    position: 'bottom'
                                }
                    
                            };
                    
                            let chart = new ApexCharts($refs.pies, options);
                            chart.render();
                        }
                    
                    
                    }">


                        <div class="mt-5" x-ref='pies'></div>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-lg-6 col-md-6 d-flex align-items-stretch">
                    <div class="card w-100">
                        <div class="card-header">
                            <div class="align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Users</h4>
                                <a class="btn btn-primary btn-sm" href="/admin/users" role="button">View More</a>

                            </div>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-borderless  align-middle">
                                    <thead>

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


                                    </tbody>

                                </table>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 d-flex align-items-stretch">
                    <div class="card w-100">
                        <div class="card-header">
                            <div class="align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Recent Register</h4>
                                <a class="btn btn-primary btn-sm"
                                    href="/admin/forms/rtc-market/attendance-register/view" role="button">View
                                    More</a>

                            </div>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-borderless  align-middle">
                                    <thead>
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
                                                            alt=""
                                                            class="shadow avatar-sm rounded-circle me-2">
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
                                                    <div class="alert alert-light text-center" role="alert">No data
                                                        available!
                                                    </div>
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
        @endif
    </div>

</div>
