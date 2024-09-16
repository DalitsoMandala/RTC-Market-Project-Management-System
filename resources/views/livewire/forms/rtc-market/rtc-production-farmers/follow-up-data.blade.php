<div class="alert alert-primary" id="section-b" role="alert">
    <strong>SECTION A: RTC PRODUCTION </strong>
</div>

<!-- Area Under Cultivation (Number of Acres) by Variety -->
<div class="mb-3">
    <label for="areaUnderCultivation" class="my-3 form-label fw-bold">Area Under
        Cultivation
        (Number of Acres) by Variety</label>



    <div class="row">
        <div class="card card-body shadow-none border">
            @foreach ($area_under_cultivation as $index => $value)
            <div class="row">
                <label for="variety" class="my-3 form-label fw-bold">VARIETY
                    ({{ $index + 1 }})
                </label>
                <div class="col">

                    <div class="mb-3">

                        <x-text-input :class="$errors->has('area_under_cultivation.' . $index . '.variety')
                                ? 'is-invalid'
                                : ''" wire:model='area_under_cultivation.{{ $index }}.variety' placeholder="variety" />
                        @error('area_under_cultivation.' . $index . '.variety')
                        <x-error>{{ $message }}</x-error>
                        @enderror
                    </div>
                </div>
                <div class="col">
                    <div class="mb-3">
                        <x-text-input type="number" min="0" step="any" :class="$errors->has('area_under_cultivation.' . $index . '.area') ? 'is-invalid' : ''" wire:model='area_under_cultivation.{{ $index }}.area' placeholder="area" />
                        @error('area_under_cultivation.' . $index . '.area')
                        <x-error>{{ $message }}</x-error>
                        @enderror
                    </div>
                </div>


                <div class="col-2" x-data> <button type="button" class="btn btn-danger" @click="$wire.removeAreaofCultivation({{ $index }})" @if ($index==0) disabled @endif>
                        -
                    </button>
                </div>
            </div>
            @endforeach
            <div class="row">
                <div class="col-2" x-data>

                    <button type="button" class="btn btn-primary" @click='$wire.addAreaofCultivation()'>
                        +
                    </button>
                </div>
            </div>
        </div>



    </div>


</div>


