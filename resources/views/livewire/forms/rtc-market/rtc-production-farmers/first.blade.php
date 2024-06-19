<div class="mb-5 alert alert-primary" role="alert" id="section-a">
    <strong>SECTION A: RTC ACTOR PROFILE</strong>
</div>

<!-- Date of Recruitment -->
<div class="mb-3">
    <label for="dateOfRecruitment" class="form-label">Date of
        Recruitment</label>
    <input type="date" class="form-control" id="dateOfRecruitment" wire:model='date_of_recruitment' />
</div>

<!-- Name of Actor -->
<div class="mb-3">
    <label for="nameOfActor" class="form-label">Name of Actor</label>
    <input type="text" class="form-control" id="nameOfActor" wire:model='name_of_actor'>
</div>

<!-- Name of Representative -->
<div class="mb-3">
    <label for="nameOfRepresentative" class="form-label">Name of
        Representative</label>
    <input type="text" class="form-control" id="nameOfRepresentative" wire:model='name_of_representative'>
</div>

<!-- Phone Number -->
<div class="mb-3">
    <label for="phoneNumber" class="form-label">Phone Number</label>
    <input type="tel" class="form-control" id="phoneNumber" wire:model='phone_number'>
</div>

<!-- Type -->
<div class="mb-3">
    <label for="type" class="form-label">Type</label>
    <input type="text" class="form-control" id="type" wire:model='type'>
</div>

<!-- Approach (For Producer Organizations Only) -->
<div class="mb-3">
    <label for="approach" class="form-label">If Producer Organization, What
        Approach Does Your Group Follow (For Producer Organizations
        Only)</label>
    <select class="form-select" wire:model="approach">

        <option value="COLLECTIVE PRODUCTION ONLY">COLLECTIVE PRODUCTION ONLY
        </option>
        <option value="COLLECTIVE MARKETING ONLY">COLLECTIVE MARKETING ONLY</option>
        <option value="KNOWLEDGE SHARING ONLY">KNOWLEDGE SHARING ONLY</option>
        <option value="COLLECTIVE PRODUCTION, MARKETING AND KNOWLEDGE SHARING">
            COLLECTIVE PRODUCTION, MARKETING AND KNOWLEDGE SHARING</option>
        <option value="N/A">N/A</option>
    </select>
</div>

<!-- Sector -->
<div class="mb-3">
    <label for="sector" class="form-label">Sector</label>
    <select class="form-select" wire:model="sector">

        <option value="PRIVATE">PRIVATE</option>
        <option value="PUBLIC">PUBLIC</option>
    </select>
</div>

<!-- Number of Members (For Producer Organizations Only) -->
<div class="mb-3">
    <label for="numberOfMembers" class="form-label">Number of Members (For
        Producer Organizations Only)</label>


    <div class="mb-3">
        <label for="total">TOTAL:</label>
        <div class="row">
            <div class="col">
                <label for="female1835">FEMALE 18-35YRS:</label>
                <input type="number" class="form-control" id="female1835" wire:model="number_of_members.female_18_35">
            </div>
            <div class="col">
                <label for="female35plus">FEMALE 35YRS+:</label>
                <input type="number" class="form-control" id="female35plus"
                    wire:model="number_of_members.female_35_plus">
            </div>
        </div>
        <div class="row">
            <div class="col">
                <label for="male1835">MALE 18-35YRS:</label>
                <input type="number" class="form-control" id="male1835" wire:model="number_of_members.male_18_35">
            </div>
            <div class="col">
                <label for="male35plus">MALE 35YRS +:</label>
                <input type="number" class="form-control" id="male35plus" wire:model="number_of_members.male_35_plus">
            </div>
        </div>
    </div>
</div>

<!-- Group -->
<div class="mb-3">
    <label for="group" class="form-label">Group</label>
    <select class="form-select" wire:model="group">

        <option value="EARLY GENERATION SEED PRODUCER">EARLY GENERATION SEED
            PRODUCER</option>
        <option value="SEED MULTIPLIER">SEED MULTIPLIER</option>
        <option value="RTC PRODUCER">RTC PRODUCER</option>
    </select>
