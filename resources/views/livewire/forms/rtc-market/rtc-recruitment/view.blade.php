<div>
    @section('title')
        View Recruitment Data
    @endsection
    <div class="container-fluid">

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
                            <li class="breadcrumb-item active">View Data</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- Tab panes -->


        <div class="card">
<x-card-header>RTC Actor Recruitment Table</x-card-header>
            <div class=" card-body">
                <!-- Nav tabs -->
                <ul class=" nav nav-tabs " id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home"
                            type="button" role="tab" aria-controls="home" aria-selected="true">
                            RTC Actor Recruitment
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile"
                            type="button" role="tab" aria-controls="profile" aria-selected="false">
                            Seed Services Unit
                        </button>
                    </li>

                </ul>

                <div class="tab-content mt-2">
                    <div class="tab-pane  active" id="home" role="tabpanel" aria-labelledby="home-tab">



                        <livewire:tables.rtc-market.recruitments-table />


                    </div>

                    <div class="tab-pane" id="profile" role="tabpanel" aria-labelledby="profile-tab">



                        <livewire:tables.recruitment-seed-services-table />



                    </div>
                </div>
            </div>



        </div>



    </div>
    @script
        <script>
            if (window.location.hash !== '') {
                const button = document.querySelector(`button[data-bs-target='${window.location.hash}']`);
                if (button) {
                    button.click();

                }
            }
        </script>
    @endscript
