<div class="alert alert-primary" role="alert">
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

{{-- SECTION B --}}

<div class="alert alert-primary" role="alert">
    <strong>SECTION B: RTC MARKETING </strong>
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
        wire:model='total_production_previous_season'>
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