</div>

<!-- New or Old Establishment -->
<div class="mb-3">
    <label for="establishment" class="form-label">Is this a New or Old
        Establishment</label>
    <select class="form-select" id="establishment" wire:model='establishment_status'>

        <option value="new">NEW (1-5 YEARS)</option>
        <option value="old">OLD (OVER 5 YEARS)</option>
    </select>
</div>

<!-- Formally Registered Entity -->
<div class="mb-3">
    <label for="registeredEntity" class="form-label">Is this a Formally
        Registered Entity</label>
    <select class="form-select" id="registeredEntity" wire:model='is_registered'>

        <option value="1">Yes</option>
        <option value="0">No</option>
    </select>
</div>

<!-- Registration Details -->
<div class="mb-3">
    <label for="registrationDetails" class="form-label">Registration
        Details</label>

    <div class="mb-3">
        <label for="registrationBody">REGISTRATION BODY:</label>
        <input type="text" class="form-control" id="registrationBody"
            wire:model="registration_details.registration_body">
    </div>
    <div class="mb-3">
        <label for="registrationNumber">REGISTRATION NUMBER:</label>
        <input type="text" class="form-control" id="registrationNumber"
            wire:model="registration_details.registration_number">
    </div>
    <div class="mb-3">
        <label for="registrationDate">REGISTRATION DATE:</label>
        <input type="date" class="form-control" id="registrationDate"
            wire:model="registration_details.registration_date">
    </div>

</div>

<!-- Number of Employees on RTC Establishment -->
<div class="mb-3">
    <label for="numberOfEmployees" class="form-label">Number of Employees on RTC
        Establishment</label>
    <div class="row">
        <strong class="my-3">Formal Employees</strong>
        <div class="col">


            <div class="mb-3">
                <label for="formalFemale1835">FEMALE 18-35YRS:</label>
                <input type="number" class="form-control" id="formalFemale1835"
                    wire:model="number_of_employees.formal.female_18_35">
            </div>
            <div class="mb-3">
                <label for="formalFemale35">FEMALE 35YRS+:</label>
                <input type="number" class="form-control" id="formalFemale35"
                    wire:model="number_of_employees.formal.female_35_plus">
            </div>
        </div>
        <div class="col">
            <div class="mb-3">
                <label for="formalMale1835">MALE 18-35YRS:</label>
                <input type="number" class="form-control" id="formalMale1835"
                    wire:model="number_of_employees.formal.male_18_35">
            </div>
            <div class="mb-3">
                <label for="formalMale35">MALE 35YRS+:</label>
                <input type="number" class="form-control" id="formalMale35"
                    wire:model="number_of_employees.formal.male_35_plus">
            </div>
        </div>
    </div>

    <div class="row">
        <strong class="my-3">Informal Employees</strong>
        <div class="col">


            <div class="mb-3">
                <label for="informalFemale1835">FEMALE 18-35YRS:</label>
                <input type="number" class="form-control" id="informalFemale1835"
                    wire:model="number_of_employees.informal.female_18_35">
            </div>
            <div class="mb-3">
                <label for="informalFemale35">FEMALE 35YRS+:</label>
                <input type="number" class="form-control" id="informalFemale35"
                    wire:model="number_of_employees.informal.female_35_plus">
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="informalMale1835">MALE 18-35YRS: </label>
                <input type="number" class="form-control" id="informalMale1835"
                    wire:model="number_of_employees.informal.male_18_35">
            </div>
            <div class="mb-3">
                <label for="informalMale35">MALE 35YRS+:</label>
                <input type="number" class="form-control" id="informalMale35"
                    wire:model="number_of_employees.informal.male_35_plus">
            </div>
        </div>
    </div>
</div>





