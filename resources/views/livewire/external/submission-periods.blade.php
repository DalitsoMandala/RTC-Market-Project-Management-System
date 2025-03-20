<div>
    @section('title')
        Submission Period
    @endsection
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Submission Period</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active">Submission Period</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between ">
                        <h4 class="card-title">Submission Period Table</h4>

                    </div>
                    <div class="px-0 card-body">
                        @php

                            $route = Route::current()->getPrefix();
                        @endphp
                        <livewire:external.tables.form-table :userId="auth()->user()->id" :currentRoutePrefix="$route" />



                    </div>
                </div>
            </div>
        </div>





    </div>

</div>
