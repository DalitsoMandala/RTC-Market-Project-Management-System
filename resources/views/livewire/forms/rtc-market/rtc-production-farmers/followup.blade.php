<div class="mb-5 alert alert-warning" id="section-d" role="alert">
    <strong>SECTION D: ABOUT RTC PRODUCTION (FOLLOW UP)</strong>
</div>
<!-- Group Name -->
<div class="mb-3">
    <label for="groupName" class="form-label">Group Name</label>
    <input type="text" class="form-control" id="groupName" wire:model='f_location_data.group_name'>
</div>

<!-- District -->
<div class="mb-3">
    <label for="district" class="form-label">District</label>
    <select class="form-select" wire:model='f_location_data.district'>
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
</div>

<!-- EPA -->
<div class="mb-3">
    <label for="epa" class="form-label">EPA</label>
    <input type="text" class="form-control" id="epa" wire:model='f_location_data.epa'>
</div>

<!-- SECTION -->
<div class="mb-3">
    <label for="epa" class="form-label">SECTION</label>
    <input type="text" class="form-control" id="section" wire:model='f_location_data.section'>
</div>


<!-- Enterprise -->
<div class="mb-3">
    <label for="enterprise" class="form-label">Enterprise</label>
    <input type="text" class="form-control" id="enterprise" wire:model='f_location_data.enterprise'>
</div>
'
<!-- Date of Follow Up -->
<div class="mb-3">
    <label for="dateOfFollowUp" class="form-label">Date of Follow Up</label>
    <input type="date" class="form-control" id="dateOfFollowUp" wire:model='f_date_of_follow_up'>
</div>


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
                <input type="number" class="form-control" id="areaUnderCultivationVariety1"
                    wire:model="f_area_under_cultivation.variety_1">
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="areaUnderCultivationVariety2" class="form-label">Area
                    Under Cultivation (Variety 2):</label>
                <input type="number" class="form-control" id="areaUnderCultivationVariety2"
                    wire:model="f_area_under_cultivation.variety_2">
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="areaUnderCultivationVariety3" class="form-label">Area
                    Under Cultivation (Variety 3):</label>
                <input type="number" class="form-control" id="areaUnderCultivationVariety3"
                    wire:model="f_area_under_cultivation.variety_3">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <label for="areaUnderCultivationVariety4" class="form-label">Area
                    Under Cultivation (Variety 4):</label>
                <input type="number" class="form-control" id="areaUnderCultivationVariety4"
                    wire:model="f_area_under_cultivation.variety_4">
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="areaUnderCultivationVariety5" class="form-label">Area
                    Under Cultivation (Variety 5):</label>
                <input type="number" class="form-control" id="areaUnderCultivationVariety5"
                    wire:model="f_area_under_cultivation.variety_5">
            </div>
        </div>
    </div>
</div>

<!-- Number of Plantlets Produced -->
<div class="mb-3">
    <label for="numberOfPlantlets" class="my-3 form-label fw-bold">Number of
        Plantlets
        Produced</label>
    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <label for="cassavaPlantlets" class="form-label">Number of
                    Plantlets Produced (Cassava):</label>
                <input type="number" class="form-control" id="cassavaPlantlets"
                    wire:model="f_number_of_plantlets_produced.cassava">
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="potatoPlantlets" class="form-label">Number of
                    Plantlets Produced (Potato):</label>
                <input type="number" class="form-control" id="potatoPlantlets"
                    wire:model="f_number_of_plantlets_produced.potato">
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="sweetPotatoPlantlets" class="form-label">Number of
                    Plantlets Produced (Sweet Potato):</label>
                <input type="number" class="form-control" id="sweetPotatoPlantlets"
                    wire:model="f_number_of_plantlets_produced.sweet_potato">
            </div>
        </div>
    </div>

</div>

<!-- Number of Screen House Vines Harvested (Sweet Potatoes) -->
<div class="mb-3">
    <label for="numberOfScreenHouseVines" class="form-label">Number of Screen
        House Vines Harvested (Sweet Potatoes)</label>
    <input type="number" class="form-control" id="numberOfScreenHouseVines"
        wire:model='f_number_of_screen_house_vines_harvested'>
</div>

<!-- Number of Screen House Mini-Tubers Harvested (Potato) -->
<div class="mb-3">
    <label for="numberOfMiniTubers" class="form-label">Number of Screen House
        Mini-Tubers Harvested (Potato)</label>
    <input type="number" class="form-control" id="numberOfMiniTubers"
        wire:model='f_number_of_screen_house_min_tubers_harvested'>
</div>

<!-- Number of SAH Plants Produced (Cassava) -->
<div class="mb-3">
    <label for="numberOfSAHPlants" class="form-label">Number of SAH Plants
        Produced (Cassava)</label>
    <input type="number" class="form-control" id="numberOfSAHPlants" wire:model='f_number_of_sah_plants_produced'>
</div>

