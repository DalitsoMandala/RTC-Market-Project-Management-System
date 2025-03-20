<div>
    @section('title')
        Targets
    @endsection
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Targets</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ $routePrefix }}/reports">Reporting</a></li>

                            <li class="breadcrumb-item active"> Targets</li>
                        </ol>
                    </div>

                </div>
            </div>

        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <div class="card ">
                    <div class="card-header">
                        <h5 class="card-title">Targets Table</h5>
                    </div>

                    <div class="px-0 card-body">
                        <ul class="mx-1 my-2 nav nav-tabs">
                            <li class="nav-item">
                                <a href="{{ $routePrefix }}/reports" class="nav-link "
                                    aria-current="page">Reporting</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ $routePrefix }}/standard-targets" class="nav-link ">Standard
                                    Targets</a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ $routePrefix }}/targets" class="nav-link active">Targets</a>
                            </li>

                        </ul>

                        <livewire:targets.target-table />
                    </div>
                </div>
            </div>
        </div>
    </div>
