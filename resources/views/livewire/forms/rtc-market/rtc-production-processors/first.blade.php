<div class="mb-5 alert alert-warning" id="section-b" role="alert">
    <strong>SECTION A: RTC MARKETING </strong>
</div>

<!-- Date of Follow Up -->
<div class="mb-3">
    <label for="dateOfFollowUp" class="form-label">Date of Follow Up</label>
    <input type="date" class="form-control" id="dateOfFollowUp" wire:model='date_of_followup'>
</div>

<!-- Market Segment (Multiple Responses) -->
<div class="mb-3">
    <label for="marketSegment" class="form-label">Market Segment (Multiple
        Responses)</label>
    <div class="@error('market_segment') border border-danger @enderror">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="marketSegmentFresh" wire:model="market_segment"
                value="Fresh">
            <label class="form-check-label" for="marketSegmentFresh">Fresh</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="marketSegmentProcessed" value="Processed"
                wire:model="market_segment">
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
            <input class="form-check-input" type="radio" id="rtcMarketContractYes" value="1"
                x-model="has_rtc_market_contract">
            <label class="form-check-label" for="rtcMarketContractYes">Yes</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" id="rtcMarketContractNo" value="0"
                x-model="has_rtc_market_contract">
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
    <input type="number" min="0" step="any"
        class="form-control @error('total_vol_production_previous_season') is-invalid @enderror"
        id="totalVolumeProductions" wire:model='total_vol_production_previous_season'>
    @error('total_vol_production_previous_season')
        <x-error>{{ $message }}</x-error>
    @enderror
</div>


<!-- Total Value Production Previous Season (Financial Value-MWK) -->
<div class="mb-3 border shadow-none card card-body" x-data="{



}" x-init="() => {



}">
    <label for="totalValueProduction" class="my-3 form-label fw-bold">Total Value
        Production
        Previous Season (Financial Value-MWK)</label>


    <div class="mb-3">
        <label for="totalProductionValue" class="form-label"> Total Production
            Value Previous Season (Financial Value-MWK):
        </label>
        <input type="number" min="0" step="any"
            class="form-control  @error('total_production_value_previous_season.value') is-invalid @enderror"
            id="totalProductionValue" wire:model.blur="total_production_value_previous_season.value">
        @error('total_production_value_previous_season.value')
            <x-error>{{ $message }}</x-error>
        @enderror
    </div>

    <div class="mb-3">
        <label for="dateOfMaximumSales" class="form-label">Date of Maximum
            Sales:</label>
        <input type="date"
            class="form-control  @error('total_production_value_previous_season.date_of_maximum_sales') is-invalid @enderror"
            id="dateOfMaximumSales" wire:model.blur="total_production_value_previous_season.date_of_maximum_sales">
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
        <input type="number" min="0" step="any"
            class="form-control bg-light  @error('total_production_value_previous_season.total') is-invalid @enderror"
            readonly id="totalProductionValue" wire:model="total_production_value_previous_season.total">

    </div>


</div>

<!-- Total Volume of Production in Previous Season from Irrigation Farming (Metric Tonnes) -->
{{-- <div class="mb-3">
    <label for="totalVolumeIrrigation" class="form-label">Total Volume of
        Production in Previous Season from Irrigation Farming (Metric
        Tonnes)</label>
    <input type="number" min="0" step="any"
        class="form-control  @error('total_vol_irrigation_production_previous_season') is-invalid @enderror"
        id="totalVolumeIrrigation" wire:model='total_vol_irrigation_production_previous_season'>
    @error('total_vol_irrigation_production_previous_season')
        <x-error>{{ $message }}</x-error>
    @enderror
</div> --}}