<div class="alert alert-primary" id="section-b" role="alert">
    <strong>SECTION B: RTC PRODUCTION </strong>
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
                    wire:model="area_under_cultivation.variety_1">
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="areaUnderCultivationVariety2" class="form-label">Area
                    Under Cultivation (Variety 2):</label>
                <input type="number" class="form-control" id="areaUnderCultivationVariety2"
                    wire:model="area_under_cultivation.variety_2">
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="areaUnderCultivationVariety3" class="form-label">Area
                    Under Cultivation (Variety 3):</label>
                <input type="number" class="form-control" id="areaUnderCultivationVariety3"
                    wire:model="area_under_cultivation.variety_3">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <label for="areaUnderCultivationVariety4" class="form-label">Area
                    Under Cultivation (Variety 4):</label>
                <input type="number" class="form-control" id="areaUnderCultivationVariety4"
                    wire:model="area_under_cultivation.variety_4">
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="areaUnderCultivationVariety5" class="form-label">Area
                    Under Cultivation (Variety 5):</label>
                <input type="number" class="form-control" id="areaUnderCultivationVariety5"
                    wire:model="area_under_cultivation.variety_5">
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
                    wire:model="number_of_plantlets_produced.cassava">
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="potatoPlantlets" class="form-label">Number of
                    Plantlets Produced (Potato):</label>
                <input type="number" class="form-control" id="potatoPlantlets"
                    wire:model="number_of_plantlets_produced.potato">
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="sweetPotatoPlantlets" class="form-label">Number of
                    Plantlets Produced (Sweet Potato):</label>
                <input type="number" class="form-control" id="sweetPotatoPlantlets"
                    wire:model="number_of_plantlets_produced.sweet_potato">
            </div>
        </div>
    </div>

</div>

<!-- Number of Screen House Vines Harvested (Sweet Potatoes) -->
<div class="mb-3">
    <label for="numberOfScreenHouseVines" class="form-label">Number of Screen
        House Vines Harvested (Sweet Potatoes)</label>
    <input type="number" class="form-control" id="numberOfScreenHouseVines"
        wire:model='number_of_screen_house_vines_harvested'>
</div>

<!-- Number of Screen House Mini-Tubers Harvested (Potato) -->
<div class="mb-3">
    <label for="numberOfMiniTubers" class="form-label">Number of Screen House
        Mini-Tubers Harvested (Potato)</label>
    <input type="number" class="form-control" id="numberOfMiniTubers"
        wire:model='number_of_screen_house_min_tubers_harvested'>
</div>

