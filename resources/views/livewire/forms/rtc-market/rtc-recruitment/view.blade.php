<div>
    @section('title')
        View Recruitment Data
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
        <!-- end page title -->
        <div class="row">
            <div class="col-12">


                <!-- Nav tabs -->
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home"
                            type="button" role="tab" aria-controls="home" aria-selected="true">
                            RTC ACTOR RECRUITMENT
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile"
                            type="button" role="tab" aria-controls="profile" aria-selected="false">
                            SEED SERVICES UNIT
                        </button>
                    </li>

                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div class="tab-pane active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <div class="card">

                            <div class=" card-body">
                                <livewire:tables.rtc-market.recruitments-table />
                            </div>

                        </div>
                    </div>
                    <div class="tab-pane" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <div class="card">

                            <div class=" card-body">
                                <livewire:tables.recruitment-seed-services-table />
                            </div>

                        </div>
                    </div>

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
