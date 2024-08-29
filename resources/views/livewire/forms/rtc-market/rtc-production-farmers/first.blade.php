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
        <option selected value="">Select one</option>
        <option value="PRODUCER ORGANIZATION">PRODUCER ORGANIZATION (PO)</option>
        <option value="LARGE SCALE FARM">LARGE SCALE FARM</option>
    </select>

    @error('type')
        <x-error>{{ $message }}</x-error>
    @enderror

</div>

<!-- Approach (For Producer Organizations Only) -->
<div class="mb-3" x-data="{ type: $wire.entangle('type'), approach: $wire.entangle('approach') }" x-init="$watch('type', (v) => {
    if (v != 'PRODUCER ORGANIZATION') {
        approach = '';
        $wire.resetValues('approach');
    }
});" x-show="type=='PRODUCER ORGANIZATION'">
    <label for="approach" class="form-label">What Approach Does Your Group Follow (For Producer Organizations
        Only)</label>
    <select class="form-select @error('approach') is-invalid @enderror" wire:model="approach">
        <option value="">Select One</option>
        <option value="COLLECTIVE PRODUCTION ONLY">COLLECTIVE PRODUCTION ONLY</option>
        <option value="COLLECTIVE MARKETING ONLY">COLLECTIVE MARKETING ONLY</option>
        <option value="KNOWLEDGE SHARING ONLY">KNOWLEDGE SHARING ONLY</option>
        <option value="COLLECTIVE PRODUCTION, MARKETING AND KNOWLEDGE SHARING">COLLECTIVE PRODUCTION, MARKETING AND
            KNOWLEDGE SHARING</option>
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
    number_of_members: $wire.entangle('number_of_members'),


}" x-init="$watch('type', (v) => {
    if (v != 'PRODUCER ORGANIZATION') {
        number_of_members = {};
        $wire.resetValues('number_of_members');
    }


});

$watch('number_of_members', (v) => {
    v.total = parseInt(v.female_18_35 || 0) + parseInt(v.female_35_plus || 0) + parseInt(v.male_18_35 || 0) + parseInt(v.male_35_plus || 0);
});" x-show="type=='PRODUCER ORGANIZATION'">
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
        <option value="EARLY GENERATION SEED PRODUCER">EARLY GENERATION SEED PRODUCER</option>
        <option value="SEED MULTIPLIER">SEED MULTIPLIER</option>
        <option value="RTC PRODUCER">RTC PRODUCER</option>
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
        <option value="NEW">NEW (1-5 YEARS)</option>
        <option value="OLD">OLD (OVER 5 YEARS)</option>
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
                <input type="number" min="0" step="any"
                    class="form-control @error('area_under_cultivation.variety_1') is-invalid @enderror"
                    id="areaUnderCultivationVariety1" wire:model="area_under_cultivation.variety_1">
                @error('area_under_cultivation.variety_1')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="areaUnderCultivationVariety2" class="form-label">Area
                    Under Cultivation (Variety 2):</label>
                <input type="number" min="0" step="any"
                    class="form-control @error('area_under_cultivation.variety_2') is-invalid @enderror"
                    id="areaUnderCultivationVariety2" wire:model="area_under_cultivation.variety_2">
                @error('area_under_cultivation.variety_2')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="areaUnderCultivationVariety3" class="form-label">Area
                    Under Cultivation (Variety 3):</label>
                <input type="number" min="0" step="any"
                    class="form-control @error('area_under_cultivation.variety_3') is-invalid @enderror"
                    id="areaUnderCultivationVariety3" wire:model="area_under_cultivation.variety_3">
                @error('area_under_cultivation.variety_3')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="areaUnderCultivationVariety4" class="form-label">Area
                    Under Cultivation (Variety 4):</label>
                <input type="number" min="0" step="any"
                    class="form-control @error('area_under_cultivation.variety_4') is-invalid @enderror"
                    id="areaUnderCultivationVariety4" wire:model="area_under_cultivation.variety_4">
                @error('area_under_cultivation.variety_4')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="areaUnderCultivationVariety5" class="form-label">Area
                    Under Cultivation (Variety 5):</label>
                <input type="number" min="0" step="any"
                    class="form-control @error('area_under_cultivation.variety_5') is-invalid @enderror"
                    id="areaUnderCultivationVariety5" wire:model="area_under_cultivation.variety_5">
                @error('area_under_cultivation.variety_5')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>
        </div>
    </div>


