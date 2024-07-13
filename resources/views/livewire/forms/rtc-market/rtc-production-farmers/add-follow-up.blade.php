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
                            {{-- <div class="mb-3" x-data="{}" x-init="() => {
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
                                            ({{ $recruit->id }})
                                            {{ $recruit->name_of_actor }} </option>
                                    @endforeach
                                </select>

                            </div> --}}
                            <div class="mb-3">
                                <label for="" class="form-label">GROUP NAME</label>
                                <x-text-input wire:model='location_data.group_name' :class="$errors->has('location_data.group_name') ? 'is-invalid' : ''" />
                                @error('location_data.group_name')
                                    <x-error>{{ $message }}</x-error>
                                @enderror
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
                                <div class="mb-3" x-data="{ group: $wire.entangle('group') }" x-init="$wire.on('change-group', (e) => {
                                    setTimeout(() => {
                                        group = e.data;
                                    }, 500)
                                })">
                                    <label for="group" class="form-label">Group</label>
                                    <select x-ref="selectGroup"
                                        class="form-select bg-light @error('group') is-invalid @enderror"
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
                                <!-- Area Under Cultivation (Number of Acres) by Variety -->
                                <div class="mb-3">
                                    <label for="areaUnderCultivation" class="my-3 form-label fw-bold">Area Under
                                        Cultivation
                                        (Number of Acres) by Variety</label>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="areaUnderCultivationVariety1" class="form-label">Area
                                                    Under Cultivation (Variety 1):</label>
                                                <input type="number"
                                                    class="form-control @error('area_under_cultivation.variety_1') is-invalid @enderror"
                                                    id="areaUnderCultivationVariety1"
                                                    wire:model="area_under_cultivation.variety_1">
                                                @error('area_under_cultivation.variety_1')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="areaUnderCultivationVariety2" class="form-label">Area
                                                    Under Cultivation (Variety 2):</label>
                                                <input type="number"
                                                    class="form-control @error('area_under_cultivation.variety_2') is-invalid @enderror"
                                                    id="areaUnderCultivationVariety2"
                                                    wire:model="area_under_cultivation.variety_2">
                                                @error('area_under_cultivation.variety_2')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="areaUnderCultivationVariety3" class="form-label">Area
                                                    Under Cultivation (Variety 3):</label>
                                                <input type="number"
                                                    class="form-control @error('area_under_cultivation.variety_3') is-invalid @enderror"
                                                    id="areaUnderCultivationVariety3"
                                                    wire:model="area_under_cultivation.variety_3">
                                                @error('area_under_cultivation.variety_3')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="areaUnderCultivationVariety4" class="form-label">Area
                                                    Under Cultivation (Variety 4):</label>
                                                <input type="number"
                                                    class="form-control @error('area_under_cultivation.variety_4') is-invalid @enderror"
                                                    id="areaUnderCultivationVariety4"
                                                    wire:model="area_under_cultivation.variety_4">
                                                @error('area_under_cultivation.variety_4')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="areaUnderCultivationVariety5" class="form-label">Area
                                                    Under Cultivation (Variety 5):</label>
                                                <input type="number"
                                                    class="form-control @error('area_under_cultivation.variety_5') is-invalid @enderror"
                                                    id="areaUnderCultivationVariety5"
                                                    wire:model="area_under_cultivation.variety_5">
                                                @error('area_under_cultivation.variety_5')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>


                                </div>

                                <!-- Group -->

                                <!-- Number of Plantlets Produced -->
                                <div class="mb-3" x-data="{
                                    group: $wire.entangle('group'),
                                    number_of_plantlets_produced: $wire.entangle('number_of_plantlets_produced')
                                }" x-init="$watch('group', (v) => {
                                    if (v != 'EARLY GENERATION SEED PRODUCER') {
                                        number_of_plantlets_produced = {};
                                        $wire.resetValues('number_of_plantlets_produced');
                                    }
                                });
                                $watch('number_of_plantlets_produced', (v) => {


                                    $wire.number_of_plantlets_produced = v;

                                });"
                                    x-show="group=='EARLY GENERATION SEED PRODUCER'">

                                    <label for="numberOfPlantlets" class="my-3 form-label fw-bold">Number of
                                        Plantlets
                                        Produced</label>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="cassavaPlantlets" class="form-label">Number of
                                                    Plantlets Produced (Cassava):</label>
                                                <input type="number"
                                                    class="form-control @error('number_of_plantlets_produced.cassava') is-invalid @enderror"
                                                    id="cassavaPlantlets"
                                                    x-model="number_of_plantlets_produced.cassava">
                                                @error('number_of_plantlets_produced.cassava')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="potatoPlantlets" class="form-label">Number of
                                                    Plantlets Produced (Potato):</label>
                                                <input type="number"
                                                    class="form-control @error('number_of_plantlets_produced.potato') is-invalid @enderror"
                                                    id="potatoPlantlets"
                                                    x-model="number_of_plantlets_produced.potato">
                                                @error('number_of_plantlets_produced.potato')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="sweetPotatoPlantlets" class="form-label">Number of
                                                    Plantlets Produced (Sweet Potato):</label>
                                                <input type="number"
                                                    class="form-control @error('number_of_plantlets_produced.sweet_potato') is-invalid @enderror"
                                                    id="sweetPotatoPlantlets"
                                                    x-model="number_of_plantlets_produced.sweet_potato">
                                                @error('number_of_plantlets_produced.sweet_potato')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <!-- Number of Screen House Vines Harvested (Sweet Potatoes) -->
                                <div class="mb-3" x-data="{
                                    group: $wire.entangle('group'),
                                    number_of_screen_house_vines_harvested: $wire.entangle('number_of_screen_house_vines_harvested'),
                                    init() {
                                        this.$watch('group', (v) => {
                                            if (v != 'EARLY GENERATION SEED PRODUCER') {
                                                $wire.resetValues('number_of_screen_house_vines_harvested');
                                            }
                                        });
                                    }

                                }"
                                    x-show="group=='EARLY GENERATION SEED PRODUCER'">
                                    <label for="numberOfScreenHouseVines" class="form-label">Number of Screen
                                        House Vines Harvested (Sweet Potatoes)</label>
                                    <input type="number"
                                        class="form-control @error('number_of_screen_house_vines_harvested') is-invalid @enderror"
                                        id="numberOfScreenHouseVines"
                                        x-model='number_of_screen_house_vines_harvested'>
                                    @error('number_of_screen_house_vines_harvested')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>
                                <!-- Number of Screen House Mini-Tubers Harvested (Potato) -->
                                <div class="mb-3" x-data="{
                                    group: $wire.entangle('group'),
                                    number_of_screen_house_min_tubers_harvested: $wire.entangle('number_of_screen_house_min_tubers_harvested'),
                                    init() {
                                        this.$watch('group', (v) => {
                                            if (v != 'EARLY GENERATION SEED PRODUCER') {
                                                $wire.resetValues('number_of_screen_house_min_tubers_harvested');
                                            }
                                        });
                                    }
                                }"
                                    x-show="group=='EARLY GENERATION SEED PRODUCER' ">
                                    <label for="numberOfMiniTubers" class="form-label">Number of Screen House
                                        Mini-Tubers Harvested (Potato)</label>
                                    <input type="number"
                                        class="form-control @error('number_of_screen_house_min_tubers_harvested') is-invalid @enderror"
                                        id="numberOfMiniTubers" x-model='number_of_screen_house_min_tubers_harvested'>
                                    @error('number_of_screen_house_min_tubers_harvested')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>
                                <!-- Number of SAH Plants Produced (Cassava) -->
                                <div class="mb-3" x-data="{
                                    group: $wire.entangle('group'),
                                    number_of_sah_plants_produced: $wire.entangle('number_of_sah_plants_produced'),
                                    init() {
                                        this.$watch('group', (v) => {
                                            if (v != 'EARLY GENERATION SEED PRODUCER') {
                                                $wire.resetValues('number_of_sah_plants_produced');
                                            }
                                        });
                                    }
                                }"
                                    x-show=" group=='EARLY GENERATION SEED PRODUCER'">
                                    <label for="numberOfSAHPlants" class="form-label">Number of SAH Plants
                                        Produced (Cassava)</label>
                                    <input type="number"
                                        class="form-control @error('number_of_sah_plants_produced') is-invalid @enderror"
                                        id="numberOfSAHPlants" x-model='number_of_sah_plants_produced'>
                                    @error('number_of_sah_plants_produced')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>

                                <!-- Area Under Basic Seed Multiplication (Number of Acres) -->
                                <div class="mb-3" x-data="{
                                    group: $wire.entangle('group'),
                                    area_under_basic_seed_multiplication: $wire.entangle('area_under_basic_seed_multiplication'),
                                    init() {
                                        this.$watch('group', (v) => {
                                            if (v != 'EARLY GENERATION SEED PRODUCER') {
                                                area_under_basic_seed_multiplication = {};
                                                $wire.resetValues('area_under_basic_seed_multiplication');
                                            }
                                        });

                                        this.$watch('area_under_basic_seed_multiplication', (v) => {
                                            $wire.area_under_basic_seed_multiplication = v;
                                        });
                                    }
                                }"
                                    x-show="group=='EARLY GENERATION SEED PRODUCER'">
                                    <label for="areaUnderBasicSeed" class="my-3 form-label fw-bold">Area Under
                                        Basic Seed
                                        Multiplication (Number of Acres)</label>
                                    <div class="mb-3">
                                        <label for="areaUnderBasicSeedTotal" class="form-label">Area Under Basic
                                            Seed Multiplication (Total):</label>
                                        <input type="number"
                                            class="form-control @error('area_under_basic_seed_multiplication.total') is-invalid @enderror"
                                            id="areaUnderBasicSeedTotal"
                                            wire:model="area_under_basic_seed_multiplication.total">
                                        @error('area_under_basic_seed_multiplication.total')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="variety1Seed" class="form-label">Area Under Basic Seed
                                                    Multiplication (Variety 1):</label>
                                                <input type="number"
                                                    class="form-control @error('area_under_basic_seed_multiplication.variety_1') is-invalid @enderror"
                                                    id="variety1Seed"
                                                    wire:model="area_under_basic_seed_multiplication.variety_1">
                                                @error('area_under_basic_seed_multiplication.variety_1')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="variety2Seed" class="form-label">Area Under Basic Seed
                                                    Multiplication (Variety 2):</label>
                                                <input type="number"
                                                    class="form-control @error('area_under_basic_seed_multiplication.variety_2') is-invalid @enderror"
                                                    id="variety2Seed"
                                                    wire:model="area_under_basic_seed_multiplication.variety_2">
                                                @error('area_under_basic_seed_multiplication.variety_2')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="variety3Seed" class="form-label">Area Under Basic Seed
                                                    Multiplication (Variety 3):</label>
                                                <input type="number"
                                                    class="form-control @error('area_under_basic_seed_multiplication.variety_3') is-invalid @enderror"
                                                    id="variety3Seed"
                                                    wire:model="area_under_basic_seed_multiplication.variety_3">
                                                @error('area_under_basic_seed_multiplication.variety_3')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="variety4Seed" class="form-label">Area Under Basic Seed
                                                    Multiplication (Variety 4):</label>
                                                <input type="number"
                                                    class="form-control @error('area_under_basic_seed_multiplication.variety_4') is-invalid @enderror"
                                                    id="variety4Seed"
                                                    wire:model="area_under_basic_seed_multiplication.variety_4">
                                                @error('area_under_basic_seed_multiplication.variety_4')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="variety5Seed" class="form-label">Area Under Basic Seed
                                                    Multiplication (Variety 5):</label>
                                                <input type="number"
                                                    class="form-control @error('area_under_basic_seed_multiplication.variety_5') is-invalid @enderror"
                                                    id="variety5Seed"
                                                    wire:model="area_under_basic_seed_multiplication.variety_5">
                                                @error('area_under_basic_seed_multiplication.variety_5')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="variety6Seed" class="form-label">Area Under Basic Seed
                                                    Multiplication (Variety 6):</label>
                                                <input type="number"
                                                    class="form-control @error('area_under_basic_seed_multiplication.variety_6') is-invalid @enderror"
                                                    id="variety6Seed"
                                                    wire:model="area_under_basic_seed_multiplication.variety_6">
                                                @error('area_under_basic_seed_multiplication.variety_6')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="variety7Seed" class="form-label">Area Under Basic Seed
                                                    Multiplication (Variety 7):</label>
                                                <input type="number"
                                                    class="form-control @error('area_under_basic_seed_multiplication.variety_7') is-invalid @enderror"
                                                    id="variety7Seed"
                                                    wire:model="area_under_basic_seed_multiplication.variety_7">
                                                @error('area_under_basic_seed_multiplication.variety_7')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror

                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <!-- Area Under Certified Seed Multiplication -->
                                <div class="mb-3">
                                    <label for="areaUnderCertifiedSeed" class="my-3 form-label fw-bold">Area Under
                                        Certified
                                        Seed Multiplication</label>
                                    <div class="mb-3">
                                        <label for="areaUnderCertifiedSeedTotal" class="form-label">Area Under
                                            Certified Seed Multiplication (Total):</label>
                                        <input type="number"
                                            class="form-control @error('area_under_certified_seed_multiplication.total') is-invalid @enderror"
                                            id="areaUnderCertifiedSeedTotal"
                                            wire:model="area_under_certified_seed_multiplication.total">

                                        @error('area_under_certified_seed_multiplication.total')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="variety1CertifiedSeed" class="form-label">Area Under
                                                    Certified Seed Multiplication (Variety 1):</label>
                                                <input type="number"
                                                    class="form-control @error('area_under_certified_seed_multiplication.variety_1') is-invalid @enderror"
                                                    id="variety1CertifiedSeed"
                                                    wire:model="area_under_certified_seed_multiplication.variety_1">
                                                @error('area_under_certified_seed_multiplication.variety_1')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="variety2CertifiedSeed" class="form-label">Area Under
                                                    Certified Seed Multiplication (Variety 2):</label>
                                                <input type="number"
                                                    class="form-control @error('area_under_certified_seed_multiplication.variety_2') is-invalid @enderror"
                                                    id="variety2CertifiedSeed"
                                                    wire:model="area_under_certified_seed_multiplication.variety_2">

                                                @error('area_under_certified_seed_multiplication.variety_2')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="variety3CertifiedSeed" class="form-label">Area Under
                                                    Certified Seed Multiplication (Variety 3):</label>
                                                <input type="number"
                                                    class="form-control @error('area_under_certified_seed_multiplication.variety_3') is-invalid @enderror"
                                                    id="variety3CertifiedSeed"
                                                    wire:model="area_under_certified_seed_multiplication.variety_3">
                                                @error('area_under_certified_seed_multiplication.variety_3')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="variety4CertifiedSeed" class="form-label">Area Under
                                                    Certified Seed Multiplication (Variety 4):</label>
                                                <input type="number"
                                                    class="form-control @error('area_under_certified_seed_multiplication.variety_4') is-invalid @enderror"
                                                    id="variety4CertifiedSeed"
                                                    wire:model="area_under_certified_seed_multiplication.variety_4">
                                                @error('area_under_certified_seed_multiplication.variety_4')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="variety5CertifiedSeed" class="form-label">Area Under
                                                    Certified Seed Multiplication (Variety 5):</label>
                                                <input type="number"
                                                    class="form-control @error('area_under_certified_seed_multiplication.variety_5') is-invalid @enderror"
                                                    id="variety5CertifiedSeed"
                                                    wire:model="area_under_certified_seed_multiplication.variety_5">
                                                @error('area_under_certified_seed_multiplication.variety_5')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="variety6CertifiedSeed" class="form-label">Area Under
                                                    Certified Seed Multiplication (Variety 6):</label>
                                                <input type="number"
                                                    class="form-control @error('area_under_certified_seed_multiplication.variety_6') is-invalid @enderror"
                                                    id="variety6CertifiedSeed"
                                                    wire:model="area_under_certified_seed_multiplication.variety_6">
                                                @error('area_under_certified_seed_multiplication.variety_6')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="variety7CertifiedSeed" class="form-label">Area Under
                                                    Certified Seed Multiplication (Variety 7):</label>
                                                <input type="number"
                                                    class="form-control @error('area_under_certified_seed_multiplication.variety_7') is-invalid @enderror"
                                                    id="variety7CertifiedSeed"
                                                    wire:model="area_under_certified_seed_multiplication.variety_7">
                                                @error('area_under_certified_seed_multiplication.variety_7')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>




                                </div>

                                <!-- Are You a Registered Seed Producer -->
                                <div class="mb-3" x-data="{
                                    is_registered_seed_producer: $wire.entangle('is_registered_seed_producer'),
                                }">

                                    <label class="form-label">Are You a Registered Seed Producer</label>
                                    <div class="@error('is_registered_seed_producer') border border-danger @enderror">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio"
                                                id="registeredSeedProducerYes" value="1"
                                                x-model="is_registered_seed_producer">
                                            <label class="form-check-label"
                                                for="registeredSeedProducerYes">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio"
                                                id="registeredSeedProducerNo" value="0"
                                                x-model="is_registered_seed_producer">
                                            <label class="form-check-label" for="registeredSeedProducerNo">No</label>
                                        </div>
                                    </div>

                                    @error('is_registered_seed_producer')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>


                                <!-- Registration Details (Seed Services Unit) -->
                                <div class="mb-3" x-data="{
                                    is_registered_seed_producer: $wire.entangle('is_registered_seed_producer'),
                                    registration_details: $wire.entangle('seed_service_unit_registration_details')
                                }" x-init="$watch('is_registered_seed_producer', (v) => {

                                    if (v != 1) {
                                        registration_details = {};
                                        $wire.resetValues('seed_service_unit_registration_details');
                                    }
                                });
                                $watch('registration_details', (v) => {

                                    $wire.registration_details = v;
                                });"
                                    x-show='is_registered_seed_producer == 1'>
                                    <label for="seedRegistrationDetails" class="form-label">Registration Details
                                        (Seed Services Unit)</label>
                                    <div class="mb-3">
                                        <label for="registrationNumber" class="form-label">Seed Service Unit
                                            Registration Number:</label>
                                        <input type="text"
                                            class="form-control  @error('seed_service_unit_registration_details.registration_number') is-invalid @enderror"
                                            id="registrationNumber"
                                            wire:model="seed_service_unit_registration_details.registration_number">
                                        @error('seed_service_unit_registration_details.registration_number')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="registrationDate" class="form-label">Seed Service Unit
                                            Registration Date:</label>
                                        <input type="date"
                                            class="form-control @error('seed_service_unit_registration_details.registration_date') is-invalid @enderror "
                                            id="registrationDate"
                                            wire:model="seed_service_unit_registration_details.registration_date">
                                        @error('seed_service_unit_registration_details.registration_date')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>

                                </div>

                                <!-- Do You Use Certified Seed -->
                                <div class="mb-3">
                                    <label class="form-label">Do You Use Certified Seed</label>
                                    <div class="@error('uses_certified_seed') border border-danger @enderror">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" id="useCertifiedSeedYes"
                                                value="1" wire:model="uses_certified_seed">
                                            <label class="form-check-label" for="useCertifiedSeedYes">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" id="useCertifiedSeedNo"
                                                value="0" wire:model="uses_certified_seed">
                                            <label class="form-check-label" for="useCertifiedSeedNo">No</label>
                                        </div>
                                    </div>
                                    @error('uses_certified_seed')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>

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
    @script
        <script>
            document.querySelectorAll('input[type="number"]').forEach(function(input) {
                input.setAttribute('step', '0.01');
            });
        </script>
    @endscript
</div>
