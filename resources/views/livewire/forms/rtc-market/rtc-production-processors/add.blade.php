<div>
    <style>
        input,
        select,
        label {
            text-transform: uppercase;
        }

        .sticky-side {
            position: sticky;
            top: 120px;

        }

        .nav-pills a:hover {
            background: #3980c0;
            color: white;
        }
    </style>
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Dashboard</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active">Page Name</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <h3 class="mb-5 text-center text-primary">RTC PRODUCTION AND MARKETING FORM (PROCESSORS)</h3>


        @if (session()->has('success'))
            <x-success-alert>{!! session()->get('success') !!}</x-success-alert>
        @endif
        @if (session()->has('error'))
            <x-error-alert>{!! session()->get('error') !!}</x-error-alert>
        @endif

        @if (session()->has('validation_error'))
            <x-error-alert>{!! session()->get('validation_error') !!}</x-error-alert>
        @endif



        @if ($openSubmission === false)
            <div class="alert alert-warning" role="alert">
                You can not submit a form right now
                because submissions are closed for the moment!
            </div>
        @endif

        <div class="mb-1 row  @if ($openSubmission === false) opacity-25  pe-none @endif" x-data="{
            selectedFinancialYear: $wire.entangle('selectedFinancialYear').live,
            selectedMonth: $wire.entangle('selectedMonth').live,
            selectedIndicator: $wire.entangle('selectedIndicator').live,
        }">

            <div class="col-md-8">
                <form wire:submit.debounce.1000ms='save'>
                    <div class="card col-12 col-md-12">
                        <div class="card-header fw-bold" id="section-0">Location</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="" class="form-label">ENTERPRISE</label>
                                <x-text-input wire:model='location_data.enterprise' :class="$errors->has('location_data.enterprise') ? 'is-invalid' : ''" />
                                @error('location_data.enterprise')
                                    <x-error>{{ $message }}</x-error>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">DISTRICT</label>
                                <select
                                    class="form-select @error('location_data.district')
                                        is-invalid
                                    @enderror"
                                    wire:model='location_data.district'>
                                    <option value="">Choose one</option>
                                    <option>BALAKA</option>
                                    <option>BLANTYRE</option>
                                    <option>CHIKWAWA</option>
                                    <option>CHIRADZULU</option>
                                    <option>CHITIPA</option>
                                    <option>DEDZA</option>
                                    <option>DOWA</option>
                                    <option>KARONGA</option>
                                    <option>KASUNGU</option>
                                    <option>LILONGWE</option>
                                    <option>MACHINGA</option>
                                    <option>MANGOCHI</option>
                                    <option>MCHINJI</option>
                                    <option>MULANJE</option>
                                    <option>MWANZA</option>
                                    <option>MZIMBA</option>
                                    <option>NENO</option>
                                    <option>NKHATA BAY</option>
                                    <option>NKHOTAKOTA</option>
                                    <option>NSANJE</option>
                                    <option>NTCHEU</option>
                                    <option>NTCHISI</option>
                                    <option>PHALOMBE</option>
                                    <option>RUMPHI</option>
                                    <option>SALIMA</option>
                                    <option>THYOLO</option>
                                    <option>ZOMBA</option>
                                </select>
                                @error('location_data.district')
                                    <x-error>{{ $message }}</x-error>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label">EPA</label>
                                <x-text-input wire:model='location_data.epa' :class="$errors->has('location_data.epa') ? 'is-invalid' : ''" />
                                @error('location_data.epa')
                                    <x-error>{{ $message }}</x-error>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label">SECTION</label>
                                <x-text-input wire:model='location_data.section' :class="$errors->has('location_data.section') ? 'is-invalid' : ''" />
                                @error('location_data.section')
                                    <x-error>{{ $message }}</x-error>
                                @enderror
                            </div>

                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            @include('livewire.forms.rtc-market.rtc-production-processors.first')

                            @include('livewire.forms.rtc-market.rtc-production-processors.repeats')

                            <div class="d-grid col-12 justify-content-center" x-data>

                                <button class="btn btn-primary btn-lg"
                                    @click="window.scrollTo({
                                    top: 0,
                                    behavior: 'smooth'
                                })"
                                    type="submit">Submit</button>
                            </div>
                        </div>
                    </div>

                </form>


            </div>



            <div class="d-none d-md-block col-md-4 ">
                <div class="card sticky-side">
                    <div class="card-body">
                        <nav class="nav nav-pills flex-column nav-fill ">
                            <a class="nav-link " aria-current="page" href="#section-0">LOCATION</a>
                            <a class="nav-link" href="#section-a" href="#">SECTION A: RTC
                                ACTOR PROFILE</a>

                            <a class="nav-link" href="#section-b"href="#">SECTION B: RTC
                                MARKETING</a>


                            <a x-show="has_rtc_market_contract==1" x-data="{ has_rtc_market_contract: $wire.entangle('has_rtc_market_contract') }" class="nav-link"
                                href="#section-f" href="#">CONTRACTUAL
                                AGREEMENT</a>


                            <a x-show="sells_to_domestic_markets == 1" x-data="{ sells_to_domestic_markets: $wire.entangle('sells_to_domestic_markets'), }" class="nav-link"
                                href="#section-g" href="#">DOMESTIC
                                MARKETS</a>
                            <a x-show="sells_to_international_markets == 1" x-data="{
                            
                                sells_to_international_markets: $wire.entangle('sells_to_international_markets'),
                            }" class="nav-link"
                                href="#section-h" href="#">INTERNATIONAL
                                MARKETS</a>
                        </nav>

                    </div>
                </div>

            </div>
        </div>






    </div>
    @script
        <script>
            let textInputs = document.querySelectorAll('input[type="text"]');

            // Attach event listener to each input
            textInputs.forEach(function(input) {
                input.addEventListener('input', function() {
                    // Convert input value to uppercase
                    this.value = this.value.toUpperCase();
                });

            });

            document.querySelectorAll('input[type="number"]').forEach(function(input) {
                input.setAttribute('step', '0.01');
            });
        </script>
    @endscript
</div>
