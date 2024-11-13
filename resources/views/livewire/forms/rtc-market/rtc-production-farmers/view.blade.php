<div>
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-end">


                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="/cip/forms">Forms</a></li>
                            <li class="breadcrumb-item active">View</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                @if (session()->has('success'))
                    <x-success-alert>{!! session()->get('success') !!}</x-success-alert>
                @endif
                @if (session()->has('error'))
                    <x-error-alert>{!! session()->get('error') !!}</x-error-alert>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h4 class="text-center text-warning text-uppercase">RTC PRODUCTION AND MARKETING FORM DATA FOR
                            FARMERS @if ($batch_no)
                                [Batch : {{ $batch_no }}]
                            @endif
                        </h4>
                    </div>
                    <div class="card-body" id="#datatable">


                        {{-- @if ($loadingData)
                        <div wire:poll.5s='readCache()' class="d-flex justify-content-center align-items-center">
                            <div class="spinner-border text-warning spinner-border-lg" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>

                        </div>
                        @endif --}}


                        <ul class="nav nav-tabs  " id="myTab" role="tablist">
                            <li class="nav-item  border-right" role="presentation">
                                <button class="nav-link active" id="batch-tab" data-bs-toggle="tab"
                                    data-bs-target="#normal" type="button" role="tab" aria-controls="home"
                                    aria-selected="true">
                                    Farmers' Data
                                </button>
                            </li>

                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="people-tab" data-bs-toggle="tab" data-bs-target="#followup"
                                    type="button" role="tab" aria-controls="profile" aria-selected="false">
                                    Follow up Data
                                </button>
                            </li>

                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="people-tab" data-bs-toggle="tab" data-bs-target="#conc"
                                    type="button" role="tab" aria-controls="profile" aria-selected="false">
                                    Contractual Aggrement Data
                                </button>
                            </li>

                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="people-tab" data-bs-toggle="tab" data-bs-target="#dom"
                                    type="button" role="tab" aria-controls="profile" aria-selected="false">
                                    Domestic Markets Data
                                </button>
                            </li>

                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="people-tab" data-bs-toggle="tab" data-bs-target="#inter"
                                    type="button" role="tab" aria-controls="profile" aria-selected="false">
                                    International Markets Data
                                </button>
                            </li>


                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="people-tab" data-bs-toggle="tab" data-bs-target="#agg"
                                    type="button" role="tab" aria-controls="profile" aria-selected="false">
                                    Aggregation Centers
                                </button>
                            </li>

                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="people-tab" data-bs-toggle="tab" data-bs-target="#mis"
                                    type="button" role="tab" aria-controls="profile" aria-selected="false">
                                    Market Information System
                                </button>
                            </li>


                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="people-tab" data-bs-toggle="tab" data-bs-target="#basic"
                                    type="button" role="tab" aria-controls="profile" aria-selected="false">
                                    Basic seed multiplication
                                </button>
                            </li>

                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="people-tab" data-bs-toggle="tab"
                                    data-bs-target="#certified" type="button" role="tab" aria-controls="profile"
                                    aria-selected="false">
                                    Certified seed multiplication
                                </button>
                            </li>

                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="people-tab" data-bs-toggle="tab" data-bs-target="#cultiv"
                                    type="button" role="tab" aria-controls="profile" aria-selected="false">
                                    Area under cultivation
                                </button>
                            </li>

                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div class="mt-2 tab-pane active fade show" id="normal" role="tabpanel"
                                aria-labelledby="home-tab">
                                <livewire:tables.rtc-market.rtc-production-farmers-table :key="'rpm1'"
                                    :userId="auth()->user()->id" :routePrefix="Route::current()->getPrefix()" />
                            </div>
                            <div class="mt-2 tab-pane fade show" id="followup" role="tabpanel"
                                aria-labelledby="profile-tab">

                                <livewire:tables.rtc-market.rtc-production-farmers-follow-u :key="'rpm2'"
                                    :userId="auth()->user()->id" :routePrefix="Route::current()->getPrefix()" />
                            </div>

                            <div class="mt-2 tab-pane fade show" id="conc" role="tabpanel"
                                aria-labelledby="profile-tab">
                                <livewire:tables.rtc-market.rtc-production-farmers-conc-agreement />
                            </div>

                            <div class="mt-2 tab-pane fade show" id="dom" role="tabpanel" aria-labelledby="profile-tab">
                                <livewire:tables.rtc-market.rtc-production-farmers-dom-markets />
                            </div>
                            <div class="mt-2 tab-pane fade show" id="inter" role="tabpanel"
                                aria-labelledby="profile-tab">
                                <livewire:tables.rtc-market.rtc-production-farmers-inter-markets />
                            </div>

                            <div class="mt-2 tab-pane fade show" id="agg" role="tabpanel" aria-labelledby="profile-tab">
                                <livewire:tables.rtc-market.rpm-farmer-agg-centers />
                            </div>

                            <div class="mt-2 tab-pane fade show" id="mis" role="tabpanel" aria-labelledby="profile-tab">
                                <livewire:tables.rtc-market.rpm-farmer-m-i-s />
                            </div>


                            <div class="mt-2 tab-pane fade show" id="basic" role="tabpanel"
                                aria-labelledby="profile-tab">
                                <livewire:tables.rtc-market.rpm-farmer-basic />
                            </div>

                            <div class="mt-2 tab-pane fade show" id="certified" role="tabpanel"
                                aria-labelledby="profile-tab">
                                <livewire:tables.rtc-market.rpm-farmer-certified />
                            </div>


                            <div class="mt-2 tab-pane fade show" id="cultiv" role="tabpanel"
                                aria-labelledby="profile-tab">
                                <livewire:tables.rtc-market.rpm-farmer-cultivation />
                            </div>
                            {{--





                            <div class="mt-2 tab-pane fade show" id="agg" role="tabpanel" aria-labelledby="profile-tab">
                                <livewire:tables.rtc-market.rpm-farmer-agg-centers />
                            </div>
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