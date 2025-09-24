<div>
    @section('title')
        View Farmers Data
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
        <div class="card">
            <x-card-header>RTC Production Farmers Table</x-card-header>
            <div class="card-body" id="#datatable">


                <ul class=" nav nav-tabs " id="myTab" role="tablist">
                    <li class="nav-item border-right " role="presentation">
                        <button class="nav-link active text-capitalize" id="batch-tab" data-bs-toggle="tab"
                            data-bs-target="#normal" type="button" role="tab" aria-controls="home"
                            aria-selected="true">
                            RTC Production Farmers
                        </button>
                    </li>


                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-capitalize" id="people-tab" data-bs-toggle="tab"
                            data-bs-target="#conc" type="button" role="tab" aria-controls="profile"
                            aria-selected="false">
                            Contractual Aggrement
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-capitalize" id="people-tab" data-bs-toggle="tab"
                            data-bs-target="#dom" type="button" role="tab" aria-controls="profile"
                            aria-selected="false">
                            Domestic Markets
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-capitalize" id="people-tab" data-bs-toggle="tab"
                            data-bs-target="#inter" type="button" role="tab" aria-controls="profile"
                            aria-selected="false">
                            International Markets
                        </button>
                    </li>


                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-capitalize" id="people-tab" data-bs-toggle="tab"
                            data-bs-target="#agg" type="button" role="tab" aria-controls="profile"
                            aria-selected="false">
                            Aggregation Centers
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-capitalize" id="people-tab" data-bs-toggle="tab"
                            data-bs-target="#mis" type="button" role="tab" aria-controls="profile"
                            aria-selected="false">
                            Market Information Systems
                        </button>
                    </li>


                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-capitalize" id="people-tab" data-bs-toggle="tab"
                            data-bs-target="#basic" type="button" role="tab" aria-controls="profile"
                            aria-selected="false">
                            Basic seed multiplication
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-capitalize" id="people-tab" data-bs-toggle="tab"
                            data-bs-target="#certified" type="button" role="tab" aria-controls="profile"
                            aria-selected="false">
                            Certified seed multiplication
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-capitalize" id="people-tab" data-bs-toggle="tab"
                            data-bs-target="#cultiv" type="button" role="tab" aria-controls="profile"
                            aria-selected="false">
                            Area under cultivation
                        </button>
                    </li>



                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-capitalize" id="people-tab" data-bs-toggle="tab"
                            data-bs-target="#seed" type="button" role="tab" aria-controls="profile"
                            aria-selected="false">
                            Seed services unit
                        </button>
                    </li>

                </ul>


                <!-- Tab panes -->
                <div class="tab-content mt-2">
                    <div class=" tab-pane active fade show" id="normal" role="tabpanel"
                        aria-labelledby="home-tab">
                        <livewire:tables.rtc-market.rtc-production-farmers-table :key="'rpm1'" :userId="auth()->user()->id"
                            :routePrefix="Route::current()->getPrefix()" />
                    </div>


                    <div class=" tab-pane fade show" id="conc" role="tabpanel" aria-labelledby="profile-tab">
                        <livewire:tables.rtc-market.rtc-production-farmers-conc-agreement />
                    </div>

                    <div class=" tab-pane fade show" id="dom" role="tabpanel" aria-labelledby="profile-tab">
                        <livewire:tables.rtc-market.rtc-production-farmers-dom-markets />
                    </div>
                    <div class=" tab-pane fade show" id="inter" role="tabpanel" aria-labelledby="profile-tab">
                        <livewire:tables.rtc-market.rtc-production-farmers-inter-markets />
                    </div>

                    <div class=" tab-pane fade show" id="agg" role="tabpanel" aria-labelledby="profile-tab">
                        <livewire:tables.rtc-market.rpm-farmer-agg-centers />
                    </div>

                    <div class=" tab-pane fade show" id="mis" role="tabpanel" aria-labelledby="profile-tab">
                        <livewire:tables.rtc-market.rpm-farmer-m-i-s />
                    </div>


                    <div class=" tab-pane fade show" id="basic" role="tabpanel" aria-labelledby="profile-tab">
                        <livewire:tables.rtc-market.rpm-farmer-basic />
                    </div>

                    <div class=" tab-pane fade show" id="certified" role="tabpanel" aria-labelledby="profile-tab">
                        <livewire:tables.rtc-market.rpm-farmer-certified />
                    </div>


                    <div class=" tab-pane fade show" id="cultiv" role="tabpanel" aria-labelledby="profile-tab">
                        <livewire:tables.rtc-market.rpm-farmer-cultivation />
                    </div>
                    <div class=" tab-pane fade show" id="seed" role="tabpanel" aria-labelledby="profile-tab">
                        <livewire:tables.farmer-seed-registration-table />
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
</div>
