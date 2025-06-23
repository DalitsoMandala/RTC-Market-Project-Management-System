<div>
    @section('title')
        View Processors Data
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

                <ul class=" nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="batch-tab" data-bs-toggle="tab" data-bs-target="#normal"
                            type="button" role="tab" aria-controls="home" aria-selected="true">
                            RTC PRODUCTION PROCESSORS
                        </button>
                    </li>



                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="people-tab" data-bs-toggle="tab" data-bs-target="#conc"
                            type="button" role="tab" aria-controls="profile" aria-selected="false">
                            CONTRACTUAL AGGREMENT
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="people-tab" data-bs-toggle="tab" data-bs-target="#dom"
                            type="button" role="tab" aria-controls="profile" aria-selected="false">
                            DOMESTIC MARKETS
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="people-tab" data-bs-toggle="tab" data-bs-target="#inter"
                            type="button" role="tab" aria-controls="profile" aria-selected="false">
                            INTERNATION MARKETS
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="mis-tab" data-bs-toggle="tab" data-bs-target="#mis"
                            type="button" role="tab" aria-controls="profile" aria-selected="false">
                            MARKET INFORMATION SYSTEMS
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="aggrSales-tab" data-bs-toggle="tab" data-bs-target="#aggrSales"
                            type="button" role="tab" aria-controls="profile" aria-selected="false">
                            AGGREGATION CENTERS
                        </button>
                    </li>
                </ul>

                <div class="card">

                    <div class=" card-body" id="#datatable">

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div class="mt-2 tab-pane active fade show" id="normal" role="tabpanel"
                                aria-labelledby="home-tab">
                                <livewire:tables.rtc-market.rtc-production-processors-table :key="'rpm1'"
                                    :userId="auth()->user()->id" :routePrefix="Route::current()->getPrefix()" />
                            </div>


                            <div class="mt-2 tab-pane fade show" id="conc" role="tabpanel"
                                aria-labelledby="profile-tab">
                                <livewire:tables.rtc-market.rtc-production-processors-conc-agreement />
                            </div>
                            <div class="mt-2 tab-pane fade show" id="dom" role="tabpanel"
                                aria-labelledby="profile-tab">
                                <livewire:tables.rtc-market.rtc-production-processor-dom-markets />
                            </div>
                            <div class="mt-2 tab-pane fade show" id="inter" role="tabpanel"
                                aria-labelledby="profile-tab">
                                <livewire:tables.rtc-market.rtc-production-processor-inter-markets />
                            </div>
                            <div class="mt-2 tab-pane fade show" id="mis" role="tabpanel"
                                aria-labelledby="profile-tab">
                                <livewire:tables.rtc-market.rpm-processor-m-i-s />
                            </div>


                            <div class="mt-2 tab-pane fade show" id="aggrSales" role="tabpanel"
                                aria-labelledby="profile-tab">
                                <livewire:tables.rtc-market.rpm-processor-agg-centers />
                            </div>
                            {{--





                            --}}
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
</div>
