<div class="mb-5 alert alert-primary" role="alert" id="section-a">
    <strong>SECTION A: RTC ACTOR PROFILE</strong>
</div>

<!-- Date of Recruitment -->
<div class="mb-3">
    <label for="dateOfRecruitment" class="form-label">Date of Recruitment</label>
    <input type="date" class="form-control @error('date_of_recruitment') is-invalid @enderror" id="dateOfRecruitment"
        wire:model='date_of_recruitment' />

    @error('date_of_recruitment')
        <x-error>{{ $message }}</x-error>
    @enderror
</div>

<!-- Name of Actor -->
<div class="mb-3">
    <label for="nameOfActor" class="form-label">Name of Actor</label>
    <input type="text" class="form-control @error('name_of_actor') is-invalid @enderror" id="nameOfActor"
        wire:model='name_of_actor'>
    @error('name_of_actor')
        <x-error>{{ $message }}</x-error>
    @enderror
</div>

<!-- Name of Representative -->
<div class="mb-3">
    <label for="nameOfRepresentative" class="form-label">Name of Representative</label>
    <input type="text" class="form-control @error('name_of_representative') is-invalid @enderror"
        id="nameOfRepresentative" wire:model='name_of_representative'>
    @error('name_of_representative')
        <x-error>{{ $message }}</x-error>
    @enderror
</div>

<!-- Phone Number -->
<div class="mb-3">
    <label for="phoneNumber" class="form-label">Phone Number</label>
    <x-phone type="tel" class="form-control " :class="$errors->has('phone_number') ? 'is-invalid' : ''" wire:model="phone_number" />
    @error('phone_number')
        <x-error>{{ $message }}</x-error>
    @enderror
</div>

<!-- Type -->
<div class="mb-3" x-data="{ type: $wire.entangle('type') }">
    <label for="type" class="form-label">Type</label>

    <select class="form-select form-select-md @error('type') is-invalid @enderror" wire:model='type'>
        <option selected value="">Select One</option>
        <option value="Producer organization">Producer organization (PO)</option>
        <option value="Large scale farm">Large scale farm</option>
        <option value="Small medium entrprise (SME)">Small medium entrprise (SME)</option>
    </select>

    @error('type')
        <x-error>{{ $message }}</x-error>
    @enderror

</div>

<!-- Approach (For Producer Organizations Only) -->
<div class="mb-3" x-data="{ type: $wire.entangle('type'), approach: $wire.entangle('approach') }" x-init="$watch('type', (v) => {
    if (v != 'Producer organization') {

        $wire.resetValues('approach');
    }
});" x-show="type=='Producer organization'">
    <label for="approach" class="form-label">What Approach Does Your Group Follow (For Producer Organizations
        Only)</label>
    <select class="form-select @error('approach') is-invalid @enderror" wire:model="approach">
        <option value="">Select One</option>
        <option value="Collective production only">Collective production only</option>
        <option value="Collective marketing only">Collective marketing only</option>
        <option value="Knowledge sharing only">Knowledge sharing only</option>
        <option value="Collective production, marketing and knowledge sharing">Collective production, marketing and
            knowledge sharing</option>
        <option value="N/A">N/A</option>
    </select>

    @error('approach')
        <x-error>{{ $message }}</x-error>
    @enderror
</div>


<!-- Sector -->
<div class="mb-3">
    <label for="sector" class="form-label">Sector</label>
    <select class="form-select @error('sector') is-invalid @enderror" wire:model="sector">
        <option value="">Select One</option>
        <option value="Private">Private</option>
        <option value="Public">Public</option>
    </select>
    @error('sector')
        <x-error>{{ $message }}</x-error>
    @enderror
</div>

<!-- Number of Members (For Producer Organizations Only) -->
<div class="mb-3" x-data="{
    type: $wire.entangle('type'),
    number_of_members: $wire.entangle('number_of_members'),


}" x-init="$watch('type', (v) => {
    if (v != 'Producer organization') {

        $wire.resetValues('number_of_members');
    }


});

