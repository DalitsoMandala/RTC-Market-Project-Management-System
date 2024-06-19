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

        .iti__placeholder {
            color: red;
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
        <div class="row">
            <div class="col-12 ">
                <h3 class="mb-5 text-center text-primary">RTC PRODUCTION AND MARKETING FORM (FARMERS)</h3>

                @if (session()->has('success'))
                    <x-success-alert>{!! session()->get('success') !!}</x-success-alert>
                @endif

                @if ($openSubmission === false)
                    <div class="alert alert-warning" role="alert">
                        You can not submit a form right now
                        because submissions are closed for the moment!
                    </div>
                @endif

                <div
                    class="mb-1 row justify-content-center @if ($openSubmission === false) opacity-25  pe-none @endif">
                    <form wire:submit='save'>



                        {{-- All cards here --}}
                        <div class="row">
                            <div class="col">

                                @include('livewire.forms.rtc-market.period-view')
                                <div class="row">
                                    <div class="card col-12 col-md-12">
                                        <div class="card-header fw-bold" id="section-0">Location</div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="" class="form-label">ENTERPRISE</label>
                                                <x-text-input wire:model='location_data.enterprise' />
                                                @error('location_data.enterprise')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="" class="form-label">DISTRICT</label>
                                                <select class="form-select" wire:model='location_data.district'>
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
                                                <x-text-input wire:model='location_data.epa' />
                                                @error('location_data.epa')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label for="" class="form-label">SECTION</label>
                                                <x-text-input wire:model='location_data.section' />
                                                @error('location_data.section')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>

                                        </div>
                                    </div>


                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-12">



                                        <div class="card">
                                            <div class="card-body">
                                                @include('livewire.forms.rtc-market.rtc-production-farmers.first')
                                            </div>
                                        </div>
                                        <div class="card">

                                            <div class="card-body">

                                                {{-- @include('livewire.forms.rtc-market.rtc-production-farmers.followup') --}}

                                            </div>


                                        </div>
                                        <div class="card">
                                            <div class="card-body">
                                                @include('livewire.forms.rtc-market.rtc-production-farmers.repeats')
                                            </div>
                                        </div>

                                    </div>

                                </div>
                                <div class="row" x-data>
                                    <div class="d-grid col-12 justify-content-center">

                                        <button class="btn btn-success btn-lg" @click="$wire.dispatch('to-top')"
                                            type="submit">Submit</button>
                                    </div>
                                </div>
                            </div>

                            <div class="d-none d-md-block col-md-4 ">
                                <div class="card sticky-side">
                                    <div class="card-body">
                                        <nav class="nav nav-pills flex-column nav-fill ">
                                            <a class="nav-link " aria-current="page" href="#section-0">LOCATION</a>
                                            <a class="nav-link" href="#section-a" href="#">SECTION A: RTC
                                                ACTOR PROFILE</a>
                                            <a class="nav-link" href="#section-b" href="#">SECTION B: RTC
                                                PRODUCTION </a>

                                            <a class="nav-link" href="#section-c"href="#">SECTION C: RTC
                                                MARKETING</a>
                                            <a class="nav-link" href="#section-d" href="#">SECTION D:
                                                ABOUT
                                                RTC PRODUCTION(Follow up) </a>
                                            <a class="nav-link" href="#section-e" href="#">SECTION E: RTC
                                                MARKETING (Follow up)</a>
                                            <a class="nav-link" href="#section-f" href="#">CONTRACTUAL
                                                AGREEMENT</a>
                                            <a class="nav-link" href="#section-g" href="#">DOMESTIC
                                                MARKETS</a>
                                            <a class="nav-link" href="#section-h" href="#">INTERNATIONAL
                                                MARKETS</a>
                                        </nav>

                                    </div>
                                </div>

                            </div>


                        </div>



                    </form>
                </div>

            </div>

        </div>



        {{--  <div x-data x-init="$wire.on('showModal', (e) => {

            const myModal = new bootstrap.Modal(document.getElementById(e.name), {})
            myModal.show();
        })">


            <x-modal id="view-indicator-modal" title="edit">
                <form>
                    <div class="mb-3">

                        <x-text-input placeholder="Name of indicator..." />
                    </div>

                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>

                    </div>
                </form>
            </x-modal>

        </div> --}}




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

            $wire.on('to-top', () => {

                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                })
            });
        </script>
    @endscript
</div>
