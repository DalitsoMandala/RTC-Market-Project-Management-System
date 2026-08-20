<div>

    @section('title')
        View Production Marketing Log Data
    @endsection
    <div class="my-2 container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">



                    <div class="page-title-left col-12" wire:ignore>
                        @php
                            $routePrefix = trim(Route::current()->getPrefix(), '/');
                        @endphp
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="/{{ $routePrefix }}/forms">Forms</a></li>
                            <li class="breadcrumb-item active">View Data</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>

        <div class="card">
            <x-card-header>Production Marketing Log Table</x-card-header>
            <div class=" card-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <livewire:tables.rtc-market.production-marketing-log-table />
                    </div>

                </div>
            </div>


        </div>





    </div>

</div>
