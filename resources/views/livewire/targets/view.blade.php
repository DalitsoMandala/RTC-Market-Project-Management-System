<div>
    @section('title')
        Targets
    @endsection
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">


                    <div class="page-title-left col-12">

                          <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ $routePrefix }}/standard-targets">Indicator Targets</a></li>

                            <li class="breadcrumb-item active">View Targets</li>

                        </ol>
                    </div>

                </div>
            </div>

        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">

                <div class="card">
                    <x-card-header>Targets</x-card-header>
                    <div class="card-body">
                        <livewire:targets.target-table />
                    </div>
                </div>

            </div>
        </div>
    </div>