<!-- Number of SAH Plants Produced (Cassava) -->
<div class="mb-3">
    <label for="numberOfSAHPlants" class="form-label">Number of SAH Plants
        Produced (Cassava)</label>
    <input type="number" class="form-control" id="numberOfSAHPlants" wire:model='number_of_sah_plants_produced'>
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
            wire:model="area_under_basic_seed_multiplication.total">
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety1Seed" class="form-label">Area Under Basic Seed
                    Multiplication (Variety 1):</label>
                <input type="number" class="form-control" id="variety1Seed"
                    wire:model="area_under_basic_seed_multiplication.variety_1">
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety2Seed" class="form-label">Area Under Basic Seed
                    Multiplication (Variety 2):</label>
                <input type="number" class="form-control" id="variety2Seed"
                    wire:model="area_under_basic_seed_multiplication.variety_2">
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety3Seed" class="form-label">Area Under Basic Seed
                    Multiplication (Variety 3):</label>
                <input type="number" class="form-control" id="variety3Seed"
                    wire:model="area_under_basic_seed_multiplication.variety_3">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety4Seed" class="form-label">Area Under Basic Seed
                    Multiplication (Variety 4):</label>
                <input type="number" class="form-control" id="variety4Seed"
                    wire:model="area_under_basic_seed_multiplication.variety_4">
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety5Seed" class="form-label">Area Under Basic Seed
                    Multiplication (Variety 5):</label>
                <input type="number" class="form-control" id="variety5Seed"
                    wire:model="area_under_basic_seed_multiplication.variety_5">
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety6Seed" class="form-label">Area Under Basic Seed
                    Multiplication (Variety 6):</label>
                <input type="number" class="form-control" id="variety6Seed"
                    wire:model="area_under_basic_seed_multiplication.variety_6">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety7Seed" class="form-label">Area Under Basic Seed
                    Multiplication (Variety 7):</label>
                <input type="number" class="form-control" id="variety7Seed"
                    wire:model="area_under_basic_seed_multiplication.variety_7">
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
            wire:model="area_under_certified_seed_multiplication.total">
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety1CertifiedSeed" class="form-label">Area Under
                    Certified Seed Multiplication (Variety 1):</label>
                <input type="number" class="form-control" id="variety1CertifiedSeed"
                    wire:model="area_under_certified_seed_multiplication.variety_1">
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety2CertifiedSeed" class="form-label">Area Under
                    Certified Seed Multiplication (Variety 2):</label>
                <input type="number" class="form-control" id="variety2CertifiedSeed"
                    wire:model="area_under_certified_seed_multiplication.variety_2">
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety3CertifiedSeed" class="form-label">Area Under
                    Certified Seed Multiplication (Variety 3):</label>
                <input type="number" class="form-control" id="variety3CertifiedSeed"
                    wire:model="area_under_certified_seed_multiplication.variety_3">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety4CertifiedSeed" class="form-label">Area Under
                    Certified Seed Multiplication (Variety 4):</label>
                <input type="number" class="form-control" id="variety4CertifiedSeed"
                    wire:model="area_under_certified_seed_multiplication.variety_4">
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety5CertifiedSeed" class="form-label">Area Under
                    Certified Seed Multiplication (Variety 5):</label>
                <input type="number" class="form-control" id="variety5CertifiedSeed"
                    wire:model="area_under_certified_seed_multiplication.variety_5">
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety6CertifiedSeed" class="form-label">Area Under
                    Certified Seed Multiplication (Variety 6):</label>
                <input type="number" class="form-control" id="variety6CertifiedSeed"
                    wire:model="area_under_certified_seed_multiplication.variety_6">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety7CertifiedSeed" class="form-label">Area Under
                    Certified Seed Multiplication (Variety 7):</label>
                <input type="number" class="form-control" id="variety7CertifiedSeed"
                    wire:model="area_under_certified_seed_multiplication.variety_7">
            </div>
        </div>
    </div>

</div>

<!-- Are You a Registered Seed Producer -->
<div class="mb-3">
    <label for="registeredSeedProducer" class="form-label">Are You a Registered
        Seed Producer</label>
    <select class="form-select" id="registeredSeedProducer" wire:model='is_registered_seed_producer'>

        <option value="1">Yes</option>
        <option value="0">No</option>
    </select>
</div>

<!-- Registration Details (Seed Services Unit) -->
<div class="mb-3">
    <label for="seedRegistrationDetails" class="form-label">Registration Details
        (Seed Services Unit)</label>
    <div class="mb-3">
        <label for="registrationNumber" class="form-label">Seed Service Unit
            Registration Number:</label>
        <input type="text" class="form-control" id="registrationNumber"
            wire:model="seed_service_unit_registration_details.registration_number">
    </div>

    <div class="mb-3">
        <label for="registrationDate" class="form-label">Seed Service Unit
            Registration Date:</label>
        <input type="date" class="form-control" id="registrationDate"
            wire:model="seed_service_unit_registration_details.registration_date">
    </div>

</div>

<!-- Do You Use Certified Seed -->
<div class="mb-3">
    <label for="useCertifiedSeed" class="form-label">Do You Use Certified
        Seed</label>
    <select class="form-select" id="useCertifiedSeed" wire:model='uses_certified_seed'>

        <option value="1">Yes</option>
        <option value="0">No</option>
    </select>
</div>


<div class="mb-5 alert alert-primary" id="section-c" role="alert">
    <strong>SECTION C: RTC MARKETING </strong>
</div>
<!-- Market Segment (Multiple Responses) -->
<div class="mb-3">
    <label for="marketSegment" class="form-label">Market Segment (Multiple
        Responses)</label>

    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="marketSegmentFresh" wire:model="market_segment.fresh"
            value="YES">
        <label class="form-check-label" for="marketSegmentFresh">Fresh</label>
    </div>
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="marketSegmentProcessed" value="NO"
            wire:model="market_segment.processed">
        <label class="form-check-label" for="marketSegmentProcessed">Processed</label>
    </div>

