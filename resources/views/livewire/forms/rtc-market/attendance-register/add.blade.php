<div>
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Dashboard</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active">Page Name</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row justify-content-center">
            <div class="col-md-8 col-sm-12">

                <h3 class="mb-5 text-center text-primary">ATTENDANCE REGISTER</h3>
                <x-alerts />

                @if (!$targetSet)
                    <livewire:forms.rtc-market.set-targets-form :submissionTargetIds="$targetIds" />
                @endif

                @if ($openSubmission === false)
                    <div class="alert alert-warning" role="alert">
                        You can not submit a form right now
                        because submissions are closed for the moment!
                    </div>
                @endif



                <div class=" mb-1 row justify-content-center @if ($openSubmission === false) opacity-25 pe-none @endif"
                    x-data="{
                        selectedFinancialYear: $wire.entangle('selectedFinancialYear'),
                        selectedMonth: $wire.entangle('selectedMonth'),
                        selectedIndicator: $wire.entangle('selectedIndicator'),
                    }">


                    <form wire:submit.debounce.1s='save'>
                        <div class="card">
                            <div class="card-body">
                                <h4>Meeting Details</h4>
                                <div class="mb-3">
                                    <label for="meetingTitle" class="form-label">Meeting Title</label>
                                    <input type="text" wire:model.lazy="meetingTitle"
                                        class="form-control @error('meetingTitle') is-invalid @enderror"
                                        id="meetingTitle">
                                    @error('meetingTitle')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="meetingCategory" class="form-label">Meeting Category</label>
                                    <div class="form-check">
                                        <input class="form-check-input @error('meetingCategory') is-invalid @enderror"
                                            type="radio" wire:model="meetingCategory" id="training" value="Training">
                                        <label class="form-check-label" for="training">Training</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input @error('meetingCategory') is-invalid @enderror"
                                            type="radio" wire:model="meetingCategory" id="meeting" value="Meeting">
                                        <label class="form-check-label" for="meeting">Meeting</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input @error('meetingCategory') is-invalid @enderror"
                                            type="radio" wire:model="meetingCategory" id="workshop" value="Workshop">
                                        <label class="form-check-label" for="workshop">Workshop</label>
                                    </div>
                                    @error('meetingCategory')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">RTC Crop</label>
                                    <div class="form-check">
                                        <input class="form-check-input @error('rtcCrop') is-invalid @enderror"
                                            type="checkbox" wire:model="rtcCrop" value="Cassava" id="cassava">
                                        <label class="form-check-label" for="cassava">Cassava</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input @error('rtcCrop') is-invalid @enderror"
                                            type="checkbox" wire:model="rtcCrop" value="Potato" id="potato">
                                        <label class="form-check-label" for="potato">Potato</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input @error('rtcCrop') is-invalid @enderror"
                                            type="checkbox" wire:model="rtcCrop" value="Sweet potato" id="sweetPotato">
                                        <label class="form-check-label" for="sweetPotato">Sweet Potato</label>
                                    </div>
                                    @error('rtcCrop')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="venue" class="form-label">Venue</label>
                                    <input type="text" wire:model.lazy="venue"
                                        class="form-control @error('venue') is-invalid @enderror" id="venue">
                                    @error('venue')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="district" class="form-label">District</label>
                                    <select
                                        class="form-select @error('district')
                                        is-invalid
                                    @enderror"
                                        wire:model='district'>
                                        @include('layouts.district-options')
                                    </select>
                                    @error('district')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>

                                <div class="mb-3 row">
                                    <div class="col">
                                        <label for="startDate" class="form-label">Start Date</label>
                                        <input type="date" wire:model="startDate"
                                            class="form-control @error('startDate') is-invalid @enderror"
                                            id="startDate">
                                        @error('startDate')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>
                                    <div class="col">
                                        <label for="endDate" class="form-label">End Date</label>
                                        <input type="date" wire:model="endDate"
                                            class="form-control @error('endDate') is-invalid @enderror"
                                            id="endDate">
                                        @error('endDate')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>
                                    <div class="col">
                                        <label for="totalDays" class="form-label">Total Number of Days</label>
                                        <input type="number" wire:model="totalDays"
                                            class="form-control @error('totalDays') is-invalid @enderror"
                                            id="totalDays" min="0">
                                        @error('totalDays')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>
                                </div>
                                @if (session()->has('meetingTitle'))
                                    <div class="row justify-content-end">
                                        <div class="col">
                                            <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#clearMeetingDetailsModal">
                                                Clear Meeting Details
                                            </button>

                                        </div>

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
                                                    <p>Are you sure you want to clear all meeting details? This action
                                                        cannot be undone.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Cancel</button>
                                                    <button type="button" class="btn btn-danger"
                                                        wire:click="clearSessionData" data-bs-dismiss="modal">Clear
                                                        Details</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">

                                <h4 class="mb-3">Participants</h4>

                                <div class="row">
                                    <div class="mb-3 col-12">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" wire:model="name"
                                            class="form-control @error('name') is-invalid @enderror" id="name">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3 col">
                                        <label for="sex" class="form-label">Sex</label>
                                        <select class="form-select @error('sex') is-invalid @enderror"
                                            wire:model="sex" id="sex">
                                            <option disabled value="">Choose...</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                        @error('sex')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3 col">
                                        <label for="organization" class="form-label">Organization</label>
                                        <input type="text" wire:model="organization"
                                            class="form-control @error('organization') is-invalid @enderror"
                                            id="organization">
                                        @error('organization')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="mb-3 col">
                                        <label for="designation" class="form-label">Designation</label>
                                        <select wire:model="designation"
                                            class="form-select @error('designation') is-invalid @enderror"
                                            id="designation">
                                            <option value="">Select Designation</option>
                                            <option value="Farmer">Farmer</option>
                                            <option value="Processor">Processor</option>
                                            <option value="Trader">Trader</option>
                                            <option value="Partner">Partner</option>
                                            <option value="Staff">Staff</option>
                                            {{-- <option value="Other">Other</option> --}}
                                        </select>
                                        @error('designation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>


                                    <div class="mb-3 col">
                                        <label for="phone_number" class="form-label">Phone Number</label>
                                        <x-phone type="tel" wire:model="phone_number" class="form-control"
                                            id="phone_number" :class="$errors->has('phone_number') ? 'is-invalid' : ''" />
                                        @error('phone_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" wire:model="email"
                                        class="form-control @error('email') is-invalid @enderror" id="email">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-grid col-12 justify-content-center" x-data>

                                    <button class=" btn btn-primary "
                                        @click="window.scrollTo({
top: 0,
behavior: 'smooth'
})"
                                        type="submit">Submit</button>
                                </div>
                            </div>



                        </div>

                    </form>

                </div>






            </div>
        </div>








    </div>
    @script
        <script>
            let textInputs = document.querySelectorAll('input[type="text"]');

            // Attach event listener to each input
            textInputs.forEach(function(input) {
                input.addEventListener('input', function() {
                    // Convert input value to uppercase
                    this.value = this.value.toUpperCase();
                });

            });

            document.querySelectorAll('input[type="number"]').forEach(function(input) {
                input.setAttribute('step', '0.01');
            });
        </script>
    @endscript
</div>
