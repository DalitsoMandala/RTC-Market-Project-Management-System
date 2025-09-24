<div>
    @section('title')
        View Attendance Register Data
    @endsection
    <div class="container-fluid">

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




        <div class="card">
            <x-card-header>RTC Market Attendance Register Table</x-card-header>

            <div class="card-body">
                <livewire:tables.rtc-market.attendance-register-table />
            </div>
        </div>


    </div>

</div>
