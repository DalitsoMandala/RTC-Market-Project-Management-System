<x-form-component :showAlpineAlerts="true" title="Add Processors Data" pageTitle="Add Data" :formTitle="$form_name" :openSubmission="$openSubmission"
    :targetSet="$targetSet" :targetIds="$targetIds" :showTargetForm="true" :formName="$form_name" :hideSubmitButtons="true" :bypassTargets="true"
    :skipDraftScript="true">
    <x-display-id :id="$uniqueId" />

    <!-- Group Name -->
    <div class="mb-3">
        <label for="groupName" class="form-label">Group Name</label>
        <input type="text" class="form-control @error('location_data.group_name') is-invalid

    @enderror"
            id="groupName" wire:model='location_data.group_name'>
        @error('location_data.group_name')
            <x-error>{{ $message }}</x-error>
        @enderror
    </div>
    <div class="mb-3">
        <label for="" class="form-label ">ENTERPRISE</label>
        <div class="form-group">


            <select class="form-select @error('location_data.enterprise')
            is-invalid
        @enderror"
                wire:model='location_data.enterprise'>
                <option value="">Select one</option>
                <option selected value="Cassava">Cassava</option>
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

    <!-- Group -->
    <div class="mb-3" x-data="{ group: $wire.entangle('group'), type: $wire.entangle('type') }" x-init="() => {


    }">
        <label for="group" class="form-label">Group</label>
        <select class="form-select @error('group') is-invalid @enderror" x-model="group">
            <option value="">Select One</option>


            <option value="Producer organization (PO)">Producer organization (PO)
            </option>

            <option value="Large scale farm">Large scale farm</option>

            <option value="Large scale processor">Large scale processor</option>


            <option value="Small medium enterprise (SME)">Small medium enterprise (SME)</option>


            <option value="Other">Other</option>


        </select>

        @error('group')
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

    <!-- Category -->
    <div class="mb-3">
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
    @include('livewire.forms.rtc-market.rtc-production-processors.first')

    @include('livewire.forms.rtc-market.rtc-production-processors.repeats')

</x-form-component>
