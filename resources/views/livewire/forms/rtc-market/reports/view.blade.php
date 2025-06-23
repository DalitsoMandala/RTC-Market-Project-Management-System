<div>
    @section('title')
        View Aggregates (Reports) Data
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
                    <h4 class="mb-0">View Data</h4>

                    <div class="page-title-right" wire:ignore>
                        @php
                            $routePrefix = \Illuminate\Support\Facades\Route::current()->getPrefix();
                        @endphp
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ $routePrefix }}/forms">Forms</a></li>
                            <li class="breadcrumb-item active">View Data</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">


            <div class="col-12">

                <ul class=" nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="batch-tab" data-bs-toggle="tab" data-bs-target="#normal"
                            type="button" role="tab" aria-controls="home" aria-selected="true">
                            REPORTS (AGGREGATES)
                        </button>
                    </li>




                </ul>


                <div class="card">

                    <div class="card-body">

                        <livewire:tables.submission-report-table />

                    </div>
                </div>

            </div>
        </div>




    </div>



</div>
