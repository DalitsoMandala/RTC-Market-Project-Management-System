<x-form-component :showAlpineAlerts="true" title="Add Rtc Consumption Data" pageTitle="Add Data" :formTitle="$form_name"
    :openSubmission="$openSubmission" :targetSet="$targetSet" :targetIds="$targetIds" :showTargetForm="true" :formName="$form_name">
    <div x-data="{
    
        location_data: $wire.entangle('location_data'),
        enterprise: null,
        entity_name: null,
        entity_type: null,
        district: null,
        epa: null,
        section: null,
        showHousehold: false,
        init() {
    
            this.$watch('enterprise', (value) => {
                this.location_data.enterprise = value;
            });
            this.$watch('entity_name', (value) => {
                this.location_data.entity_name = value;
    
            });
            this.$watch('entity_type', (value) => {
                this.location_data.entity_type = value;
    
                if (value === 'Nutrition intervention group') {
    
                    this.showHousehold = true;
                    $wire.female_count = 0;
                    $wire.male_count = 0;
                } else {
                    this.showHousehold = false;
                    $wire.number_of__households = 0;
                }
            });
            this.$watch('district', (value) => {
                this.location_data.district = value;
            });
            this.$watch('epa', (value) => {
                this.location_data.epa = value;
            });
            this.$watch('section', (value) => {
                this.location_data.section = value;
            });
        }
    }">




        <div id="locations">



            <div class="mb-3">
                <label for="location_data_entity_name" class="form-label">Entity Name</label>
                <input type="text"
                    class="form-control    @error('location_data.entity_name')
            is-invalid
        @enderror"
                    id="location_data_entity_name" x-model="entity_name">
                @error('location_data.entity_name')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>

            <div class="mb-3">
                <label for="" class="form-label">Entity Type</label>
                <select class="form-select @error('entity_type') is-invalid

        @enderror"x-model="entity_type">
                    <option selected value="">Select one</option>
                    <option>School</option>
                    <option>Nutrition intervention group</option>
                    <option>Household</option>
                    <option>Urban campains</option>
                </select>

                @error('entity_type')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>


            <div class="mb-3">
                <label for="location_data_district" class="form-label">District</label>

                <select class="form-select     @error('location_data.district') is-invalid @enderror"
                    x-model='district'>
                    @include('layouts.district-options')
                </select>
                @error('location_data.district')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>

            <div class="mb-3">
                <label for="location_data_epa" class="form-label">EPA</label>
                <input type="text"
                    class="form-control @error('location_data.epa')
            is-invalid
        @enderror"
                    id="location_data_epa" x-model="epa">
                @error('location_data.epa')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>

            <div class="mb-3">
                <label for="location_data_section" class="form-label">Section</label>
                <input type="text"
                    class="form-control @error('location_data.section')
            is-invalid
        @enderror"
                    id="location_data_section" x-model="section">
                @error('location_data.section')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>
        </div>
        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" class="form-control @error('date')
            is-invalid
        @enderror"
                id="date" wire:model="date">
            @error('date')
                <x-error>{{ $message }}</x-error>
            @enderror
        </div>

        <div class="mb-3">
            <label for="crop" class="form-label">Crop</label>

            <div class="list-group  @error('crop')border border-danger @enderror">
                <label class="mb-0 list-group-item text-capitalize">
                    <input class="form-check-input me-1" type="checkbox" wire:model='crop' value="cassava" />
                    Cassava
                </label>
                <label class="mb-0 list-group-item text-capitalize">
                    <input class="form-check-input me-1" type="checkbox" wire:model='crop' value="potato" />
                    Potato
                </label>
                <label class="mb-0 list-group-item text-capitalize">
                    <input class="form-check-input me-1" wire:model='crop' type="checkbox" value="sweet_potato" />
                    Sweet potato
                </label>

            </div>



            @error('crop')
                <x-error>{{ $message }}</x-error>
            @enderror
        </div>

        <div x-show="!showHousehold" x-data="{
            male_count: $wire.entangle('male_count'),
            female_count: $wire.entangle('female_count'),
            total: $wire.entangle('total'),
        }" id="total_count"
            x-effect="total = (parseInt(male_count) || 0) + (parseInt(female_count) || 0)">
            <div class="mb-3">
                <label for="male_count" class="form-label">Males</label>
                <input type="number"
                    class="form-control @error('male_count')
                is-invalid
            @enderror"
                    id="male_count" x-model="male_count">
                @error('male_count')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>

            <div class="mb-3">
                <label for="female_count" class="form-label">Females</label>
                <input type="number"
                    class="form-control @error('female_count')
                is-invalid
            @enderror"
                    id="female_count" x-model="female_count">
                @error('female_count')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>

            <div class="mb-3">
                <label for="total" class="form-label">Total</label>
                <input type="number" readonly
                    class="form-control bg-light @error('total')
                is-invalid
            @enderror"
                    id="total" x-model="total">
                @error('total')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>


        </div>


        <div class="mb-3" x-show="showHousehold">
            <label for="household" class="form-label">Number of households</label>
            <input type="number"
                class="form-control @error('number_of_households')
                is-invalid
                @enderror"
                id="household" wire:model="number_of_households" />

            @error('number_of_households')
                <x-error>{{ $message }}</x-error>
            @enderror
        </div>
    </div>
    <!-- More form fields... -->
</x-form-component>
