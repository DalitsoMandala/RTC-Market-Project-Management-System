<div>
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
            <div class="col">

                @if (session()->has('success'))
                    <x-success-alert>{!! session()->get('success') !!}</x-success-alert>
                @endif
                @if (session()->has('error'))
                    <x-error-alert>{!! session()->get('error') !!}</x-error-alert>
                @endif

                @if (session()->has('validation_error'))
                    <x-error-alert>{!! session()->get('validation_error') !!}</x-error-alert>
                @endif




            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-8">


                <div class="card">
                    <div class="card-body">

                        <div class="alert alert-primary" id="section-b" role="alert">
                            <strong> RTC PRODUCTION FARMERS (FOLLOW UP) </strong>
                        </div>
                        <form wire:submit.debounce.1000ms='save'>
                            <div wire:ignore class="mb-3" x-data="{}" x-init="() => {
                                $('#select-recruits').select2({
                                    width: '100%',
                                    theme: 'bootstrap-5',
                                    containerCssClass: 'select2--small',
                                    dropdownCssClass: 'select2--small',
                                });
                                $('#select-recruits').on('change', function(e) {
                                    data = e.target.value;
                            
                                    setTimeout(() => {
                                        $wire.set('selectedRecruit', data);
                                    }, 1000)
                            
                            
                                });
                            }">
                                <label for="" class="form-label">Select Actor</label>
                                <select id="select-recruits" class="form-select " wire:model.debounce='selectedRecruit'>
                                    <option selected value="">Select one</option>
                                    @foreach ($recruits as $recruit)
                                        <option value="{{ $recruit->id }}">
                                            ({{ str_pad($recruit->id, 5, '0', STR_PAD_LEFT) }})
                                            {{ $recruit->name_of_actor }} </option>
                                    @endforeach
                                </select>

                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">Date of follow up</label>
                                <input type="date"
                                    class="form-control @error('date_of_follow_up') is-invalid @enderror"
                                    wire:model='date_of_follow_up' />
                                @error('date_of_follow_up')
                                    <x-error>{{ $message }}</x-error>
                                @enderror
                            </div>


                            <div class="hide"
                                x-data="{
                                    show: $wire.entangle('show')
                                }":class="{ 'pe-none opacity-25' : show === false}">
                                <!-- Group -->
                                <div class="mb-3" x-data="{ group: $wire.entangle('group') }">
                                    <label for="group" class="form-label">Group</label>
                                    <select class="form-select @error('group') is-invalid @enderror bg-light"
                                        x-model="group" disabled>
                                        <option value="">Select One</option>
                                        <option value="EARLY GENERATION SEED PRODUCER">EARLY GENERATION SEED PRODUCER
                                        </option>
                                        <option value="SEED MULTIPLIER">SEED MULTIPLIER</option>
                                        <option value="RTC PRODUCER">RTC PRODUCER</option>
                                    </select>

                                    @error('group')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="" class="form-label">ENTERPRISE</label>
                                    <x-text-input style="background: #f8f9fa" disabled
                                        wire:model='location_data.enterprise' :class="$errors->has('location_data.enterprise') ? 'is-invalid' : ''" />
                                    @error('location_data.enterprise')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="" class="form-label">DISTRICT</label>
                                    <select disabled style="background: #f8f9fa"
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
                                    <x-text-input style="background: #f8f9fa" disabled wire:model='location_data.epa'
                                        :class="$errors->has('location_data.epa') ? 'is-invalid' : ''" />
                                    @error('location_data.epa')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="" class="form-label">SECTION</label>
                                    <x-text-input style="background: #f8f9fa" disabled
                                        wire:model='location_data.section' :class="$errors->has('location_data.section') ? 'is-invalid' : ''" />
                                    @error('location_data.section')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>

                                <hr>
                                @include('livewire.forms.rtc-market.rtc-production-farmers.follow-up-data')
                                @include('livewire.forms.rtc-market.rtc-production-farmers.repeats')

                                <div class="d-grid col-12 justify-content-center" x-data>

                                    <button class="px-5 btn btn-primary btn-lg"
                                        @click="window.scrollTo({
                                    top: 0,
                                    behavior: 'smooth'
                                })"
                                        type="submit">Submit</button>
                                </div>

                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>





    </div>

</div>
