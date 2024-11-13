<div>
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Add Data</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="/">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">Add Data</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- end page title -->

        <div class="row">

            <h3 class="mb-5 text-center text-warning">RTC PRODUCTION AND MARKETING (PROCESSORS) [FOLLOW UP]</h3>
            <div class="col">

                <x-alerts />



            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-8">



                <div class="card">
                    <div class="card-body">



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
                                        ({{ $recruit->id }})
                                        {{ $recruit->name_of_actor }} </option>
                                    @endforeach
                                </select>

                            </div>



                            <div class="hide" x-data="{
                                show: $wire.entangle('show')
                            }" :class="{ 'pe-none opacity-25': show === false }">


                                <div class="mb-3">
                                    <label for="" class="form-label">Date of follow up</label>
                                    <input type="date" readonly class="form-control bg-light @error('date_of_follow_up') is-invalid @enderror" wire:model='date_of_follow_up' />
                                    @error('date_of_follow_up')
                                    <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="" class="form-label">ENTERPRISE</label>
                                    <x-text-input style="background: #f8f9fa" disabled wire:model='location_data.enterprise' :class="$errors->has('location_data.enterprise') ? 'is-invalid' : ''" />
                                    @error('location_data.enterprise')
                                    <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="" class="form-label">DISTRICT</label>
                                    <select disabled style="background: #f8f9fa" class="form-select @error('location_data.district')
                                                is-invalid
                                            @enderror" wire:model='location_data.district'>
                                        @include('layouts.district-options')
                                    </select>
                                    @error('location_data.district')
                                    <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="" class="form-label">EPA</label>
                                    <x-text-input style="background: #f8f9fa" disabled wire:model='location_data.epa' :class="$errors->has('location_data.epa') ? 'is-invalid' : ''" />
                                    @error('location_data.epa')
                                    <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="" class="form-label">SECTION</label>
                                    <x-text-input style="background: #f8f9fa" disabled wire:model='location_data.section' :class="$errors->has('location_data.section') ? 'is-invalid' : ''" />
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
                                            <input class="form-check-input" type="checkbox" id="marketSegmentFresh" wire:model="market_segment" value="Fresh">
                                            <label class="form-check-label" for="marketSegmentFresh">Fresh</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="marketSegmentProcessed" value="Processed" wire:model="market_segment">
                                            <label class="form-check-label" for="marketSegmentProcessed">Processed</label>
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
                                            <input class="form-check-input" type="radio" id="rtcMarketContractYes" value="1" x-model="has_rtc_market_contract">
                                            <label class="form-check-label" for="rtcMarketContractYes">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" id="rtcMarketContractNo" value="0" x-model="has_rtc_market_contract">
                                            <label class="form-check-label" for="rtcMarketContractNo">No</label>
                                        </div>
                                    </div>
                                    @error('has_rtc_market_contract')
                                    <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>


                                <!-- Total Volume of Production in Previous Season (Metric Tonnes) -->
                                <div class="mb-3">
                                    <label for="totalVolumeProduction" class="form-label">Total Volume of
                                        Production in Previous Season (Metric Tonnes)</label>
                                    <input type="number" min="0" step="any" class="form-control @error('total_vol_production_previous_season') is-invalid @enderror" id="totalVolumeProductions" wire:model='total_vol_production_previous_season'>
                                    @error('total_vol_production_previous_season')
                                    <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>


                                <!-- Total Value Production Previous Season (Financial Value-MWK) -->
                                <div class="mb-3 card card-body shadow-none border" x-data="{



                                }" x-init="() => {



                                    }">
                                    <label for="totalValueProduction" class="my-3 form-label fw-bold">Total Value
                                        Production
                                        Previous Season (Financial Value-MWK)</label>


                                    <div class="mb-3">
                                        <label for="totalProductionValue" class="form-label"> Total Production
                                            Value Previous Season (Financial Value-MWK):
                                        </label>
                                        <input type="number" min="0" step="any" class="form-control  @error('total_production_value_previous_season.value') is-invalid @enderror" id="totalProductionValue" wire:model.live.debounce.600ms="total_production_value_previous_season.value">
                                        @error('total_production_value_previous_season.value')
                                        <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="dateOfMaximumSales" class="form-label">Date of Maximum
                                            Sales:</label>
                                        <input type="date" class="form-control  @error('total_production_value_previous_season.date_of_maximum_sales') is-invalid @enderror" id="dateOfMaximumSales" wire:model.live.debounce.600ms="total_production_value_previous_season.date_of_maximum_sales">
                                        @error('total_production_value_previous_season.date_of_maximum_sales')
                                        <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="dateOfMaximumSales" class="form-label"> USD Rate:
                                        </label>
                                        <x-text-input wire:model='total_production_value_previous_season.rate' class="bg-light" readonly />
                                    </div>
                                    <div class="mb-3">
                                        <label for="totalProductionValue" class="form-label">Financial Value
                                            ($)</label>
                                        <input type="number" min="0" step="any" class="form-control bg-light  @error('total_production_value_previous_season.total') is-invalid @enderror" readonly id="totalProductionValue" wire:model="total_production_value_previous_season.total">

                                    </div>


                                </div>



                                <!-- Sell RTC Products to Domestic Markets -->
                                <div class="mb-3" x-data="{
                                    sells_to_domestic_markets: $wire.entangle('sells_to_domestic_markets')
                                }">
                                    <label class="form-label">Do You Sell Your RTC Products to Domestic Markets</label>
                                    <div class=" @error('sells_to_domestic_markets') is-invalid @enderror">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" id="sellToDomesticMarketsYes" value="1" x-model="sells_to_domestic_markets">
                                            <label class="form-check-label" for="sellToDomesticMarketsYes">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" id="sellToDomesticMarketsNo" value="0" x-model="sells_to_domestic_markets">
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
                                    <div class=" @error('sells_to_international_markets') border border-primary @enderror">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" id="sellToInternationalMarketsYes" value="1" x-model="sells_to_international_markets">
                                            <label class="form-check-label" for="sellToInternationalMarketsYes">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" id="sellToInternationalMarketsNo" value="0" x-model="sells_to_international_markets">
                                            <label class="form-check-label" for="sellToInternationalMarketsNo">No</label>
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
                                    <div class=" @error('uses_market_information_systems') border border-danger @enderror">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" id="sellThroughMarketInfoYes" value="1" x-model="uses_market_information_systems">
                                            <label class="form-check-label" for="sellThroughMarketInfoYes">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" id="sellThroughMarketInfoNo" value="0" x-model="uses_market_information_systems">
                                            <label class="form-check-label" for="sellThroughMarketInfoNo">No</label>
                                        </div>
                                    </div>
                                    @error('uses_market_information_systems')
                                    <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>

                                <div class="mb-3" x-data="{
                                    uses_market_information_systems: $wire.entangle('uses_market_information_systems'),

                                }" x-init="$watch('uses_market_information_systems', (v) => {

                                    if (v == 0) {

                                        $wire.resetValues('market_information_systems');

                                    } else if (v == 1) {
                                        $wire.resetValues('market_information_systems');
                                        $wire.addMIS();
                                    }
                                });">



                                    <div class="mb-3" x-show='uses_market_information_systems == 1'>

                                        <div class="row">

                                            <div class="card card-body shadow-none border">
                                                <label for="" class="form-label">Specify Market Information
                                                    System</label>
                                                @foreach ($market_information_systems as $index => $value)
                                                <div class="row">
                                                    <label for="variety" class="my-3 form-label fw-bold">Market
                                                        information System
                                                        ({{ $index + 1 }})
                                                    </label>
                                                    <div class="col">

                                                        <div class="mb-3">

                                                            <x-text-input :class="$errors->has(
                                                                    'market_information_systems.' . $index . '.name',
                                                                )
                                                                    ? 'is-invalid'
                                                                    : ''" wire:model='market_information_systems.{{ $index }}.name' placeholder="Name of market information systems" />
                                                            @error('market_information_systems.' . $index . '.name')
                                                            <x-error>{{ $message }}</x-error>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-2" x-data>
                                                        <button type="button" class="btn btn-danger" @click="$wire.removeMIS({{ $index }})" @if ($index==0) disabled @endif>
                                                            -
                                                        </button>
                                                    </div>

                                                </div>
                                                @endforeach
                                                <div class="row">
                                                    <div class="col-2" x-data>

                                                        <button type="button" class="btn btn-warning" @click='$wire.addMIS()'>
                                                            +
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>



                                        </div>


                                    </div>


                                </div>


                                <!-- Sell RTC Produce Through Aggregation Centers -->

                                <div x-data="{
                                    sells_to_aggregation_centers: $wire.entangle('sells_to_aggregation_centers'),



                                }" x-init="$watch('sells_to_aggregation_centers', (v) => {

                                    if (v == 0) {

                                        $wire.resetValues('aggregation_center_sales');
                                    } else {
                                        $wire.resetValues('aggregation_center_sales');
                                        $wire.addSales();

                                    }
                                });">
                                    <div class="mb-3">
                                        <label for="sellThroughAggregationCenters" class="my-3 form-label ">Do
                                            You Sell RTC
                                            Produce Through Aggregation Centers</label>

                                        <div class=" @error('sells_to_aggregation_centers') border border-primary @enderror">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" id="aggregationCenterResponseYes" value="1" wire:model='sells_to_aggregation_centers'>
                                                <label class="form-check-label">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" id="aggregationCenterResponseNo" value="0" wire:model='sells_to_aggregation_centers'>
                                                <label class="form-check-label">No</label>
                                            </div>
                                        </div>

                                        @error('aggregation_centers')
                                        <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>



                                    <!-- Total Volume of RTC Sold Through Aggregation Centers in Previous Season (Metric Tonnes) -->
                                    <div class="mb-3" x-show='sells_to_aggregation_centers == 1'>

                                        <div class="row">

                                            <div class="card card-body shadow-none border">
                                                <label for="totalVolumeSoldThroughAggregation" class="form-label">Specify Aggregation Center</label>
                                                @foreach ($aggregation_center_sales as $index => $value)
                                                <div class="row">
                                                    <label for="variety" class="my-3 form-label fw-bold">Aggregation Center
                                                        ({{ $index + 1 }})
                                                    </label>
                                                    <div class="col">

                                                        <div class="mb-3">

                                                            <x-text-input :class="$errors->has(
                                                                    'aggregation_center_sales.' . $index . '.name',
                                                                )
                                                                    ? 'is-invalid'
                                                                    : ''" wire:model='aggregation_center_sales.{{ $index }}.name' placeholder="Name of aggregation center" />
                                                            @error('aggregation_center_sales.' . $index . '.name')
                                                            <x-error>{{ $message }}</x-error>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-2" x-data>
                                                        <button type="button" class="btn btn-danger" @click="$wire.removeSales({{ $index }})" @if ($index==0) disabled @endif>
                                                            -
                                                        </button>
                                                    </div>
                                                </div>
                                                @endforeach
                                                <div class="row">
                                                    <div class="col-2" x-data>

                                                        <button type="button" class="btn btn-warning" @click='$wire.addSales()'>
                                                            +
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>



                                        </div>


                                    </div>

                                </div>


                                <div class="mb-3">
                                    <label for="" class="form-label">Total Volume
                                        of RTC Sold Through Aggregation Centers in Previous Season (Metric
                                        Tonnes)</label>
                                    <input type="number" min="0" step="any" class="form-control @error('total_vol_aggregation_center_sales') is-invalid @enderror" wire:model='total_vol_aggregation_center_sales' id="" aria-describedby="helpId" placeholder="" />
                                    @error('total_vol_aggregation_center_sales')
                                    <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>

                                @include('livewire.forms.rtc-market.rtc-production-farmers.repeats')

                                <div class="d-grid col-12 justify-content-center" x-data>

                                    <button class="px-5 btn btn-warning " @click="window.scrollTo({
                                    top: 0,
                                    behavior: 'smooth'
                                })" type="submit">Submit</button>
                                </div>

                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>





    </div>

</div>