{{-- <!-- Total Value of Irrigation Production in Previous Season (Financial Value-MWK) -->
<div class="mb-3 border shadow-none card card-body" x-data="{



}" x-init="() => {


}">

    <label for="totalValueIrrigation" class="my-3 form-label fw-bold">Total Value
        of Irrigation
        Production in Previous Season (Financial Value-MWK)</label>




    <div class="mb-3">
        <label for="totalProductionValue" class="form-label"> Total Irrigation Production
            Value Previous Season (Financial Value-MWK):
        </label>
        <input type="number" min="0" step="any"
            class="form-control  @error('total_irrigation_production_value_previous_season.value') is-invalid @enderror"
            id="totalProductionValue" wire:model.blur="total_irrigation_production_value_previous_season.value">
        @error('total_irrigation_production_value_previous_season.value')
            <x-error>{{ $message }}</x-error>
        @enderror
    </div>

    <div class="mb-3">
        <label for="dateOfMaximumSales" class="form-label">Date of Maximum
            Sales:</label>
        <input type="date"
            class="form-control  @error('total_irrigation_production_value_previous_season.date_of_maximum_sales') is-invalid @enderror"
            id="dateOfMaximumSales"
            wire:model.blur="total_irrigation_production_value_previous_season.date_of_maximum_sales">
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
        <input type="number" min="0" step="any"
            class="form-control bg-light  @error('total_irrigation_production_value_previous_season.total') is-invalid @enderror"
            readonly id="totalProductionValue" wire:model="total_irrigation_production_value_previous_season.total">

    </div>




</div> --}}


<!-- Sell RTC Products to Domestic Markets -->
<div class="mb-3" x-data="{
    sells_to_domestic_markets: $wire.entangle('sells_to_domestic_markets')
}">
    <label class="form-label">Do You Sell Your RTC Products to Domestic Markets</label>
    <div class=" @error('sells_to_domestic_markets') is-invalid @enderror">
        <div class="form-check">
            <input class="form-check-input" type="radio" id="sellToDomesticMarketsYes" value="1"
                x-model="sells_to_domestic_markets">
            <label class="form-check-label" for="sellToDomesticMarketsYes">Yes</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" id="sellToDomesticMarketsNo" value="0"
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
    <label class="form-label">Do You Sell Your Products to International Markets</label>
    <div class=" @error('sells_to_international_markets') border border-primary @enderror">
        <div class="form-check">
            <input class="form-check-input" type="radio" id="sellToInternationalMarketsYes" value="1"
                x-model="sells_to_international_markets">
            <label class="form-check-label" for="sellToInternationalMarketsYes">Yes</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" id="sellToInternationalMarketsNo" value="0"
                x-model="sells_to_international_markets">
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
            <input class="form-check-input" type="radio" id="sellThroughMarketInfoYes" value="1"
                x-model="uses_market_information_systems">
            <label class="form-check-label" for="sellThroughMarketInfoYes">Yes</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" id="sellThroughMarketInfoNo" value="0"
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

            <div class="border shadow-none card card-body">
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
                                    : ''"
                                    wire:model='market_information_systems.{{ $index }}.name'
                                    placeholder="Name of market information systems" />
                                @error('market_information_systems.' . $index . '.name')
                                    <x-error>{{ $message }}</x-error>
                                @enderror
                            </div>
                        </div>
                        <div class="col-2" x-data>
                            <button type="button" class="btn btn-theme-red"
                                @click="$wire.removeMIS({{ $index }})"
                                @if ($index == 0) disabled @endif>
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
                <input class="form-check-input" type="radio" id="aggregationCenterResponseYes" value="1"
                    wire:model='sells_to_aggregation_centers'>
                <label class="form-check-label">Yes</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" id="aggregationCenterResponseNo" value="0"
                    wire:model='sells_to_aggregation_centers'>
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

            <div class="border shadow-none card card-body">
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
                                    : ''"
                                    wire:model='aggregation_center_sales.{{ $index }}.name'
                                    placeholder="Name of aggregation center" />
                                @error('aggregation_center_sales.' . $index . '.name')
                                    <x-error>{{ $message }}</x-error>
                                @enderror
                            </div>
                        </div>
                        <div class="col-2" x-data>
                            <button type="button" class="btn btn-theme-red"
                                @click="$wire.removeSales({{ $index }})"
                                @if ($index == 0) disabled @endif>
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
    <input type="number" min="0" step="any"
        class="form-control @error('total_vol_aggregation_center_sales') is-invalid @enderror"
        wire:model='total_vol_aggregation_center_sales' id="" aria-describedby="helpId" placeholder="" />
    @error('total_vol_aggregation_center_sales')
        <x-error>{{ $message }}</x-error>
    @enderror
</div>