<!-- Number of Plantlets Produced -->
<div class="mb-3" x-data="{
    group: $wire.entangle('group'),
    number_of_plantlets_produced: $wire.entangle('number_of_plantlets_produced')
}" x-init="$watch('group', (v) => {
    if (v != 'Early generation seed producer') {

        $wire.resetValues('number_of_plantlets_produced');
    }
});" x-show="group=='Early generation seed producer'">

    <label for="numberOfPlantlets" class="my-3 form-label fw-bold">Number of
        Plantlets
        Produced</label>
    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <label for="cassavaPlantlets" class="form-label">Number of
                    Plantlets Produced (Cassava):</label>
                <input type="number" min="0" step="any" class="form-control @error('number_of_plantlets_produced.cassava') is-invalid @enderror" id="cassavaPlantlets" x-model="number_of_plantlets_produced.cassava">
                @error('number_of_plantlets_produced.cassava')
                <x-error>{{ $message }}</x-error>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="potatoPlantlets" class="form-label">Number of
                    Plantlets Produced (Potato):</label>
                <input type="number" min="0" step="any" class="form-control @error('number_of_plantlets_produced.potato') is-invalid @enderror" id="potatoPlantlets" x-model="number_of_plantlets_produced.potato">
                @error('number_of_plantlets_produced.potato')
                <x-error>{{ $message }}</x-error>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="sweetPotatoPlantlets" class="form-label">Number of
                    Plantlets Produced (Sweet Potato):</label>
                <input type="number" min="0" step="any" class="form-control @error('number_of_plantlets_produced.sweet_potato') is-invalid @enderror" id="sweetPotatoPlantlets" x-model="number_of_plantlets_produced.sweet_potato">
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
            if (v != 'Early generation seed producer') {
                $wire.resetValues('number_of_screen_house_vines_harvested');
            }
        });
    }

}" x-show="group=='Early generation seed producer'">
    <label for="numberOfScreenHouseVines" class="form-label">Number of Screen
        House Vines Harvested (Sweet Potatoes)</label>
    <input type="number" min="0" step="any" class="form-control @error('number_of_screen_house_vines_harvested') is-invalid @enderror" id="numberOfScreenHouseVines" x-model='number_of_screen_house_vines_harvested'>
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
            if (v != 'Early generation seed producer') {
                $wire.resetValues('number_of_screen_house_min_tubers_harvested');
            }
        });
    }
}" x-show="group=='Early generation seed producer' ">
    <label for="numberOfMiniTubers" class="form-label">Number of Screen House
        Mini-Tubers Harvested (Potato)</label>
    <input type="number" min="0" step="any" class="form-control @error('number_of_screen_house_min_tubers_harvested') is-invalid @enderror" id="numberOfMiniTubers" x-model='number_of_screen_house_min_tubers_harvested'>
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
            if (v != 'Early generation seed producer') {
                $wire.resetValues('number_of_sah_plants_produced');
            }
        });
    }
}" x-show=" group=='Early generation seed producer'">
    <label for="numberOfSAHPlants" class="form-label">Number of SAH Plants
        Produced (Cassava)</label>
    <input type="number" min="0" step="any" class="form-control @error('number_of_sah_plants_produced') is-invalid @enderror" id="numberOfSAHPlants" x-model='number_of_sah_plants_produced'>
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
            if (v == 'Early generation seed producer') {
                $wire.resetValues('area_under_basic_seed_multiplication');
                $wire.addBasicSeed();

            } else {
                $wire.resetValues('area_under_basic_seed_multiplication');
            }
        });


    }
}" x-show="group=='Early generation seed producer'">
    <label for="areaUnderBasicSeed" class="my-3 form-label fw-bold">Area Under
        Basic Seed
        Multiplication (Number of Acres)</label>



    <div class="row">
        <div class="card card-body shadow-none border">
            @foreach ($area_under_basic_seed_multiplication as $index => $value)
            <div class="row">
                <label for="variety" class="my-3 form-label fw-bold">vARIETY
                    ({{ $index + 1 }})
                </label>
                <div class="col">

                    <div class="mb-3">

                        <x-text-input :class="$errors->has('area_under_basic_seed_multiplication.' . $index . '.variety')
                                ? 'is-invalid'
                                : ''" wire:model='area_under_basic_seed_multiplication.{{ $index }}.variety' placeholder="variety" />
                        @error('area_under_basic_seed_multiplication.' . $index . '.variety')
                        <x-error>{{ $message }}</x-error>
                        @enderror
                    </div>
                </div>
                <div class="col">
                    <div class="mb-3">
                        <x-text-input type="number" min="0" step="any" :class="$errors->has('area_under_basic_seed_multiplication.' . $index . '.area')
                                ? 'is-invalid'
                                : ''" wire:model='area_under_basic_seed_multiplication.{{ $index }}.area' placeholder="area" />
                        @error('area_under_basic_seed_multiplication.' . $index . '.area')
                        <x-error>{{ $message }}</x-error>
                        @enderror
                    </div>
                </div>
                <div class="col-2" x-data>
                    <button type="button" class="btn btn-danger" @click="$wire.removeBasicSeed({{ $index }})" @if ($index==0) disabled @endif>
                        -
                    </button>
                </div>
            </div>
            @endforeach
            <div class="row">
                <div class="col-2" x-data>

                    <button type="button" class="btn btn-primary" @click='$wire.addBasicSeed()'>
                        +
                    </button>
                </div>
            </div>
        </div>



    </div>


</div>

