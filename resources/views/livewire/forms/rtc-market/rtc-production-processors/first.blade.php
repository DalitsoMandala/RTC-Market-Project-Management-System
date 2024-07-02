<div class="mb-5 alert alert-primary" role="alert" id="section-a">
    <strong>SECTION A: RTC ACTOR PROFILE</strong>
</div>

<!-- Date of Recruitment -->
<div class="mb-3">
    <label for="dateOfRecruitment" class="form-label">Date of
        Recruitment</label>
    <input type="date" class="form-control" id="dateOfRecruitment" wire:model='date_of_recruitment' />

    @error('date_of_recruitment')
        <x-error>{{ $message }}</x-error>
    @enderror
</div>

<!-- Name of Actor -->
<div class="mb-3">
    <label for="nameOfActor" class="form-label">Name of Actor</label>
    <input type="text" class="form-control" id="nameOfActor" wire:model='name_of_actor'>
    @error('name_of_actor')
        <x-error>{{ $message }}</x-error>
    @enderror
</div>

<!-- Name of Representative -->
<div class="mb-3">
    <label for="nameOfRepresentative" class="form-label">Name of
        Representative</label>
    <input type="text" class="form-control" id="nameOfRepresentative" wire:model='name_of_representative'>
    @error('name_of_representative')
        <x-error>{{ $message }}</x-error>
    @enderror
</div>

<!-- Phone Number -->
<div class="mb-3">
    <label for="phoneNumber" class="form-label">Phone Number</label>
    <x-phone type="tel" class="form-control" id="phoneNumber" wire:model='phone_number' />
    @error('phone_number')
        <x-error>{{ $message }}</x-error>
    @enderror
</div>

<!-- Type -->
<div class="mb-3" x-data="{ type: $wire.entangle('type') }">
    <label for="type" class="form-label">Type</label>

    <select class="form-select form-select-md" wire:model='type'>
        <option selected value="">Select one</option>
        <option value="PRODUCER ORGANIZATION">PRODUCER ORGANIZATION (PO)</option>
        <option value="LARGE SCALE FARM">LARGE SCALE FARM</option>

    </select>

    @error('type')
        <x-error>{{ $message }}</x-error>
    @enderror

</div>

<!-- Approach (For Producer Organizations Only) -->
<div class="mb-3" x-data="{
    type: $wire.entangle('type'),
    approach: $wire.entangle('approach')
}" x-init="$watch('type', (v) => {

    if (v != 'PRODUCER ORGANIZATION') {
        approach = '';
        $wire.resetValues('approach');

    }
});" x-show="type=='PRODUCER ORGANIZATION'">
    <label for="approach" class="form-label">What
        Approach Does Your Group Follow (For Producer Organizations
        Only)</label>
    <select class="form-select" x-model="approach">
        <option value="">Select One</option>
        <option value="COLLECTIVE PRODUCTION ONLY">COLLECTIVE PRODUCTION ONLY
        </option>
        <option value="COLLECTIVE MARKETING ONLY">COLLECTIVE MARKETING ONLY</option>
        <option value="KNOWLEDGE SHARING ONLY">KNOWLEDGE SHARING ONLY</option>
        <option value="COLLECTIVE PRODUCTION, MARKETING AND KNOWLEDGE SHARING">
            COLLECTIVE PRODUCTION, MARKETING AND KNOWLEDGE SHARING</option>
        <option value="N/A">N/A</option>
    </select>

    @error('approach')
        <x-error>{{ $message }}</x-error>
    @enderror
</div>
<!-- Sector -->
<div class="mb-3">
    <label for="sector" class="form-label">Sector</label>
    <select class="form-select" wire:model="sector">
        <option value="">Select One</option>
        <option value="PRIVATE">PRIVATE</option>
        <option value="PUBLIC">PUBLIC</option>
    </select>

    @error('sector')
        <x-error>{{ $message }}</x-error>
    @enderror
</div>

<!-- Number of Members (For Producer Organizations Only) -->
<div class="mb-3" x-data="{
    type: $wire.entangle('type'),
    number_of_members: $wire.entangle('number_of_members')
}" x-init="$watch('type', (v) => {

    if (v != 'PRODUCER ORGANIZATION') {
        number_of_members = [];
        $wire.resetValues('number_of_members');
    }
});" x-show="type=='PRODUCER ORGANIZATION'">
    <label for="numberOfMembers" class="form-label">Number of Members (For
        Producer Organizations Only)</label>


    <div class="mb-3">
        <label for="total">TOTAL:</label>
        <div class="row">
            <div class="col">
                <label for="female1835">FEMALE 18-35YRS:</label>
                <input type="number" class="form-control" id="female1835" x-model="number_of_members.female_18_35">
            </div>
            <div class="col">
                <label for="female35plus">FEMALE 35YRS+:</label>
                <input type="number" class="form-control" id="female35plus" x-model="number_of_members.female_35_plus">
            </div>
        </div>
        <div class="row">
            <div class="col">
                <label for="male1835">MALE 18-35YRS:</label>
                <input type="number" class="form-control" id="male1835" x-model="number_of_members.male_18_35">
            </div>
            <div class="col">
                <label for="male35plus">MALE 35YRS +:</label>
                <input type="number" class="form-control" id="male35plus" x-model="number_of_members.male_35_plus">
            </div>
        </div>
    </div>
