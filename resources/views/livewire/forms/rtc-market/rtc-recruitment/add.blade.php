@php
    use Ramsey\Uuid\Uuid;
    $uuid = Uuid::uuid4()->toString();
    $currentUrl = url()->current();
    $replaceUrl = str_replace('add', 'upload', $currentUrl) . "/{$uuid}";

@endphp
<x-form-component title="Add Recruitment Data" pageTitle="Add Data" :formTitle="$form_name" :openSubmission="$openSubmission" :targetSet="$targetSet"
    :targetIds="$targetIds" :showTargetForm="true" formName="rtc-actor-recruitment">

    <x-slot name="breadcrumbs">


        <li class="breadcrumb-item">
            <a href="/">Dashboard</a>
        </li>
        <li class="breadcrumb-item active">Add Data</li>
        <li class="breadcrumb-item">
            <a href="{{ $replaceUrl }}">Upload Data</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ $routePrefix }}/forms/rtc-market/rtc-actor-recruitment-form/view">
                {{ ucwords(strtolower($form_name)) }}
            </a>
        </li>
    </x-slot>

    <div class="mb-3">
        <label for="" class="form-label ">ENTERPRISE</label>
        <div class="form-group">

            <select class="form-select @error('location_data.enterprise')
            is-invalid
        @enderror"
                wire:model='location_data.enterprise'>
                <option value="">Select one</option>
                <option value="Cassava">Cassava</option>
                <option value="Potato">Potato</option>
                <option value="Sweet potato">Sweet potato</option>
            </select>
        </div>
        {{-- <x-text-input wire:model='enterprise'
        :class="$errors->has('enterprise') ? 'is-invalid' : ''" /> --}}
        @error('location_data.enterprise')
            <x-error>{{ $message }}</x-error>
        @enderror
    </div>

    <div class="mb-3">
        <label for="" class="form-label">DISTRICT</label>
        <select class="form-select @error('location_data.district')
        is-invalid
    @enderror"
            wire:model='location_data.district'>
            @include('layouts.district-options')
        </select>
        @error('location_data.district')
            <x-error>{{ $message }}</x-error>
        @enderror
    </div>

    <div class="mb-3">
        <label for="" class="form-label">EPA</label>
        <x-text-input wire:model='location_data.epa' :class="$errors->has('location_data.epa') ? 'is-invalid' : ''" />
        @error('location_data.epa')
            <x-error>{{ $message }}</x-error>
        @enderror
    </div>

    <div class="mb-3">
        <label for="" class="form-label">SECTION</label>
        <x-text-input wire:model='location_data.section' :class="$errors->has('location_data.section') ? 'is-invalid' : ''" />
        @error('location_data.section')
            <x-error>{{ $message }}</x-error>
        @enderror
    </div>



    <!-- Date of Recruitment -->
    <div class="mb-3">
        <label for="dateOfRecruitment" class="form-label">Date of
            Recruitment</label>
        <input type="date" class="form-control @error('date_of_recruitment') is-invalid @enderror"
            id="dateOfRecruitment" wire:model='date_of_recruitment' />

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
        <label for="nameOfRepresentative" class="form-label">Name of
            Representative</label>
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

        <select class="form-select form-select-md @error('type') is-invalid @enderror" x-model='type'>
            <option selected value="">Select one</option>
            <option value="Farmers">Farmers</option>
            <option value="Processors">Processors</option>
            <option value="Traders">Traders</option>
            <option value="Aggregators">Aggregators</option>
            <option value="Transporters">Transporters</option>
        </select>

        @error('type')
            <x-error>{{ $message }}</x-error>
        @enderror

    </div>

    <!-- Group -->
    <div class="mb-3" x-data="{ group: $wire.entangle('group'), type: $wire.entangle('type') }" x-init="() => {
    
    
    }">
        <label for="group" class="form-label">Group</label>
        <select class="form-select @error('group') is-invalid @enderror" x-model="group">
            <option value="">Select One</option>


            <option value="Producer organization (PO)">Producer organization (PO)
            </option>

            <option value="Large scale farm">Large scale farm</option>

            <option value="Small scale individual farms">Small scale individual
                farms</option>

            <option value="Large scale processor">Large scale processor</option>

            <option value="Other">Other</option>


        </select>

        @error('group')
            <x-error>{{ $message }}</x-error>
        @enderror
    </div>
    <!-- Approach (For Producer Organizations Only) -->
    <div class="mb-3" x-data="{ type: $wire.entangle('group'), approach: $wire.entangle('approach') }" x-init="$watch('type', (v) => {
        if (v != 'Producer organization') {
            approach = '';
            $wire.resetValues('approach');
        }
    });" x-show="type=='Producer organization'">
        <label for="approach" class="form-label">What Approach Does Your Group
            Follow
            (For Producer Organizations
            Only)</label>
        <select class="form-select @error('approach') is-invalid @enderror" wire:model="approach">
            <option value="">Select One</option>
            <option value="Collective production only">Collective production only
            </option>
            <option value="Collective marketing only">Collective marketing only
            </option>
            <option value="Knowledge sharing only">Knowledge sharing only</option>
            <option value="Collective production, marketing and knowledge sharing">
                Collective production, marketing and
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
    
    
    }" x-init="$watch('number_of_members', (v) => {
        v.total = parseInt(v.female_18_35 || 0) + parseInt(v.female_35_plus || 0) + parseInt(v.male_18_35 || 0) + parseInt(v.male_35_plus || 0);
    });">
        <label for="numberOfMembers" class="form-label">Number of Members</label>

        <div class="mb-3">

            <div class="row">

                <div class="col">
                    <label for="female1835">FEMALE 18-35YRS:</label>
                    <input type="number" min="0" step="any"
                        class="form-control @error('number_of_members.female_18_35') is-invalid @enderror"
                        id="female1835" x-model="number_of_members.female_18_35">
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
                        class="form-control @error('number_of_members.male_18_35') is-invalid @enderror"
                        id="male1835" x-model="number_of_members.male_18_35">
                    @error('number_of_members.male_18_35')
                        <x-error>{{ $message }}</x-error>
                    @enderror
                </div>
                <div class="col">
                    <label for="male35plus">MALE 35YRS +:</label>
                    <input type="number" min="0" step="any"
                        class="form-control @error('number_of_members.male_35_plus') is-invalid @enderror"
                        id="male35plus" x-model="number_of_members.male_35_plus">
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


    <!-- Category -->
    <div x-show="type==='Farmers'" class="mb-3" x-data="{
        type: $wire.entangle('type'),
        category: $wire.entangle('category'),
    }" x-init="$watch('type', (v) => {
        if (v != 'Farmers') {
    
            $wire.resetValues('category');
        }
    });">
        <label for="category" class="form-label">Category</label>
        <select class="form-select @error('category') is-invalid @enderror" id="establishment" x-model='category'>
            <option value="">Select One</option>
            <option value="Early generation seed producer">Early generation seed
                producer</option>
            <option value="Seed multiplier">Seed multiplier</option>
            <option value="RTC producer">RTC producer</option>
        </select>
        @error('category')
            <x-error>{{ $message }}</x-error>
        @enderror
    </div>

    <!-- New or Old Establishment -->
    <div class="mb-3">
        <label for="establishment" class="form-label">Is this a New or Old
            Establishment</label>
        <select class="form-select @error('establishment_status') is-invalid @enderror" id="establishment"
            wire:model='establishment_status'>
            <option value="">Select One</option>
            <option value="New">New (1-5 years)</option>
            <option value="Old">Old (Over 5 years)</option>
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
                <input checked class="form-check-input @error('is_registered') is-invalid @enderror" type="radio"
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

        <label for="numberOfEmployees" class="form-label">Number of Employees on
            RTC
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

    <!-- Area Under Cultivation (Number of Acres) by Variety -->
    <div x-show="type==='Farmers'" class="mb-3" x-data="{
        area_under_cultivation: $wire.entangle('area_under_cultivation'),
        type: $wire.entangle('type'),
    
        init() {
            this.$watch('type', (v) => {
                if (v != 'Farmers') {
                    $wire.resetValues('area_under_cultivation')
                }
            })
    
        }
    }">
        <label for="areaUnderCultivation" class="my-3 form-label fw-bold">Area
            Under
            Cultivation (Number of Acres)</label>
        <input type="number" min="0" step="any"
            class="form-control bg-light @error('area_under_cultivation') is-invalid @enderror"
            id="area_under_cultivation12" x-model="area_under_cultivation">

        @error('area_under_cultivation')
            <x-error>{{ $message }}</x-error>
        @enderror

    </div>



    <!-- Are You a Registered Seed Producer -->
    <div x-show="type==='Farmers'" class="mb-3" x-data="{
        is_registered_seed_producer: $wire.entangle('is_registered_seed_producer'),
        type: $wire.entangle('type'),
    
        init() {
            this.$watch('type', (v) => {
                if (v != 'Farmers') {
                    $wire.resetValues('is_registered_seed_producer')
                }
            })
    
        }
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
    <div x-data="{ is_registered_seed_producer: $wire.entangle('is_registered_seed_producer'), }" x-show="is_registered_seed_producer==1" x-init="$watch('is_registered_seed_producer', (v) => {
        if (v != 1) { $wire.resetValues('registrations'); }
    })">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Variety</th>
                    <th>Reg. Date</th>
                    <th>Reg. No.</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($registrations as $index => $reg)
                    <tr>
                        <td><input type="text" wire:model="registrations.{{ $index }}.variety"
                                class="form-control form-control-sm @error('registrations.' . $index . '.variety') is-invalid @enderror" />

                            @error('registrations.' . $index . '.variety')
                                <x-error>{{ $message }}</x-error>
                            @enderror
                        </td>
                        <td><input type="date" wire:model="registrations.{{ $index }}.reg_date"
                                class="form-control form-control-sm @error('registrations.' . $index . '.reg_date') is-invalid @enderror" />

                            @error('registrations.' . $index . '.reg_date')
                                <x-error>{{ $message }}</x-error>
                            @enderror
                        </td>
                        <td><input type="text" wire:model="registrations.{{ $index }}.reg_no"
                                class="form-control form-control-sm @error('registrations.' . $index . '.reg_no') is-invalid @enderror" />

                            @error('registrations.' . $index . '.reg_no')
                                <x-error>{{ $message }}</x-error>
                            @enderror
                        </td>
                        <td>
                            <button wire:click.debounce.1000ms="removeRegistration({{ $index }})"
                                @if (count($registrations) <= 1) disabled @endif class="btn btn-danger btn-sm">Remove
                                <i class="bx bx-trash"></i></button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3"></td>
                    <td>
                        <button @click="$wire.addRegistration()" @if (count($registrations) >= 10) disabled @endif
                            class="btn btn-warning btn-sm">Add Row <i class="bx bx-plus"></i></button>
                    </td>
                </tr>
            </tfoot>
            </tfoot>
        </table>



    </div>


    <!-- Do You Use Certified Seed -->
    <div x-show="type==='Farmers'" class="mb-3" x-data="{
        uses_certified_seed: $wire.entangle('uses_certified_seed'),
        type: $wire.entangle('type'),
    
        init() {
            this.$watch('type', (v) => {
                if (v != 'Farmers') {
                    $wire.resetValues('uses_certified_seed')
                }
            })
    
        }
    }">
        <label class="form-label">Do You Use Certified Seed</label>
        <div class="@error('uses_certified_seed') border border-danger @enderror">
            <div class="form-check">
                <input class="form-check-input" type="radio" id="useCertifiedSeedYes" value="1"
                    x-model="uses_certified_seed">
                <label class="form-check-label" for="useCertifiedSeedYes">Yes</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" id="useCertifiedSeedNo" value="0"
                    x-model="uses_certified_seed">
                <label class="form-check-label" for="useCertifiedSeedNo">No</label>
            </div>
        </div>
        @error('uses_certified_seed')
            <x-error>{{ $message }}</x-error>
        @enderror
    </div>



    <!-- More form fields... -->
</x-form-component>