</div>


<!-- Number of Plantlets Produced -->
<div class="mb-3" x-data="{
    group: $wire.entangle('group'),
    number_of_plantlets_produced: $wire.entangle('number_of_plantlets_produced')
}" x-init="$watch('group', (v) => {
    if (v != 'EARLY GENERATION SEED PRODUCER') {
        number_of_plantlets_produced = {};
        $wire.resetValues('number_of_plantlets_produced');
    }
});"
    x-show="group=='EARLY GENERATION SEED PRODUCER'">

    <label for="numberOfPlantlets" class="my-3 form-label fw-bold">Number of
        Plantlets
        Produced</label>
    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <label for="cassavaPlantlets" class="form-label">Number of
                    Plantlets Produced (Cassava):</label>
                <input type="number" min="0" step="any"
                    class="form-control @error('number_of_plantlets_produced.cassava') is-invalid @enderror"
                    id="cassavaPlantlets" x-model="number_of_plantlets_produced.cassava">
                @error('number_of_plantlets_produced.cassava')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="potatoPlantlets" class="form-label">Number of
                    Plantlets Produced (Potato):</label>
                <input type="number" min="0" step="any"
                    class="form-control @error('number_of_plantlets_produced.potato') is-invalid @enderror"
                    id="potatoPlantlets" x-model="number_of_plantlets_produced.potato">
                @error('number_of_plantlets_produced.potato')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="sweetPotatoPlantlets" class="form-label">Number of
                    Plantlets Produced (Sweet Potato):</label>
                <input type="number" min="0" step="any"
                    class="form-control @error('number_of_plantlets_produced.sweet_potato') is-invalid @enderror"
                    id="sweetPotatoPlantlets" x-model="number_of_plantlets_produced.sweet_potato">
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
            if (v != 'EARLY GENERATION SEED PRODUCER') {
                $wire.resetValues('number_of_screen_house_vines_harvested');
            }
        });
    }

}" x-show="group=='EARLY GENERATION SEED PRODUCER'">
    <label for="numberOfScreenHouseVines" class="form-label">Number of Screen
        House Vines Harvested (Sweet Potatoes)</label>
    <input type="number" min="0" step="any"
        class="form-control @error('number_of_screen_house_vines_harvested') is-invalid @enderror"
        id="numberOfScreenHouseVines" x-model='number_of_screen_house_vines_harvested'>
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
            if (v != 'EARLY GENERATION SEED PRODUCER') {
                $wire.resetValues('number_of_screen_house_min_tubers_harvested');
            }
        });
    }
}" x-show="group=='EARLY GENERATION SEED PRODUCER' ">
    <label for="numberOfMiniTubers" class="form-label">Number of Screen House
        Mini-Tubers Harvested (Potato)</label>
    <input type="number" min="0" step="any"
        class="form-control @error('number_of_screen_house_min_tubers_harvested') is-invalid @enderror"
        id="numberOfMiniTubers" x-model='number_of_screen_house_min_tubers_harvested'>
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
            if (v != 'EARLY GENERATION SEED PRODUCER') {
                $wire.resetValues('number_of_sah_plants_produced');
            }
        });
    }
}" x-show=" group=='EARLY GENERATION SEED PRODUCER'">
    <label for="numberOfSAHPlants" class="form-label">Number of SAH Plants
        Produced (Cassava)</label>
    <input type="number" min="0" step="any"
        class="form-control @error('number_of_sah_plants_produced') is-invalid @enderror" id="numberOfSAHPlants"
        x-model='number_of_sah_plants_produced'>
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
            if (v != 'EARLY GENERATION SEED PRODUCER') {
                area_under_basic_seed_multiplication = {};
                $wire.resetValues('area_under_basic_seed_multiplication');
            }
        });


    }
}" x-show="group=='EARLY GENERATION SEED PRODUCER'">
    <label for="areaUnderBasicSeed" class="my-3 form-label fw-bold">Area Under
        Basic Seed
        Multiplication (Number of Acres)</label>
    <div class="mb-3">
        <label for="areaUnderBasicSeedTotal" class="form-label">Area Under Basic
            Seed Multiplication (Total):</label>
        <input type="number" min="0" step="any"
            class="form-control @error('area_under_basic_seed_multiplication.total') is-invalid @enderror"
            id="areaUnderBasicSeedTotal" wire:model="area_under_basic_seed_multiplication.total">
        @error('area_under_basic_seed_multiplication.total')
            <x-error>{{ $message }}</x-error>
        @enderror
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety1Seed" class="form-label">Area Under Basic Seed
                    Multiplication (Variety 1):</label>
                <input type="number" min="0" step="any"
                    class="form-control @error('area_under_basic_seed_multiplication.variety_1') is-invalid @enderror"
                    id="variety1Seed" wire:model="area_under_basic_seed_multiplication.variety_1">
                @error('area_under_basic_seed_multiplication.variety_1')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety2Seed" class="form-label">Area Under Basic Seed
                    Multiplication (Variety 2):</label>
                <input type="number" min="0" step="any"
                    class="form-control @error('area_under_basic_seed_multiplication.variety_2') is-invalid @enderror"
                    id="variety2Seed" wire:model="area_under_basic_seed_multiplication.variety_2">
                @error('area_under_basic_seed_multiplication.variety_2')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety3Seed" class="form-label">Area Under Basic Seed
                    Multiplication (Variety 3):</label>
                <input type="number" min="0" step="any"
                    class="form-control @error('area_under_basic_seed_multiplication.variety_3') is-invalid @enderror"
                    id="variety3Seed" wire:model="area_under_basic_seed_multiplication.variety_3">
                @error('area_under_basic_seed_multiplication.variety_3')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety4Seed" class="form-label">Area Under Basic Seed
                    Multiplication (Variety 4):</label>
                <input type="number" min="0" step="any"
                    class="form-control @error('area_under_basic_seed_multiplication.variety_4') is-invalid @enderror"
                    id="variety4Seed" wire:model="area_under_basic_seed_multiplication.variety_4">
                @error('area_under_basic_seed_multiplication.variety_4')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety5Seed" class="form-label">Area Under Basic Seed
                    Multiplication (Variety 5):</label>
                <input type="number" min="0" step="any"
                    class="form-control @error('area_under_basic_seed_multiplication.variety_5') is-invalid @enderror"
                    id="variety5Seed" wire:model="area_under_basic_seed_multiplication.variety_5">
                @error('area_under_basic_seed_multiplication.variety_5')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety6Seed" class="form-label">Area Under Basic Seed
                    Multiplication (Variety 6):</label>
                <input type="number" min="0" step="any"
                    class="form-control @error('area_under_basic_seed_multiplication.variety_6') is-invalid @enderror"
                    id="variety6Seed" wire:model="area_under_basic_seed_multiplication.variety_6">
                @error('area_under_basic_seed_multiplication.variety_6')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety7Seed" class="form-label">Area Under Basic Seed
                    Multiplication (Variety 7):</label>
                <input type="number" min="0" step="any"
                    class="form-control @error('area_under_basic_seed_multiplication.variety_7') is-invalid @enderror"
                    id="variety7Seed" wire:model="area_under_basic_seed_multiplication.variety_7">
                @error('area_under_basic_seed_multiplication.variety_7')
                    <x-error>{{ $message }}</x-error>
                @enderror

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
        <input type="number" min="0" step="any"
            class="form-control @error('area_under_certified_seed_multiplication.total') is-invalid @enderror"
            id="areaUnderCertifiedSeedTotal" wire:model="area_under_certified_seed_multiplication.total">

        @error('area_under_certified_seed_multiplication.total')
            <x-error>{{ $message }}</x-error>
        @enderror
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety1CertifiedSeed" class="form-label">Area Under
                    Certified Seed Multiplication (Variety 1):</label>
                <input type="number" min="0" step="any"
                    class="form-control @error('area_under_certified_seed_multiplication.variety_1') is-invalid @enderror"
                    id="variety1CertifiedSeed" wire:model="area_under_certified_seed_multiplication.variety_1">
                @error('area_under_certified_seed_multiplication.variety_1')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety2CertifiedSeed" class="form-label">Area Under
                    Certified Seed Multiplication (Variety 2):</label>
                <input type="number" min="0" step="any"
                    class="form-control @error('area_under_certified_seed_multiplication.variety_2') is-invalid @enderror"
                    id="variety2CertifiedSeed" wire:model="area_under_certified_seed_multiplication.variety_2">

                @error('area_under_certified_seed_multiplication.variety_2')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety3CertifiedSeed" class="form-label">Area Under
                    Certified Seed Multiplication (Variety 3):</label>
                <input type="number" min="0" step="any"
                    class="form-control @error('area_under_certified_seed_multiplication.variety_3') is-invalid @enderror"
                    id="variety3CertifiedSeed" wire:model="area_under_certified_seed_multiplication.variety_3">
                @error('area_under_certified_seed_multiplication.variety_3')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety4CertifiedSeed" class="form-label">Area Under
                    Certified Seed Multiplication (Variety 4):</label>
                <input type="number" min="0" step="any"
                    class="form-control @error('area_under_certified_seed_multiplication.variety_4') is-invalid @enderror"
                    id="variety4CertifiedSeed" wire:model="area_under_certified_seed_multiplication.variety_4">
                @error('area_under_certified_seed_multiplication.variety_4')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety5CertifiedSeed" class="form-label">Area Under
                    Certified Seed Multiplication (Variety 5):</label>
                <input type="number" min="0" step="any"
                    class="form-control @error('area_under_certified_seed_multiplication.variety_5') is-invalid @enderror"
                    id="variety5CertifiedSeed" wire:model="area_under_certified_seed_multiplication.variety_5">
                @error('area_under_certified_seed_multiplication.variety_5')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety6CertifiedSeed" class="form-label">Area Under
                    Certified Seed Multiplication (Variety 6):</label>
                <input type="number" min="0" step="any"
                    class="form-control @error('area_under_certified_seed_multiplication.variety_6') is-invalid @enderror"
                    id="variety6CertifiedSeed" wire:model="area_under_certified_seed_multiplication.variety_6">
                @error('area_under_certified_seed_multiplication.variety_6')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>
        </div>

        <div class="col-md-4">
            <div class="mb-3">
                <label for="variety7CertifiedSeed" class="form-label">Area Under
                    Certified Seed Multiplication (Variety 7):</label>
                <input type="number" min="0" step="any"
                    class="form-control @error('area_under_certified_seed_multiplication.variety_7') is-invalid @enderror"
                    id="variety7CertifiedSeed" wire:model="area_under_certified_seed_multiplication.variety_7">
                @error('area_under_certified_seed_multiplication.variety_7')
                    <x-error>{{ $message }}</x-error>
                @enderror
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
            <input class="form-check-input" type="radio" id="registeredSeedProducerYes" value="1"
                x-model="is_registered_seed_producer">
            <label class="form-check-label" for="registeredSeedProducerYes">Yes</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" id="registeredSeedProducerNo" value="0"
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
});" x-show='is_registered_seed_producer == 1'>
    <label for="seedRegistrationDetails" class="form-label">Registration Details
        (Seed Services Unit)</label>
    <div class="mb-3">
        <label for="registrationNumber" class="form-label">Seed Service Unit
            Registration Number:</label>
        <input type="text"
            class="form-control  @error('seed_service_unit_registration_details.registration_number') is-invalid @enderror"
            id="registrationNumber" wire:model="seed_service_unit_registration_details.registration_number">
        @error('seed_service_unit_registration_details.registration_number')
            <x-error>{{ $message }}</x-error>
        @enderror
    </div>

    <div class="mb-3">
        <label for="registrationDate" class="form-label">Seed Service Unit
            Registration Date:</label>
        <input type="date"
            class="form-control @error('seed_service_unit_registration_details.registration_date') is-invalid @enderror "
            id="registrationDate" wire:model="seed_service_unit_registration_details.registration_date">
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
            <input class="form-check-input" type="radio" id="useCertifiedSeedYes" value="1"
                wire:model="uses_certified_seed">
            <label class="form-check-label" for="useCertifiedSeedYes">Yes</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" id="useCertifiedSeedNo" value="0"
                wire:model="uses_certified_seed">
            <label class="form-check-label" for="useCertifiedSeedNo">No</label>
        </div>
    </div>
    @error('uses_certified_seed')
        <x-error>{{ $message }}</x-error>
    @enderror