$watch('number_of_members', (v) => {
    v.total = parseInt(v.female_18_35 || 0) + parseInt(v.female_35_plus || 0) + parseInt(v.male_18_35 || 0) + parseInt(v.male_35_plus || 0);
});" x-show="type=='Producer organization'">
    <label for="numberOfMembers" class="form-label">Number of Members (For Producer Organizations Only)</label>

    <div class="mb-3">

        <div class="row">

            <div class="col">
                <label for="female1835">FEMALE 18-35YRS:</label>
                <input type="number" min="0" step="any"
                    class="form-control @error('number_of_members.female_18_35') is-invalid @enderror" id="female1835"
                    x-model="number_of_members.female_18_35">
                @error('number_of_members.female_18_35')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>
            <div class="col">
                <label for="female35plus">FEMALE 35YRS+:</label>
                <input type="number" min="0" step="any"
                    class="form-control @error('number_of_members.female_35_plus') is-invalid @enderror"
                    id="female35plus" x-model="number_of_members.female_35_plus">
                @error('number_of_members.female_35_plus')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>
        </div>
        <div class="row">
            <div class="col">
                <label for="male1835">MALE 18-35YRS:</label>
                <input type="number" min="0" step="any"
                    class="form-control @error('number_of_members.male_18_35') is-invalid @enderror" id="male1835"
                    x-model="number_of_members.male_18_35">
                @error('number_of_members.male_18_35')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>
            <div class="col">
                <label for="male35plus">MALE 35YRS +:</label>
                <input type="number" min="0" step="any"
                    class="form-control @error('number_of_members.male_35_plus') is-invalid @enderror" id="male35plus"
                    x-model="number_of_members.male_35_plus">
                @error('number_of_members.male_35_plus')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>

            <div class="col-12">
                <label for="female1835">Total Members:</label>
                <input type="number" min="0" step="any"
                    class="form-control bg-light @error('number_of_members.total') is-invalid @enderror"
                    id="female1835" readonly x-model="number_of_members.total">
                @error('number_of_members.total')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>
        </div>
    </div>
</div>

<!-- Group -->
<div class="mb-3" x-data="{ group: $wire.entangle('group') }">
    <label for="group" class="form-label">Group</label>
    <select class="form-select @error('group') is-invalid @enderror" x-model="group">
        <option value="">Select One</option>
        <option value="Other">Other</option>

    </select>

    @error('group')
        <x-error>{{ $message }}</x-error>
    @enderror
</div>

<!-- New or Old Establishment -->
<div class="mb-3">
    <label for="establishment" class="form-label">Is this a New or Old Establishment</label>
    <select class="form-select @error('establishment_status') is-invalid @enderror" id="establishment"
        wire:model='establishment_status'>
        <option value="">Select One</option>
        <option value="New">New (1-5 Years)</option>
        <option value="Old">Old (Over 5 Years)</option>
    </select>
    @error('establishment_status')
        <x-error>{{ $message }}</x-error>
    @enderror
</div>
<!-- Formally Registered Entity -->
<div class="mb-3" x-data="{ is_registered: $wire.entangle('is_registered') }">
    <label class="form-label">Is this a Formally Registered Entity</label>
    <div class="@error('is_registered')
        border border-danger
    @enderror">
        <div class="form-check">
            <input class="form-check-input @error('is_registered') is-invalid @enderror" type="radio"
                id="registeredYes" value="1" x-model="is_registered">
            <label class="form-check-label" for="registeredYes">Yes</label>
        </div>
        <div class="form-check">
            <input class="form-check-input @error('is_registered') is-invalid @enderror" type="radio"
                id="registeredNo" value="0" x-model="is_registered">
            <label class="form-check-label" for="registeredNo">No</label>
        </div>
    </div>
    @error('is_registered')
        <x-error>{{ $message }}</x-error>
    @enderror
</div>
<!-- Registration Details -->
<div class="mb-3" x-data="{
    is_registered: $wire.entangle('is_registered'),
    registration_details: $wire.entangle('registration_details')
}" x-init="$watch('is_registered', (v) => {

    if (v != 1) {
        registration_details = {};
        $wire.resetValues('registration_details');
    }
});" x-show='is_registered == 1'>
    <label for="registrationDetails" class="form-label">Registration
        Details</label>

    <div class="mb-3">
        <label for="registrationBody">REGISTRATION BODY:</label>
        <input type="text"
            class="form-control @error('registration_details.registration_body') is-invalid @enderror"
            id="registrationBody" x-model="registration_details.registration_body">
        @error('registration_details.registration_body')
            <x-error>{{ $message }}</x-error>
        @enderror
    </div>
    <div class="mb-3">
        <label for="registrationNumber">REGISTRATION NUMBER:</label>
        <input type="text"
            class="form-control @error('registration_details.registration_number') is-invalid @enderror"
            id="registrationNumber" x-model="registration_details.registration_number">
        @error('registration_details.registration_number')
            <x-error>{{ $message }}</x-error>
        @enderror
    </div>
    <div class="mb-3">
        <label for="registrationDate">REGISTRATION DATE:</label>
        <input type="date"
            class="form-control @error('registration_details.registration_date') is-invalid @enderror"
            id="registrationDate" x-model="registration_details.registration_date">
        @error('registration_details.registration_date')
            <x-error>{{ $message }}</x-error>
        @enderror
    </div>

</div>