<!-- Area Under Certified Seed Multiplication -->
<div class="mb-3">
    <label for="areaUnderCertifiedSeed" class="my-3 form-label fw-bold">Area Under
        Certified
        Seed Multiplication</label>
    <div class="row">
        <div class="card card-body shadow-none border">
            @foreach ($area_under_certified_seed_multiplication as $index => $value)
            <div class="row">
                <label for="variety" class="my-3 form-label fw-bold">vARIETY
                    ({{ $index + 1 }})
                </label>
                <div class="col">

                    <div class="mb-3">

                        <x-text-input :class="$errors->has('area_under_certified_seed_multiplication.' . $index . '.variety')
                                ? 'is-invalid'
                                : ''" wire:model='area_under_certified_seed_multiplication.{{ $index }}.variety' placeholder="variety" />
                        @error('area_under_certified_seed_multiplication.' . $index . '.variety')
                        <x-error>{{ $message }}</x-error>
                        @enderror
                    </div>
                </div>
                <div class="col">
                    <div class="mb-3">
                        <x-text-input type="number" min="0" step="any" :class="$errors->has('area_under_certified_seed_multiplication.' . $index . '.area')
                                ? 'is-invalid'
                                : ''" wire:model='area_under_certified_seed_multiplication.{{ $index }}.area' placeholder="area" />
                        @error('area_under_certified_seed_multiplication.' . $index . '.area')
                        <x-error>{{ $message }}</x-error>
                        @enderror
                    </div>
                </div>
                <div class="col-2" x-data>
                    <button type="button" class="btn btn-danger" @click="$wire.removeCertifiedSeed({{ $index }})" @if ($index==0) disabled @endif>
                        -
                    </button>
                </div>

            </div>
            @endforeach
            <div class="row">
                <div class="col-2" x-data>

                    <button type="button" class="btn btn-primary" @click='$wire.addCertifiedSeed()'>
                        +
                    </button>
                </div>
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
            <input class="form-check-input" type="radio" id="registeredSeedProducerYes" value="1" x-model="is_registered_seed_producer">
            <label class="form-check-label" for="registeredSeedProducerYes">Yes</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" id="registeredSeedProducerNo" value="0" x-model="is_registered_seed_producer">
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

        $wire.resetValues('seed_service_unit_registration_details');
    }
});" x-show='is_registered_seed_producer == 1'>
    <label for="seedRegistrationDetails" class="form-label">Registration Details
        (Seed Services Unit)</label>
    <div class="mb-3">
        <label for="registrationNumber" class="form-label">Seed Service Unit
            Registration Number:</label>
        <input type="text" class="form-control  @error('seed_service_unit_registration_details.registration_number') is-invalid @enderror" id="registrationNumber" wire:model="seed_service_unit_registration_details.registration_number">
        @error('seed_service_unit_registration_details.registration_number')
        <x-error>{{ $message }}</x-error>
        @enderror
    </div>

    <div class="mb-3">
        <label for="registrationDate" class="form-label">Seed Service Unit
            Registration Date:</label>
        <input type="date" class="form-control @error('seed_service_unit_registration_details.registration_date') is-invalid @enderror " id="registrationDate" wire:model="seed_service_unit_registration_details.registration_date">
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
            <input class="form-check-input" type="radio" id="useCertifiedSeedYes" value="1" wire:model="uses_certified_seed">
            <label class="form-check-label" for="useCertifiedSeedYes">Yes</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" id="useCertifiedSeedNo" value="0" wire:model="uses_certified_seed">
            <label class="form-check-label" for="useCertifiedSeedNo">No</label>
        </div>
    </div>
    @error('uses_certified_seed')
    <x-error>{{ $message }}</x-error>
    @enderror
</div>


<div class="mb-5 alert alert-primary" id="section-c" role="alert">
    <strong>SECTION B: RTC MARKETING </strong>
</div>
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
        <label for="totalProductionValue" class="form-label">Financial Value ($)</label>
        <input type="number" min="0" step="any" class="form-control bg-light  @error('total_production_value_previous_season.total') is-invalid @enderror" readonly id="totalProductionValue" wire:model="total_production_value_previous_season.total">

    </div>


</div>

