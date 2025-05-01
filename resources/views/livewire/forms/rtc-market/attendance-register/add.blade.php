@php
    use Ramsey\Uuid\Uuid;
    $uuid = Uuid::uuid4()->toString();
    $currentUrl = url()->current();
    $replaceUrl = str_replace('add', 'upload', $currentUrl) . "/{$uuid}";

@endphp
<x-form-component :showAlpineAlerts="true" title="Add Attendance Data" pageTitle="Add Data" :formTitle="$form_name" :openSubmission="$openSubmission"
    :targetSet="$targetSet" :targetIds="$targetIds" :showTargetForm="true" formName="attendance-register">

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
            <a href="{{ $routePrefix }}/forms/rtc-market/attendance-register/view">
                {{ ucwords(strtolower($form_name)) }}
            </a>
        </li>

    </x-slot>


    <h4>Meeting Details</h4>
    <div class="mb-3">
        <label for="meetingTitle" class="form-label">Meeting Title</label>
        <input type="text" wire:model="meetingTitle" class="form-control @error('meetingTitle') is-invalid @enderror"
            id="meetingTitle">
        @error('meetingTitle')
            <x-error>{{ $message }}</x-error>
        @enderror
    </div>

    <div class="mb-3">
        <label for="meetingCategory" class="form-label">Meeting Category</label>
        <div class="form-check">
            <input class="form-check-input @error('meetingCategory') is-invalid @enderror" type="radio"
                wire:model="meetingCategory" id="training" value="Training">
            <label class="form-check-label" for="training">Training</label>
        </div>
        <div class="form-check">
            <input class="form-check-input @error('meetingCategory') is-invalid @enderror" type="radio"
                wire:model="meetingCategory" checked id="meeting" value="Meeting">
            <label class="form-check-label" for="meeting">Meeting</label>
        </div>
        <div class="form-check">
            <input class="form-check-input @error('meetingCategory') is-invalid @enderror" type="radio"
                wire:model="meetingCategory" id="workshop" value="Workshop">
            <label class="form-check-label" for="workshop">Workshop</label>
        </div>
        @error('meetingCategory')
            <x-error>{{ $message }}</x-error>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Crop</label>
        <div class="form-check">
            <input class="form-check-input @error('rtcCrop') is-invalid @enderror" type="checkbox" wire:model="rtcCrop"
                value="Cassava" id="cassava">
            <label class="form-check-label" for="cassava">Cassava</label>
        </div>
        <div class="form-check">
            <input class="form-check-input @error('rtcCrop') is-invalid @enderror" type="checkbox" wire:model="rtcCrop"
                value="Potato" id="potato">
            <label class="form-check-label" for="potato">Potato</label>
        </div>
        <div class="form-check">
            <input class="form-check-input @error('rtcCrop') is-invalid @enderror" type="checkbox" wire:model="rtcCrop"
                value="Sweet potato" id="sweetPotato">
            <label class="form-check-label" for="sweetPotato">Sweet Potato</label>
        </div>
        @error('rtcCrop')
            <x-error>{{ $message }}</x-error>
        @enderror
    </div>

    <div class="mb-3">
        <label for="venue" class="form-label">Venue</label>
        <input type="text" wire:model="venue" class="form-control @error('venue') is-invalid @enderror"
            id="venue">
        @error('venue')
            <x-error>{{ $message }}</x-error>
        @enderror
    </div>

    <div class="mb-3">
        <label for="district" class="form-label">District</label>
        <select class="form-select @error('district')
            is-invalid
        @enderror" wire:model='district'>
            @include('layouts.district-options')
        </select>
        @error('district')
            <x-error>{{ $message }}</x-error>
        @enderror
    </div>

    <div class="mb-3 row">
        <div class="col">
            <label for="startDate" class="form-label">Start Date</label>
            <input type="date" wire:model="startDate" class="form-control @error('startDate') is-invalid @enderror"
                id="startDate">
            @error('startDate')
                <x-error>{{ $message }}</x-error>
            @enderror
        </div>
        <div class="col">
            <label for="endDate" class="form-label">End Date</label>
            <input type="date" wire:model="endDate" class="form-control @error('endDate') is-invalid @enderror"
                id="endDate">
            @error('endDate')
                <x-error>{{ $message }}</x-error>
            @enderror
        </div>
        <div class="col">
            <label for="totalDays" class="form-label">Total Number of Days</label>
            <input type="number" wire:model="totalDays"
                class="form-control @error('totalDays') is-invalid @enderror" id="totalDays" min="0">
            @error('totalDays')
                <x-error>{{ $message }}</x-error>
            @enderror
        </div>
    </div>
    @if (session()->has('attendance_register'))
        <div class="row justify-content-center">

            <button type="button" class="my-2 btn btn-danger btn-sm" data-bs-toggle="modal"
                data-bs-target="#clearMeetingDetailsModal">
                <i class="bx bx-arrow-to-top"></i> Clear Meeting Details
            </button>



        </div>
        <!-- Clear Meeting Details Confirmation Modal -->
        <div class="modal fade" id="clearMeetingDetailsModal" tabindex="-1"
            aria-labelledby="clearMeetingDetailsModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="clearMeetingDetailsModalLabel">Confirm
                            Clear Meeting Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to clear the above meeting details? This means you are supposed to fill
                            these details for again for all other participants. This action
                            cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-theme-red" wire:click="clearSessionData"
                            data-bs-dismiss="modal">Clear
                            Details</button>
                    </div>
                </div>
            </div>
        </div>
    @endif


    <hr>

    <h4 class="mb-3">Participants</h4>

    <div class="row">
        <div class="mb-3 col-12">
            <label for="name" class="form-label">Name</label>
            <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror"
                id="name">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3 col">
            <label for="sex" class="form-label">Sex</label>
            <select class="form-select @error('sex') is-invalid @enderror" wire:model="sex" id="sex">
                <option value="">Choose...</option>
                <option selected value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
            @error('sex')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3 col">
            <label for="organization" class="form-label">Organization</label>
            <input type="text" wire:model="organization"
                class="form-control @error('organization') is-invalid @enderror" id="organization">
            @error('organization')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="row">
        <div class="mb-3 col">
            <label for="designation" class="form-label">Designation</label>
            <input type="text" wire:model="designation"
                class="form-control @error('designation') is-invalid @enderror" id="designation">
            @error('designation')
                <x-error>{{ $message }}</x-error>
            @enderror
        </div>


        <div class="mb-3 col">
            <label for="category" class="form-label">Category</label>
            <select wire:model="category" class="form-select @error('category') is-invalid @enderror"
                id="category">
                <option value="">Select Designation</option>
                <option value="Farmer">Farmer</option>
                <option value="Processor">Processor</option>
                <option value="Trader">Trader</option>
                <option value="Partner">Partner</option>
                <option value="Staff">Staff</option>
                <option value="Other">Other</option>
            </select>
            @error('designation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>



        <div class="mb-3 col">
            <label for="phone_number" class="form-label">Phone Number</label>
            <x-phone type="tel" wire:model="phone_number" class="form-control" id="phone_number"
                :class="$errors->has('phone_number') ? 'is-invalid' : ''" />
            @error('phone_number')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" wire:model="email" class="form-control @error('email') is-invalid @enderror"
            id="email">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <!-- More form fields... -->
</x-form-component>