<!-- Number of Employees on RTC Establishment -->
<div class="mb-3" x-data="{
    number_of_employees: $wire.entangle('number_of_employees')
}" x-init="$watch('number_of_employees', (v) => {

    v.formal.total = parseInt(v.formal.female_18_35 || 0) + parseInt(v.formal.female_35_plus || 0) + parseInt(v.formal.male_18_35 || 0) + parseInt(v.formal.male_35_plus || 0);
    v.informal.total = parseInt(v.informal.female_18_35 || 0) + parseInt(v.informal.female_35_plus || 0) + parseInt(v.informal.male_18_35 || 0) + parseInt(v.informal.male_35_plus || 0);
});">

    <label for="numberOfEmployees" class="form-label">Number of Employees on RTC
        Establishment</label>
    <div class="row">
        <strong class="my-3">Formal Employees</strong>

        <div class="col">


            <div class="mb-3">
                <label for="formalFemale1835">FEMALE 18-35YRS:</label>
                <input type="number" min="0" step="any"
                    class="form-control @error('number_of_employees.formal.female_18_35') is-invalid @enderror"
                    id="formalFemale1835" x-model="number_of_employees.formal.female_18_35">
                @error('number_of_employees.formal.female_18_35')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>
            <div class="mb-3">
                <label for="formalFemale35">FEMALE 35YRS+:</label>
                <input type="number" min="0" step="any"
                    class="form-control @error('number_of_employees.formal.female_35_plus') is-invalid @enderror"
                    id="formalFemale35" x-model="number_of_employees.formal.female_35_plus">
                @error('number_of_employees.formal.female_35_plus')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>
        </div>


        <div class="col">
            <div class="mb-3">
                <label for="formalMale1835">MALE 18-35YRS:</label>
                <input type="number" min="0" step="any"
                    class="form-control @error('number_of_employees.formal.male_18_35') is-invalid @enderror"
                    id="formalMale1835" x-model="number_of_employees.formal.male_18_35">
                @error('number_of_employees.formal.male_18_35')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>
            <div class="mb-3">
                <label for="formalMale35">MALE 35YRS+:</label>
                <input type="number" min="0" step="any"
                    class="form-control @error('number_of_employees.formal.male_35_plus') is-invalid @enderror"
                    id="formalMale35" x-model="number_of_employees.formal.male_35_plus">
                @error('number_of_employees.formal.male_35_plus')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>
        </div>

        <div class="col-12">

            <div class="mb-3">
                <label for="formalFemale1835">Total Formal Employees:</label>
                <input type="number" min="0" step="any"
                    class="form-control bg-light @error('number_of_employees.formal.total') is-invalid @enderror"
                    readonly id="formalFemale1835" x-model="number_of_employees.formal.total">

            </div>
        </div>
    </div>

    <div class="row">
        <strong class="my-3">Informal Employees</strong>
        <div class="col">


            <div class="mb-3">
                <label for="informalFemale1835">FEMALE 18-35YRS:</label>
                <input type="number" min="0" step="any"
                    class="form-control @error('number_of_employees.informal.female_18_35') is-invalid @enderror"
                    id="informalFemale1835" x-model="number_of_employees.informal.female_18_35">
                @error('number_of_employees.informal.female_18_35')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>
            <div class="mb-3">
                <label for="informalFemale35">FEMALE 35YRS+:</label>
                <input type="number" min="0" step="any"
                    class="form-control @error('number_of_employees.informal.female_35_plus') is-invalid @enderror"
                    id="informalFemale35" x-model="number_of_employees.informal.female_35_plus">
                @error('number_of_employees.informal.female_35_plus')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="informalMale1835">MALE 18-35YRS: </label>
                <input type="number" min="0" step="any"
                    class="form-control @error('number_of_employees.informal.male_18_35') is-invalid @enderror"
                    id="informalMale1835" x-model="number_of_employees.informal.male_18_35">
                @error('number_of_employees.informal.male_18_35')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>
            <div class="mb-3">
                <label for="informalMale35">MALE 35YRS+:</label>
                <input type="number" min="0" step="any"
                    class="form-control @error('number_of_employees.informal.male_35_plus') is-invalid @enderror"
                    id="informalMale35" x-model="number_of_employees.informal.male_35_plus">
                @error('number_of_employees.informal.male_35_plus')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>
        </div>

        <div class="col-12">

            <div class="mb-3">
                <label for="formalFemale1835">Total Informal Employees:</label>
                <input type="number" min="0" step="any"
                    class="form-control bg-light @error('number_of_employees.informal.total') is-invalid @enderror"
                    readonly id="formalFemale1835" x-model="number_of_employees.informal.total">

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
<div class="mb-3 card card-body shadow-none border" x-data="{
    total_production_value_previous_season: $wire.entangle('total_production_value_previous_season'),
    rate: $wire.entangle('rate'),


}" x-init="() => {
    $watch('total_production_value_previous_season', (v) => {
        value = parseFloat(total_production_value_previous_season.value || 0) / parseFloat(rate);
        total_production_value_previous_season.total = (Math.round(value * 100) / 100).toFixed(2);


    })


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
            id="totalProductionValue" wire:model="total_production_value_previous_season.value">
        @error('total_production_value_previous_season.value')
            <x-error>{{ $message }}</x-error>
        @enderror
    </div>

    <div class="mb-3">
        <label for="dateOfMaximumSales" class="form-label">Date of Maximum
            Sales:</label>
        <input type="date"
            class="form-control  @error('total_production_value_previous_season.date_of_maximum_sales') is-invalid @enderror"
            id="dateOfMaximumSales" wire:model="total_production_value_previous_season.date_of_maximum_sales">
        @error('total_production_value_previous_season.date_of_maximum_sales')
            <x-error>{{ $message }}</x-error>
        @enderror
    </div>
    <div class="mb-3">
        <label for="dateOfMaximumSales" class="form-label">Today's USD Rate:
        </label>
        <x-text-input wire:model='rate' class="bg-light" readonly />
    </div>
    <div class="mb-3">
        <label for="totalProductionValue" class="form-label">Financial Value ($)</label>
        <input type="number" min="0" step="any"
            class="form-control bg-light  @error('total_production_value_previous_season.total') is-invalid @enderror"
            readonly id="totalProductionValue" wire:model="total_production_value_previous_season.total">

    </div>


</div>


<!-- Total Volume of Production in Previous Season from Irrigation Farming (Metric Tonnes) -->
<div class="mb-3">
    <label for="totalVolumeIrrigation" class="form-label">Total Volume of
        Production in Previous Season from Irrigation Farming (Metric
        Tonnes)</label>
    <input type="number" min="0" step="any"
        class="form-control  @error('total_vol_irrigation_production_previous_season') is-invalid @enderror"
        id="totalVolumeIrrigation" wire:model='total_vol_irrigation_production_previous_season'>
    @error('total_vol_irrigation_production_previous_season')
        <x-error>{{ $message }}</x-error>
    @enderror
</div>

<!-- Total Value of Irrigation Production in Previous Season (Financial Value-MWK) -->
<div class="mb-3 card card-body shadow-none border" x-data="{
    total_irrigation_production_value_previous_season: $wire.entangle('total_irrigation_production_value_previous_season'),
    rate: $wire.entangle('rate'),


}" x-init="() => {
    $watch('total_irrigation_production_value_previous_season', (v) => {
        value = parseFloat(total_irrigation_production_value_previous_season.value || 0) / parseFloat(rate);
        total_irrigation_production_value_previous_season.total = (Math.round(value * 100) / 100).toFixed(2);


    })


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
            id="totalProductionValue" wire:model="total_irrigation_production_value_previous_season.value">
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
            wire:model="total_irrigation_production_value_previous_season.date_of_maximum_sales">
        @error('total_irrigation_production_value_previous_season.date_of_maximum_sales')
            <x-error>{{ $message }}</x-error>
        @enderror
    </div>
    <div class="mb-3">
        <label for="dateOfMaximumSales" class="form-label">Today's USD Rate:
        </label>
        <x-text-input wire:model='rate' class="bg-light" readonly />
    </div>
    <div class="mb-3">
        <label for="totalProductionValue" class="form-label">Financial Value ($)</label>
        <input type="number" min="0" step="any"
            class="form-control bg-light  @error('total_irrigation_production_value_previous_season.total') is-invalid @enderror"
            readonly id="totalProductionValue" wire:model="total_irrigation_production_value_previous_season.total">

    </div>




</div>


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
                                    : ''"
                                    wire:model='market_information_systems.{{ $index }}.name'
                                    placeholder="Name of market information systems" />
                                @error('market_information_systems.' . $index . '.name')
                                    <x-error>{{ $message }}</x-error>
                                @enderror
                            </div>
                        </div>
                        <div class="col-2" x-data>
                            <button type="button" class="btn btn-danger"
                                @click="$wire.removeMIS({{ $index }})"
                                @if ($index == 0) disabled @endif>
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
                                    : ''"
                                    wire:model='aggregation_center_sales.{{ $index }}.name'
                                    placeholder="Name of aggregation center" />
                                @error('aggregation_center_sales.' . $index . '.name')
                                    <x-error>{{ $message }}</x-error>
                                @enderror
                            </div>
                        </div>
                        <div class="col-2" x-data>
                            <button type="button" class="btn btn-danger"
                                @click="$wire.removeSales({{ $index }})"
                                @if ($index == 0) disabled @endif>
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
    <input type="number" min="0" step="any"
        class="form-control @error('total_vol_aggregation_center_sales') is-invalid @enderror"
        wire:model='total_vol_aggregation_center_sales' id="" aria-describedby="helpId" placeholder="" />
    @error('total_vol_aggregation_center_sales')
        <x-error>{{ $message }}</x-error>
    @enderror
</div>