</div>

<!-- RTC Market Contractual Agreement -->
<div class="mb-3">
    <label for="rtcMarketContract" class="form-label">Do You Have Any RTC Market
        Contractual Agreement</label>
    <select class="form-select" id="rtcMarketContract" wire:model='has_rtc_market_contract'>

        <option value="1">Yes</option>
        <option value="0">No</option>
    </select>
</div>

<!-- Total Volume of Production in Previous Season (Metric Tonnes) -->
<div class="mb-3">
    <label for="totalVolumeProduction" class="form-label">Total Volume of
        Production in Previous Season (Metric Tonnes)</label>
    <input type="number" class="form-control" id="totalVolumeProductions"
        wire:model='total_vol_production_previous_season'>
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
            wire:model="total_production_value_previous_season.total">
    </div>

    <div class="mb-3">
        <label for="dateOfMaximumSales" class="form-label">Date of Maximum
            Sales:</label>
        <input type="date" class="form-control" id="dateOfMaximumSales"
            wire:model="total_production_value_previous_season.date_of_maximum_sales">
    </div>

</div>

<!-- Total Volume of Production in Previous Season from Irrigation Farming (Metric Tonnes) -->
<div class="mb-3">
    <label for="totalVolumeIrrigation" class="form-label">Total Volume of
        Production in Previous Season from Irrigation Farming (Metric
        Tonnes)</label>
    <input type="number" class="form-control" id="totalVolumeIrrigation"
        wire:model='total_irrigation_production_previous_season'>
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
            wire:model="total_irrigation_production_value_previous_season.total">
    </div>

    <div class="mb-3">
        <label for="dateOfMaximumSalesIrrigation" class="form-label">Date of
            Maximum Sales (Irrigation):</label>
        <input type="date" class="form-control" id="dateOfMaximumSalesIrrigation"
            wire:model="total_irrigation_production_value_previous_season.date_of_maximum_sales">
    </div>

</div>

<!-- Sell RTC Products to Domestic Markets -->
<div class="mb-3">
    <label for="sellToDomesticMarkets" class="form-label">Do You Sell Your RTC
        Products to Domestic Markets</label>
    <select class="form-select" id="sellToDomesticMarkets" wire:model='sells_to_domestic_markets'>

        <option value="1">Yes</option>
        <option value="0">No</option>
    </select>
</div>

<!-- Sell Products to International Markets -->
<div class="mb-3">
    <label for="sellToInternationalMarkets" class="form-label">Do You Sell Your
        Products to International Markets</label>
    <select class="form-select" id="sellToInternationalMarkets" wire:model='sells_to_international_markets'>

        <option value="1">Yes</option>
        <option value="0">No</option>
    </select>
</div>

<!-- Sell Products Through Market Information Systems -->
<div class="mb-3">
    <label for="sellThroughMarketInfo" class="form-label">Do You Sell Your
        Products Through Market Information Systems</label>
    <select class="form-select" id="sellThroughMarketInfo" wire:model='uses_market_information_systems'>

        <option value="1">Yes</option>
        <option value="0">No</option>
    </select>
</div>

<div class="mb-3">
    <label for="" class="form-label">Specify MIS</label>
    <input type="text" class="form-control" name="" id="" aria-describedby="helpId"
        placeholder="" wire:model='market_information_systems' />

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
            wire:model="aggregation_centers.response">
    </div>

    <div class="mb-3">
        <label for="aggregationCenterSpecify" class="form-label">Aggregation
            Centers Specify:</label>
        <input type="text" class="form-control" id="aggregationCenterSpecify"
            wire:model="aggregation_centers.specify">
    </div>

</div>

<!-- Total Volume of RTC Sold Through Aggregation Centers in Previous Season (Metric Tonnes) -->
<div class="mb-3">
    <label for="totalVolumeSoldThroughAggregation" class="form-label">Total Volume
        of RTC Sold Through Aggregation Centers in Previous Season (Metric
        Tonnes)</label>
    <input type="number" class="form-control" id="totalVolumeSoldThroughAggregation"
        wire:model='aggregation_center_sales'>
</div>
