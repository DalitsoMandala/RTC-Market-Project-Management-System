<div class="mb-5 alert alert-primary" id="section-c" role="alert">
    <strong>SECTION C: RTC MARKETING (FOLLOW UP)</strong>
</div>

<div class="mb-3">
    <label for="marketSegment" class="form-label">Market Segment (Multiple
        Responses)</label>

    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="marketSegmentFresh" wire:model="f_market_segment.fresh"
            value="YES">
        <label class="form-check-label" for="marketSegmentFresh">Fresh</label>
    </div>
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="marketSegmentProcessed" value="NO"
            wire:model="f_market_segment.processed">
        <label class="form-check-label" for="marketSegmentProcessed">Processed</label>
    </div>

</div>

<!-- RTC Market Contractual Agreement -->
<div class="mb-3">
    <label for="rtcMarketContract" class="form-label">Do You Have Any RTC Market
        Contractual Agreement</label>
    <select class="form-select" id="rtcMarketContract" wire:model='f_has_rtc_market_contract'>

        <option value="1">Yes</option>
        <option value="0">No</option>
    </select>
</div>

<!-- Total Volume of Production in Previous Season (Metric Tonnes) -->
<div class="mb-3">
    <label for="totalVolumeProduction" class="form-label">Total Volume of
        Production in Previous Season (Metric Tonnes)</label>
    <input type="number" min="0" step="any" class="form-control" id="totalVolumeProduction"
        wire:model='f_total_vol_production_previous_season'>
</div>

<!-- Total Value Production Previous Season (Financial Value-MWK) -->
<div class="mb-3">
    <label for="totalValueProduction" class="my-3 form-label fw-bold">Total Value
        Production
        Previous Season (Financial Value-MWK)</label>
    <div class="mb-3">
        <label for="totalProductionValue" class="form-label">Total Production
            Value Previous Season (Financial Value-MWK):</label>
        <input type="number" min="0" step="any" class="form-control" id="totalProductionValue"
            wire:model="f_total_production_value_previous_season.total">
    </div>

    <div class="mb-3">
        <label for="dateOfMaximumSales" class="form-label">Date of Maximum
            Sales:</label>
        <input type="date" class="form-control" id="dateOfMaximumSales"
            wire:model="f_total_production_value_previous_season.date_of_maximum_sales">
    </div>

</div>

<!-- Total Volume of Production in Previous Season from Irrigation Farming (Metric Tonnes) -->
<div class="mb-3">
    <label for="totalVolumeIrrigation" class="form-label">Total Volume of
        Production in Previous Season from Irrigation Farming (Metric
        Tonnes)</label>
    <input type="number" min="0" step="any" class="form-control" id="totalVolumeIrrigation"
        wire:model='f_total_irrigation_production_previous_season'>
</div>

<!-- Total Value of Irrigation Production in Previous Season (Financial Value-MWK) -->
<div class="mb-3">
    <label for="totalValueIrrigation" class="my-3 form-label fw-bold">Total Value
        of Irrigation
        Production in Previous Season (Financial Value-MWK)</label>
    <div class="mb-3">
        <label for="totalIrrigationProductionValue" class="form-label">Total
            Irrigation Production Value Previous Season:</label>
        <input type="number" min="0" step="any" class="form-control" id="totalIrrigationProductionValue"
            wire:model="f_total_irrigation_production_value_previous_season.total">
    </div>

    <div class="mb-3">
        <label for="dateOfMaximumSalesIrrigation" class="form-label">Date of
            Maximum Sales (Irrigation):</label>
        <input type="date" class="form-control" id="dateOfMaximumSalesIrrigation"
            wire:model="f_total_irrigation_production_value_previous_season.date_of_maximum_sales">
    </div>

</div>

<!-- Sell RTC Products to Domestic Markets -->
<div class="mb-3">
    <label for="sellToDomesticMarkets" class="form-label">Do You Sell Your RTC
        Products to Domestic Markets</label>
    <select class="form-select" id="sellToDomesticMarkets" wire:model='f_sells_to_domestic_markets'>

        <option value="1">Yes</option>
        <option value="0">No</option>
    </select>
</div>

<!-- Sell Products to International Markets -->
<div class="mb-3">
    <label for="sellToInternationalMarkets" class="form-label">Do You Sell Your
        Products to International Markets</label>
    <select class="form-select" id="sellToInternationalMarkets" wire:model='f_sells_to_international_markets'>

        <option value="1">Yes</option>
        <option value="0">No</option>
    </select>
</div>

<!-- Sell Products Through Market Information Systems -->
<div class="mb-3">
    <label for="sellThroughMarketInfo" class="form-label">Do You Sell Your
        Products Through Market Information Systems</label>
    <select class="form-select" id="sellThroughMarketInfo" wire:model='f_uses_market_information_systems'>

        <option value="1">Yes</option>
        <option value="0">No</option>
    </select>
</div>

<div class="mb-3">
    <label for="" class="form-label">Specify MIS</label>
    <input type="text" class="form-control" name="" id="" aria-describedby="helpId"
        placeholder="" wire:model='f_market_information_systems' />

</div>


<!-- Sell RTC Produce Through Aggregation Centers -->
<div class="mb-3">
    <label for="sellThroughAggregationCenters" class="my-3 form-label fw-bold">Do
        You Sell RTC
        Produce Through Aggregation Centers</label>
    <div class="mb-3">
        <label for="aggregationCenterResponse" class="form-label">Aggregation
            Centers Response:</label>
        <input type="text" class="form-control" id="aggregationCenterResponse"
            wire:model="f_aggregation_centers.response">
    </div>

    <div class="mb-3">
        <label for="aggregationCenterSpecify" class="form-label">Aggregation
            Centers Specify:</label>
        <input type="text" class="form-control" id="aggregationCenterSpecify"
            wire:model="f_aggregation_centers.specify">
    </div>

</div>

<!-- Total Volume of RTC Sold Through Aggregation Centers in Previous Season (Metric Tonnes) -->
<div class="mb-3">
    <label for="totalVolumeSoldThroughAggregation" class="form-label">Total
        Volume
        of RTC Sold Through Aggregation Centers in Previous Season (Metric
        Tonnes)</label>
    <input type="number" min="0" step="any" class="form-control" id="totalVolumeSoldThroughAggregation"
        wire:model='f_aggregation_center_sales'>
</div>