</div>


<div class="mb-5 alert alert-primary" id="section-c" role="alert">
    <strong>SECTION C: RTC MARKETING </strong>
</div>
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
            <input class="form-check-input" type="checkbox" id="marketSegmentProcessed" value="NO"
                wire:model="market_segment.processed">
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
<div class="mb-3">
    <label for="totalValueProduction" class="my-3 form-label fw-bold">Total Value
        Production
        Previous Season (Financial Value-MWK)</label>

    <div class="mb-3">
        <label for="totalProductionValue" class="form-label">Total Production
            Value Previous Season (Financial Value-MWK):</label>
        <input type="number" min="0" step="any"
            class="form-control  @error('total_production_value_previous_season.total') is-invalid @enderror"
            id="totalProductionValue" wire:model="total_production_value_previous_season.total">
        @error('total_production_value_previous_season.total')
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
<div class="mb-3">
    <label for="totalValueIrrigation" class="my-3 form-label fw-bold">Total Value
        of Irrigation
        Production in Previous Season (Financial Value-MWK)</label>
    <div class="mb-3">
        <label for="totalIrrigationProductionValue" class="form-label">Total
            Irrigation Production Value Previous Season:</label>
        <input type="number" min="0" step="any"
            class="form-control  @error('total_irrigation_production_value_previous_season.tota') is-invalid @enderror"
            id="totalIrrigationProductionValue" wire:model="total_irrigation_production_value_previous_season.total">
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
    market_information_systems: $wire.entangle('market_information_systems')
}" x-init="$watch('uses_market_information_systems', (v) => {

    if (v != 1) {
        market_information_systems = {};
        $wire.resetValues('market_information_systems');
    }
});" x-show='uses_market_information_systems == 1'>
    <label for="" class="form-label">Specify Market Information System</label>
    <input type="text" class="form-control  @error('market_information_systems') is-invalid @enderror"
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
});">
    <div class="mb-3">
        <label for="sellThroughAggregationCenters" class="my-3 form-label ">Do
            You Sell RTC
            Produce Through Aggregation Centers</label>

        <div class=" @error('aggregation_centers') border border-primary @enderror">
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

        @error('aggregation_centers')
            <x-error>{{ $message }}</x-error>
        @enderror
    </div>

    <div class="mb-3" x-show='aggregation_centers.response == 1'>
        <label for="aggregationCenterSpecify" class="form-label">Aggregation
            Centers Specify:</label>
        <input type="text" class="form-control  @error('aggregation_centers.specify') is-invalid @enderror"
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
        <input type="number" min="0" step="any"
            class="form-control  @error('aggregation_center_sales') is-invalid @enderror"
            id="totalVolumeSoldThroughAggregation" x-model='aggregation_center_sales'>

        @error('aggregation_center_sales')
            <x-error>{{ $message }}</x-error>
        @enderror
    </div>

</div>