<!-- Area Under Basic Seed Multiplication (Number of Acres) -->
<div class="mb-3">
    <label for="areaUnderBasicSeed" class="my-3 form-label fw-bold">Area Under
        Basic Seed
        Multiplication (Number of Acres)</label>
    <div class="mb-3">
        <label for="areaUnderBasicSeedTotal" class="form-label">Area Under Basic
            Seed Multiplication (Total):</label>
        <input type="number" class="form-control" id="areaUnderBasicSeedTotal"
            wire:model="f_area_under_basic_seed_multiplication.total">
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety1Seed" class="form-label">Area Under Basic Seed
                    Multiplication (Variety 1):</label>
                <input type="number" class="form-control" id="variety1Seed"
                    wire:model="f_area_under_basic_seed_multiplication.variety_1">
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety2Seed" class="form-label">Area Under Basic Seed
                    Multiplication (Variety 2):</label>
                <input type="number" class="form-control" id="variety2Seed"
                    wire:model="f_area_under_basic_seed_multiplication.variety_2">
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety3Seed" class="form-label">Area Under Basic Seed
                    Multiplication (Variety 3):</label>
                <input type="number" class="form-control" id="variety3Seed"
                    wire:model="f_area_under_basic_seed_multiplication.variety_3">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety4Seed" class="form-label">Area Under Basic Seed
                    Multiplication (Variety 4):</label>
                <input type="number" class="form-control" id="variety4Seed"
                    wire:model="f_area_under_basic_seed_multiplication.variety_4">
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety5Seed" class="form-label">Area Under Basic Seed
                    Multiplication (Variety 5):</label>
                <input type="number" class="form-control" id="variety5Seed"
                    wire:model="f_area_under_basic_seed_multiplication.variety_5">
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety6Seed" class="form-label">Area Under Basic Seed
                    Multiplication (Variety 6):</label>
                <input type="number" class="form-control" id="variety6Seed"
                    wire:model="f_area_under_basic_seed_multiplication.variety_6">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety7Seed" class="form-label">Area Under Basic Seed
                    Multiplication (Variety 7):</label>
                <input type="number" class="form-control" id="variety7Seed"
                    wire:model="f_area_under_basic_seed_multiplication.variety_7">
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
        <input type="number" class="form-control" id="areaUnderCertifiedSeedTotal"
            wire:model="f_area_under_certified_seed_multiplication.total">
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety1CertifiedSeed" class="form-label">Area Under
                    Certified Seed Multiplication (Variety 1):</label>
                <input type="number" class="form-control" id="variety1CertifiedSeed"
                    wire:model="f_area_under_certified_seed_multiplication.variety_1">
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety2CertifiedSeed" class="form-label">Area Under
                    Certified Seed Multiplication (Variety 2):</label>
                <input type="number" class="form-control" id="variety2CertifiedSeed"
                    wire:model="f_area_under_certified_seed_multiplication.variety_2">
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety3CertifiedSeed" class="form-label">Area Under
                    Certified Seed Multiplication (Variety 3):</label>
                <input type="number" class="form-control" id="variety3CertifiedSeed"
                    wire:model="f_area_under_certified_seed_multiplication.variety_3">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety4CertifiedSeed" class="form-label">Area Under
                    Certified Seed Multiplication (Variety 4):</label>
                <input type="number" class="form-control" id="variety4CertifiedSeed"
                    wire:model="f_area_under_certified_seed_multiplication.variety_4">
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety5CertifiedSeed" class="form-label">Area Under
                    Certified Seed Multiplication (Variety 5):</label>
                <input type="number" class="form-control" id="variety5CertifiedSeed"
                    wire:model="f_area_under_certified_seed_multiplication.variety_5">
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety6CertifiedSeed" class="form-label">Area Under
                    Certified Seed Multiplication (Variety 6):</label>
                <input type="number" class="form-control" id="variety6CertifiedSeed"
                    wire:model="f_area_under_certified_seed_multiplication.variety_6">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety7CertifiedSeed" class="form-label">Area Under
                    Certified Seed Multiplication (Variety 7):</label>
                <input type="number" class="form-control" id="variety7CertifiedSeed"
                    wire:model="f_area_under_certified_seed_multiplication.variety_7">
            </div>
        </div>
    </div>

</div>

<!-- Are You a Registered Seed Producer -->
<div class="mb-3">
    <label class="form-label">Are You a Registered Seed Producer</label>
    <div>
        <div class="form-check">
            <input class="form-check-input" type="radio" id="registeredSeedProducerYes" value="1"
                wire:model="f_is_registered_seed_producer">
            <label class="form-check-label" for="registeredSeedProducerYes">Yes</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" id="registeredSeedProducerNo" value="0"
                wire:model="f_is_registered_seed_producer">
            <label class="form-check-label" for="registeredSeedProducerNo">No</label>
        </div>
    </div>
</div>