<!-- Total Volume of Production in Previous Season from Irrigation Farming (Metric Tonnes) -->
<div class="mb-3">
    <label for="totalVolumeIrrigation" class="form-label">Total Volume of
        Production in Previous Season from Irrigation Farming (Metric
        Tonnes)</label>
    <input type="number" min="0" step="any" class="form-control  @error('total_vol_irrigation_production_previous_season') is-invalid @enderror" id="totalVolumeIrrigation" wire:model='total_vol_irrigation_production_previous_season'>
    @error('total_vol_irrigation_production_previous_season')
    <x-error>{{ $message }}</x-error>
    @enderror
</div>

<!-- Total Value of Irrigation Production in Previous Season (Financial Value-MWK) -->
<div class="mb-3 card card-body shadow-none border" x-data="{



}" x-init="() => {


}">

    <label for="totalValueIrrigation" class="my-3 form-label fw-bold">Total Value
        of Irrigation
        Production in Previous Season (Financial Value-MWK)</label>




    <div class="mb-3">
        <label for="totalProductionValue" class="form-label"> Total Irrigation Production
            Value Previous Season (Financial Value-MWK):
        </label>
        <input type="number" min="0" step="any" class="form-control  @error('total_irrigation_production_value_previous_season.value') is-invalid @enderror" id="totalProductionValue" wire:model.live.debounce.600ms="total_irrigation_production_value_previous_season.value">
        @error('total_irrigation_production_value_previous_season.value')
        <x-error>{{ $message }}</x-error>
        @enderror
    </div>

    <div class="mb-3">
        <label for="dateOfMaximumSales" class="form-label">Date of Maximum
            Sales:</label>
        <input type="date" class="form-control  @error('total_irrigation_production_value_previous_season.date_of_maximum_sales') is-invalid @enderror" id="dateOfMaximumSales" wire:model.live.debounce.600ms="total_irrigation_production_value_previous_season.date_of_maximum_sales">
        @error('total_irrigation_production_value_previous_season.date_of_maximum_sales')
        <x-error>{{ $message }}</x-error>
        @enderror
    </div>
    <div class="mb-3">
        <label for="dateOfMaximumSales" class="form-label"> USD Rate:
        </label>
        <x-text-input wire:model='total_irrigation_production_value_previous_season.rate' class="bg-light" readonly />
    </div>
    <div class="mb-3">
        <label for="totalProductionValue" class="form-label">Financial Value ($)</label>
        <input type="number" min="0" step="any" class="form-control bg-light  @error('total_irrigation_production_value_previous_season.total') is-invalid @enderror" readonly id="totalProductionValue" wire:model="total_irrigation_production_value_previous_season.total">

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
    <label class="form-label">Do You Sell Your Products to International Markets</label>
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
    <label class="form-label">Do You Sell Your Products Through Market Information Systems</label>
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
                <label for="" class="form-label">Specify Market Information System</label>
                @foreach ($market_information_systems as $index => $value)
                <div class="row">
                    <label for="variety" class="my-3 form-label fw-bold">Market information System
                        ({{ $index + 1 }})
                    </label>
                    <div class="col">

                        <div class="mb-3">

                            <x-text-input :class="$errors->has('market_information_systems.' . $index . '.name')
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

                        <button type="button" class="btn btn-primary" @click='$wire.addMIS()'>
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

                            <x-text-input :class="$errors->has('aggregation_center_sales.' . $index . '.name')
                                    ? 'is-invalid'
                                    : ''" wire:model='aggregation_center_sales.{{ $index }}.name' placeholder="Name of aggregation center" />
                            @error('aggregation_center_sales.' . $index . '.name')
                            <x-error>{{ $message }}</x-error>
                            @enderror
                        </div>
                    </div>


                    <div class="col-2" x-data> <button type="button" class="btn btn-danger" @click="$wire.removeSales({{ $index }})" @if ($index==0) disabled @endif>
                            -
                        </button>
                    </div>
                </div>
                @endforeach
                <div class="row">
                    <div class="col-2" x-data>

                        <button type="button" class="btn btn-primary" @click='$wire.addSales()'>
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
