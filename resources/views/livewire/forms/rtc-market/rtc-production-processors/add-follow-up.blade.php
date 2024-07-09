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
                            <strong> RTC PRODUCTION PROCESSORS (FOLLOW UP) </strong>
                        </div>
                        {{ var_export($errors) }}

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
                                <label for="" class="form-label">NAME OF ACTOR</label>
                                <x-text-input style="background: #f8f9fa" disabled wire:model='f_name' />

                            </div>
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
                                        class="form-select bg-light @error('group') is-invalid @enderror" model="group"
                                        disabled>
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
                                <!-- Market Segment (Multiple Responses) -->
                                <div class="mb-3">
                                    <label for="marketSegment" class="form-label">Market Segment (Multiple
                                        Responses)</label>
                                    <div class="@error('market_segment') border border-danger @enderror">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="marketSegmentFresh"
                                                wire:model="market_segment.fresh" value="YES">
                                            <label class="form-check-label" for="marketSegmentFresh">Fresh</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="marketSegmentProcessed"
                                                value="NO" wire:model="market_segment.processed">
                                            <label class="form-check-label"
                                                for="marketSegmentProcessed">Processed</label>
                                        </div>
                                    </div>
                                    @error('market_segment')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>

                                <!-- RTC Market Contractual Agreement -->
                                <div class="mb-3" x-data="{
                                    has_rtc_market_contract: $wire.entangle('has_rtc_market_contract')
                                }">
                                    <label class="form-label">Do You Have Any RTC Market Contractual Agreement</label>
                                    <div class="@error('has_rtc_market_contract') border border-primary @enderror">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" id="rtcMarketContractYes"
                                                value="1" x-model="has_rtc_market_contract">
                                            <label class="form-check-label" for="rtcMarketContractYes">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" id="rtcMarketContractNo"
                                                value="0" x-model="has_rtc_market_contract">
                                            <label class="form-check-label" for="rtcMarketContractNo">No</label>
                                        </div>
                                    </div>
                                    @error('has_rtc_market_contract')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror
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



                                <!-- Total Volume of Production in Previous Season (Metric Tonnes) -->
                                <div class="mb-3">
                                    <label for="totalVolumeProduction" class="form-label">Total Volume of
                                        Production in Previous Season (Metric Tonnes)</label>
                                    <input type="number"
                                        class="form-control @error('total_vol_production_previous_season') is-invalid @enderror"
                                        id="totalVolumeProductions" wire:model='total_vol_production_previous_season'>
                                    @error('total_vol_production_previous_season')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>

                                <!-- Total Value Production Previous Season (Financial Value-MWK) -->
                                <div class="mb-3">
                                    <label for="totalValueProduction" class="my-3 form-label fw-bold">Total Value
                                        Production
                                        Previous Season (Financial Value-MWK)</label>

                                    <div class="mb-3">
                                        <label for="totalProductionValue" class="form-label">Total Production
                                            Value Previous Season (Financial Value-MWK):</label>
                                        <input type="number"
                                            class="form-control  @error('total_production_value_previous_season.total') is-invalid @enderror"
                                            id="totalProductionValue"
                                            wire:model="total_production_value_previous_season.total">
                                        @error('total_production_value_previous_season.total')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="dateOfMaximumSales" class="form-label">Date of Maximum
                                            Sales:</label>
                                        <input type="date"
                                            class="form-control  @error('total_production_value_previous_season.date_of_maximum_sales') is-invalid @enderror"
                                            id="dateOfMaximumSales"
                                            wire:model="total_production_value_previous_season.date_of_maximum_sales">
                                        @error('total_production_value_previous_season.date_of_maximum_sales')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>

                                </div>

                                <!-- Total Volume of Production in Previous Season from Irrigation Farming (Metric Tonnes) -->
                                <div class="mb-3">
                                    <label for="totalVolumeIrrigation" class="form-label">Total Volume of
                                        Production in Previous Season from Irrigation Farming (Metric
                                        Tonnes)</label>
                                    <input type="number"
                                        class="form-control  @error('total_vol_irrigation_production_previous_season') is-invalid @enderror"
                                        id="totalVolumeIrrigation"
                                        wire:model='total_vol_irrigation_production_previous_season'>
                                    @error('total_vol_irrigation_production_previous_season')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>

                                <!-- Total Value of Irrigation Production in Previous Season (Financial Value-MWK) -->
                                <div class="mb-3">
                                    <label for="totalValueIrrigation" class="my-3 form-label fw-bold">Total Value
                                        of Irrigation
                                        Production in Previous Season (Financial Value-MWK)</label>
                                    <div class="mb-3">
                                        <label for="totalIrrigationProductionValue" class="form-label">Total
                                            Irrigation Production Value Previous Season:</label>
                                        <input type="number"
                                            class="form-control  @error('total_irrigation_production_value_previous_season.tota') is-invalid @enderror"
                                            id="totalIrrigationProductionValue"
                                            wire:model="total_irrigation_production_value_previous_season.total">
                                        @error('total_irrigation_production_value_previous_season.tota')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror

                                    </div>

                                    <div class="mb-3">
                                        <label for="dateOfMaximumSalesIrrigation" class="form-label">Date of
                                            Maximum Sales (Irrigation):</label>
                                        <input type="date"
                                            class="form-control  @error('total_irrigation_production_value_previous_season.date_of_maximum_sales') is-invalid @enderror"
                                            id="dateOfMaximumSalesIrrigation"
                                            wire:model="total_irrigation_production_value_previous_season.date_of_maximum_sales">

                                        @error('total_irrigation_production_value_previous_season.date_of_maximum_sales')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>

                                </div>

                                <!-- Sell RTC Products to Domestic Markets -->
                                <div class="mb-3" x-data="{
                                    sells_to_domestic_markets: $wire.entangle('sells_to_domestic_markets')
                                }">
                                    <label class="form-label">Do You Sell Your RTC Products to Domestic Markets</label>
                                    <div class=" @error('sells_to_domestic_markets') is-invalid @enderror">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio"
                                                id="sellToDomesticMarketsYes" value="1"
                                                x-model="sells_to_domestic_markets">
                                            <label class="form-check-label" for="sellToDomesticMarketsYes">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio"
                                                id="sellToDomesticMarketsNo" value="0"
                                                x-model="sells_to_domestic_markets">
                                            <label class="form-check-label" for="sellToDomesticMarketsNo">No</label>
                                        </div>
                                    </div>

                                    @error('sells_to_domestic_markets')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>

                                <!-- Sell Products to International Markets -->
                                <div class="mb-3" x-data="{
                                    sells_to_international_markets: $wire.entangle('sells_to_international_markets')
                                }">
                                    <label class="form-label">Do You Sell Your Products to International
                                        Markets</label>
                                    <div
                                        class=" @error('sells_to_international_markets') border border-primary @enderror">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio"
                                                id="sellToInternationalMarketsYes" value="1"
                                                x-model="sells_to_international_markets">
                                            <label class="form-check-label"
                                                for="sellToInternationalMarketsYes">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio"
                                                id="sellToInternationalMarketsNo" value="0"
                                                x-model="sells_to_international_markets">
                                            <label class="form-check-label"
                                                for="sellToInternationalMarketsNo">No</label>
                                        </div>
                                    </div>
                                    @error('sells_to_international_markets')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>

                                <!-- Sell Products Through Market Information Systems -->
                                <div class="mb-3" x-data="{
                                    uses_market_information_systems: $wire.entangle('uses_market_information_systems')
                                }">
                                    <label class="form-label">Do You Sell Your Products Through Market Information
                                        Systems</label>
                                    <div
                                        class=" @error('uses_market_information_systems') border border-danger @enderror">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio"
                                                id="sellThroughMarketInfoYes" value="1"
                                                x-model="uses_market_information_systems">
                                            <label class="form-check-label" for="sellThroughMarketInfoYes">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio"
                                                id="sellThroughMarketInfoNo" value="0"
                                                x-model="uses_market_information_systems">
                                            <label class="form-check-label" for="sellThroughMarketInfoNo">No</label>
                                        </div>
                                    </div>
                                    @error('uses_market_information_systems')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>

                                <div class="mb-3" x-data="{
                                    uses_market_information_systems: $wire.entangle('uses_market_information_systems'),
                                    market_information_systems: $wire.entangle('market_information_systems')
                                }" x-init="$watch('uses_market_information_systems', (v) => {

                                    if (v != 1) {
                                        market_information_systems = {};
                                        $wire.resetValues('market_information_systems');
                                    }
                                });

                                $watch('market_information_systems', (v) => {

                                    $wire.market_information_systems = v;

                                });"
                                    x-show='uses_market_information_systems == 1'>
                                    <label for="" class="form-label">Specify Market Information System</label>
                                    <input type="text"
                                        class="form-control  @error('market_information_systems') is-invalid @enderror"
                                        name="" id="" aria-describedby="helpId" placeholder=""
                                        x-model='market_information_systems' />
                                    @error('market_information_systems')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>


                                <!-- Sell RTC Produce Through Aggregation Centers -->

                                <div x-data="{
                                    aggregation_centers: $wire.entangle('aggregation_centers'),
                                    aggregation_center_sales: $wire.entangle('aggregation_center_sales'),


                                }" x-init="$watch('aggregation_centers', (v) => {

                                    if (v.response != 1) {
                                        aggregation_centers.specify = '';
                                        aggregation_center_sales = null;
                                        $wire.resetValues('aggregation_center_sales');
                                        $wire.resetValues('aggregation_centers');
                                    }
                                });

                                $watch('aggregation_center_sales', (v) => {



                                    $wire.aggregation_center_sales = v;


                                });">
                                    <div class="mb-3">
                                        <label for="sellThroughAggregationCenters" class="my-3 form-label ">Do
                                            You Sell RTC
                                            Produce Through Aggregation Centers</label>

                                        <div class=" @error('aggregation_centers') border border-primary @enderror">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio"
                                                    id="aggregationCenterResponseYes" value="1"
                                                    wire:model='aggregation_centers.response'
                                                    x-model="aggregation_centers.response">
                                                <label class="form-check-label">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio"
                                                    id="aggregationCenterResponseNo" value="0"
                                                    wire:model='aggregation_centers.response'
                                                    x-model="aggregation_centers.response">
                                                <label class="form-check-label">No</label>
                                            </div>
                                        </div>

                                        @error('aggregation_centers')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>

                                    <div class="mb-3" x-show='aggregation_centers.response == 1'>
                                        <label for="aggregationCenterSpecify" class="form-label">Aggregation
                                            Centers Specify:</label>
                                        <input type="text"
                                            class="form-control  @error('aggregation_centers.specify') is-invalid @enderror"
                                            wire:model="aggregation_centers.specify">
                                        @error('aggregation_centers.specify')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>

                                    <!-- Total Volume of RTC Sold Through Aggregation Centers in Previous Season (Metric Tonnes) -->
                                    <div class="mb-3" x-show='aggregation_centers.response == 1'>
                                        <label for="totalVolumeSoldThroughAggregation" class="form-label">Total Volume
                                            of RTC Sold Through Aggregation Centers in Previous Season (Metric
                                            Tonnes)</label>
                                        <input type="number"
                                            class="form-control  @error('aggregation_center_sales') is-invalid @enderror"
                                            id="totalVolumeSoldThroughAggregation" x-model='aggregation_center_sales'>

                                        @error('aggregation_center_sales')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>

                                </div>
                                @include('livewire.forms.rtc-market.rtc-production-processors.repeats')

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