<!-- Registration Details (Seed Services Unit) -->
<div class="mb-3">
    <label for="seedRegistrationDetails" class="form-label">Registration Details
        (Seed Services Unit)</label>
    <div class="mb-3">
        <label for="registrationNumber" class="form-label">Seed Service Unit
            Registration Number:</label>
        <input type="text" class="form-control" id="registrationNumber"
            wire:model="f_seed_service_unit_registration_details.registration_number">
    </div>

    <div class="mb-3">
        <label for="registrationDate" class="form-label">Seed Service Unit
            Registration Date:</label>
        <input type="date" class="form-control" id="registrationDate"
            wire:model="f_seed_service_unit_registration_details.registration_date">
    </div>

</div>

<!-- Do You Use Certified Seed -->
<div class="mb-3">
    <label class="form-label">Do You Use Certified Seed</label>
    <div>
        <div class="form-check">
            <input class="form-check-input" type="radio" id="useCertifiedSeedYes" value="1"
                wire:model="f_uses_certified_seed">
            <label class="form-check-label" for="useCertifiedSeedYes">Yes</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" id="useCertifiedSeedNo" value="0"
                wire:model="f_uses_certified_seed">
            <label class="form-check-label" for="useCertifiedSeedNo">No</label>
        </div>
    </div>
</div>



<div class="mb-5 alert alert-warning" id="section-e" role="alert">
    <strong>SECTION E: RTC MARKETING</strong>
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
    <label class="form-label">Do You Have Any RTC Market Contractual Agreement</label>
    <div>
        <div class="form-check">
            <input class="form-check-input" type="radio" id="rtcMarketContractYes" value="1"
                wire:model="f_has_rtc_market_contract">
            <label class="form-check-label" for="rtcMarketContractYes">Yes</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" id="rtcMarketContractNo" value="0"
                wire:model="f_has_rtc_market_contract">
            <label class="form-check-label" for="rtcMarketContractNo">No</label>
        </div>
    </div>
</div>

<!-- Total Volume of Production in Previous Season (Metric Tonnes) -->
<div class="mb-3">
    <label for="totalVolumeProduction" class="form-label">Total Volume of
        Production in Previous Season (Metric Tonnes)</label>
    <input type="number" class="form-control" id="totalVolumeProduction"
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
        <input type="number" class="form-control" id="totalProductionValue"
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
    <input type="number" class="form-control" id="totalVolumeIrrigation"
        wire:model='f_total_vol_irrigation_production_previous_season'>
</div>

<!-- Total Value of Irrigation Production in Previous Season (Financial Value-MWK) -->
<div class="mb-3">
    <label for="totalValueIrrigation" class="my-3 form-label fw-bold">Total Value
        of Irrigation
        Production in Previous Season (Financial Value-MWK)</label>
    <div class="mb-3">
        <label for="totalIrrigationProductionValue" class="form-label">Total
            Irrigation Production Value Previous Season:</label>
        <input type="number" class="form-control" id="totalIrrigationProductionValue"
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
    <label class="form-label">Do You Sell Your Products to International Markets</label>
    <div>
        <div class="form-check">
            <input class="form-check-input" type="radio" id="sellToInternationalMarketsYes" value="1"
                wire:model="f_sells_to_international_markets">
            <label class="form-check-label" for="sellToInternationalMarketsYes">Yes</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" id="sellToInternationalMarketsNo" value="0"
                wire:model="f_sells_to_international_markets">
            <label class="form-check-label" for="sellToInternationalMarketsNo">No</label>
        </div>
    </div>
</div>

<!-- Sell Products to International Markets -->
<div class="mb-3">
    <label class="form-label">Do You Sell Your Products to International Markets</label>
    <div>
        <div class="form-check">
            <input class="form-check-input" type="radio" id="sellToInternationalMarketsYes" value="1"
                wire:model="f_sells_to_international_markets">
            <label class="form-check-label" for="sellToInternationalMarketsYes">Yes</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" id="sellToInternationalMarketsNo" value="0"
                wire:model="f_sells_to_international_markets">
            <label class="form-check-label" for="sellToInternationalMarketsNo">No</label>
        </div>
    </div>
</div>

<!-- Sell Products Through Market Information Systems -->
<div class="mb-3">
    <label class="form-label">Do You Sell Your Products Through Market Information Systems</label>
    <div>
        <div class="form-check">
            <input class="form-check-input" type="radio" id="sellThroughMarketInfoYes" value="1"
                wire:model="f_uses_market_information_systems">
            <label class="form-check-label" for="sellThroughMarketInfoYes">Yes</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" id="sellThroughMarketInfoNo" value="0"
                wire:model="f_uses_market_information_systems">
            <label class="form-check-label" for="sellThroughMarketInfoNo">No</label>
        </div>
    </div>
</div>

<div class="mb-3">
    <label for="" class="form-label">Specify MIS</label>
    <input type="text" class="form-control" name="" id="" aria-describedby="helpId" placeholder=""
        wire:model='f_market_information_systems' />

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
    <input type="number" class="form-control" id="totalVolumeSoldThroughAggregation"
        wire:model='f_aggregation_center_sales'>
</div>