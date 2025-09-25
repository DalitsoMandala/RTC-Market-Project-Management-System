@php
    use Ramsey\Uuid\Uuid;
    $uuid = Uuid::uuid4()->toString();
    $currentUrl = url()->current();
    $replaceUrl = str_replace('add', 'upload', $currentUrl) . "/{$uuid}";

@endphp
<x-form-component :showAlpineAlerts="true" title="Add Recruitment Data" pageTitle="Add Data" :formTitle="$form_name" :openSubmission="$openSubmission"
    :targetSet="$targetSet" :targetIds="$targetIds" :showTargetForm="true" formName="rtc-actor-recruitment-form">

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
            <a href="/external/submission-period">Submission Periods</a>
            </li>
        @endrole

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
        <label for="location_data_school_name" class="form-label">SCHOOL NAME</label>
        <input type="text"
            class="form-control    @error('location_data.school_name')
            is-invalid
        @enderror"
            id="location_data_school_name" wire:model="location_data.school_name">
        @error('location_data.school_name')
            <x-error>{{ $message }}</x-error>
        @enderror
    </div>

    <div class="mb-3">
        <label for="location_data_district" class="form-label">DISTRICT</label>

        <select class="form-select     @error('location_data.district') is-invalid @enderror"
            wire:model='location_data.district'>
            @include('layouts.district-options')
        </select>
        @error('location_data.district')
            <x-error>{{ $message }}</x-error>
        @enderror
    </div>

    <div class="mb-3">
        <label for="location_data_epa" class="form-label">EPA</label>
        <input type="text" class="form-control @error('location_data.epa')
            is-invalid
        @enderror"
            id="location_data_epa" wire:model="location_data.epa">
        @error('location_data.epa')
            <x-error>{{ $message }}</x-error>
        @enderror
    </div>

    <div class="mb-3">
        <label for="location_data_section" class="form-label">SECTION</label>
        <input type="text"
            class="form-control @error('location_data.section')
            is-invalid
        @enderror"
            id="location_data_section" wire:model="location_data.section">
        @error('location_data.section')
            <x-error>{{ $message }}</x-error>
        @enderror
    </div>

    <div class="mb-3">
        <label for="date" class="form-label">DATE</label>
        <input type="date" class="form-control @error('date')
            is-invalid
        @enderror"
            id="date" wire:model="date">
        @error('date')
            <x-error>{{ $message }}</x-error>
        @enderror
    </div>

    <div class="mb-3">
        <label for="crop" class="form-label">CROP</label>

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

        <!-- <select class="form-select form-select-md" wire:model="crop">

            <option>CASSAVA</option>
            <option>POTATO</option>
            <option>SWEET POTATO</option>
        </select> -->

        @error('crop')
            <x-error>{{ $message }}</x-error>
        @enderror
    </div>

    <div class="mb-3">
        <label for="male_count" class="form-label">MALES</label>
        <input type="number" class="form-control @error('male_count')
            is-invalid
        @enderror"
            id="male_count" wire:model.live.debounce.600ms="male_count">
        @error('male_count')
            <x-error>{{ $message }}</x-error>
        @enderror
    </div>

    <div class="mb-3">
        <label for="female_count" class="form-label">FEMALE</label>
        <input type="number" class="form-control @error('female_count')
            is-invalid
        @enderror"
            id="female_count" wire:model.live.debounce.600ms="female_count">
        @error('female_count')
            <x-error>{{ $message }}</x-error>
        @enderror
    </div>

    <div class="mb-3">
        <label for="total" class="form-label">TOTAL</label>
        <input type="number" readonly
            class="form-control bg-light @error('total')
            is-invalid
        @enderror" id="total"
            wire:model="total">
        @error('total')
            <x-error>{{ $message }}</x-error>
        @enderror
    </div>



    <!-- More form fields... -->
</x-form-component>
