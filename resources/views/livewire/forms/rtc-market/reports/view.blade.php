<div>
    @section('title')
        View Reports
    @endsection
    <div class="container-fluid">
        <style>
            td {
                color: red;
            }
        </style>
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">


                    <div class="page-title-left col-12" wire:ignore>
                        @php
                            $routePrefix = \Illuminate\Support\Facades\Route::current()->getPrefix();
                        @endphp
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ $routePrefix }}/forms">Forms</a></li>
                            <li class="breadcrumb-item active">View Reports</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">


            <div class="col-12">


                <div class="card">
                    <x-card-header>Reports</x-card-header>
                    <div class="card-body">

                        <livewire:tables.submission-report-table />

                    </div>
                </div>

            </div>
        </div>




    </div>



</div>
