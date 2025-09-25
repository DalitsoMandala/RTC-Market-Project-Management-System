<div>
    @section('title')
        Submission Period
    @endsection
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                 

                    <div class="page-title-left col-12">
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
                    <x-card-header>Submission Period List</x-card-header>
                    <div class=" card-body">
                        @php

                            $route = Route::current()->getPrefix();
                        @endphp
                        {{-- <livewire:external.tables.form-table :userId="auth()->user()->id" :currentRoutePrefix="$route" /> --}}

                        <livewire:tables.submission-period-table :currentRoutePrefix="$route">

                    </div>
                </div>
            </div>
        </div>





    </div>

</div>