</div>

<!-- Group -->
<div class="mb-3" x-data="{
    group: $wire.entangle('group')
}">
    <label for="group" class="form-label">Group</label>
    <select class="form-select" x-model="group">
        <option value="">Select One</option>
        <option selected value="EARLY GENERATION SEED PRODUCER">EARLY GENERATION SEED
            PRODUCER</option>
        <option value="SEED MULTIPLIER">SEED MULTIPLIER</option>
        <option value="RTC PRODUCER">RTC PRODUCER</option>
    </select>

    @error('group')
        <x-error>{{ $message }}</x-error>
    @enderror
</div>

<!-- New or Old Establishment -->
<div class="mb-3">
    <label for="establishment" class="form-label">Is this a New or Old
        Establishment</label>
    <select class="form-select" id="establishment" wire:model='establishment_status'>
        <option value="">Select One</option>
        <option value="NEW">NEW (1-5 YEARS)</option>
        <option value="OLD">OLD (OVER 5 YEARS)</option>
    </select>
</div>

<!-- Formally Registered Entity -->
<div class="mb-3" x-data="{
    is_registered: $wire.entangle('is_registered')
}">
    <label class="form-label">Is this a Formally Registered Entity</label>
    <div>
        <div class="form-check">
            <input class="form-check-input" type="radio" id="registeredYes" value="1"
                x-model="is_registered">
            <label class="form-check-label" for="registeredYes">Yes</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" id="registeredNo" value="0"
                x-model="is_registered">
            <label class="form-check-label" for="registeredNo">No</label>
        </div>
    </div>
</div>


<!-- Registration Details -->
<div class="mb-3" x-data="{
    is_registered: $wire.entangle('is_registered'),
    registration_details: $wire.entangle('registration_details')
}" x-init="$watch('is_registered', (v) => {

    if (v != 1) {
        $wire.resetValues('registration_details');
    }
});" x-show='is_registered == 1'>
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





<div class="mb-5 alert alert-primary" id="section-b" role="alert">
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
<div class="mb-3" x-data="{
    has_rtc_market_contract: $wire.entangle('has_rtc_market_contract')
}">
    <label class="form-label">Do You Have Any RTC Market Contractual Agreement</label>
    <div>
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
        wire:model='total_vol_irrigation_production_previous_season'>
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
<div class="mb-3" x-data="{
    sells_to_domestic_markets: $wire.entangle('sells_to_domestic_markets')
}">
    <label class="form-label">Do You Sell Your RTC Products to Domestic Markets</label>
    <div>
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
</div>

<!-- Sell Products to International Markets -->
<div class="mb-3" x-data="{
    sells_to_international_markets: $wire.entangle('sells_to_international_markets')
}">
    <label class="form-label">Do You Sell Your Products to International Markets</label>
    <div>
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
</div>

<!-- Sell Products Through Market Information Systems -->
<div class="mb-3" x-data="{
    uses_market_information_systems: $wire.entangle('uses_market_information_systems')
}">
    <label class="form-label">Do You Sell Your Products Through Market Information Systems</label>
    <div>
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
</div>

<div class="mb-3" x-data="{
    uses_market_information_systems: $wire.entangle('uses_market_information_systems'),
    market_information_systems: $wire.entangle('market_information_systems')
}" x-init="$watch('uses_market_information_systems', (v) => {

    if (v != 1) {
        $wire.resetValues('market_information_systems');
    }
});" x-show='uses_market_information_systems == 1'>
    <label for="" class="form-label">Specify Market Information System</label>
    <input type="text" class="form-control" name="" id="" aria-describedby="helpId"
        placeholder="" x-model='market_information_systems' />

</div>



<!-- Sell RTC Produce Through Aggregation Centers -->

<div x-data="{
    aggregation_centers: $wire.entangle('aggregation_centers'),
    change() {
        this.aggregation_centers.specify = '';
    }

}" x-init="$watch('aggregation_centers.response', (v) => {

    if (v != 1) {
        change();
        $wire.resetValues('aggregation_centers');
    }
});

aggregation_centers.response = 0;">
    <div class="mb-3">
        <label for="sellThroughAggregationCenters" class="my-3 form-label ">Do
            You Sell RTC
            Produce Through Aggregation Centers</label>

        <div>
            <div class="form-check">
                <input class="form-check-input" type="radio" id="aggregationCenterResponseYes" value="1"
                    wire:model='aggregation_centers.response' x-model="aggregation_centers.response">
                <label class="form-check-label">Yes</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" id="aggregationCenterResponseNo" value="0"
                    wire:model='aggregation_centers.response' x-model="aggregation_centers.response">
                <label class="form-check-label">No</label>
            </div>
        </div>


    </div>

    <div class="mb-3" x-show='aggregation_centers.response == 1'>
        <label for="aggregationCenterSpecify" class="form-label">Aggregation
            Centers Specify:</label>
        <input type="text" class="form-control" wire:model="aggregation_centers.specify">
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
