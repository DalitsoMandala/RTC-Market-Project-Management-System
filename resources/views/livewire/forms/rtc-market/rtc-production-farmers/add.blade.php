@php
    use Ramsey\Uuid\Uuid;
    $uuid = Uuid::uuid4()->toString();
    $currentUrl = url()->current();
    $replaceUrl = str_replace('add', 'upload', $currentUrl) . "/{$uuid}";

@endphp
<x-form-component :showAlpineAlerts="true" title="Add Farmers Data" pageTitle="Add Data" :formTitle="$form_name" :openSubmission="$openSubmission"
    :targetSet="$targetSet" :targetIds="$targetIds" :showTargetForm="true" formName="rtc-production-and-marketing-form-farmers">

    <x-slot name="breadcrumbs">


        <li class="breadcrumb-item">
            <a href="/">Dashboard</a>
        </li>

        @role('admin|manager')
            <li class="breadcrumb-item">
                <a href="/cip/submission-period">Submission Periods</a>
            </li>
        @endrole

        @role('external')
            <li class="breadcrumb-item"></li>
            <a href="/external/submission-periods">Submission Periods</a>
            </li>
        @endrole

        <li class="breadcrumb-item active">Add Data</li>
        <li class="breadcrumb-item">
            <a href="{{ $replaceUrl }}">Upload Data</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ $routePrefix }}/forms/rtc-market/rtc-production-and-marketing-form-farmers/view">
                {{ ucwords(strtolower($form_name)) }}
            </a>
        </li>
    </x-slot>

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
    <div class="mb-3" x-data="{
        location_data: $wire.entangle('location_data'),
        enterprise: null,
        init() {
            this.$watch('enterprise', (value) => {
                this.location_data.enterprise = value;
            });
        }
    }">
        <label for="" class="form-label ">ENTERPRISE</label>
        <div class="form-group">

            <select class="form-select @error('location_data.enterprise')
            is-invalid
        @enderror"
                x-model='enterprise'>
                <option selected value="">Select one</option>
                <option value="Cassava">Cassava</option>
                <option value="Potato">Potato</option>
                <option value="Sweet potato">Sweet potato</option>
            </select>
        </div>

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

    @include('livewire.forms.rtc-market.rtc-production-farmers.first')

    @include('livewire.forms.rtc-market.rtc-production-farmers.repeats')

</x-form-component>
