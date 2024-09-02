<div>
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Dashboard</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active">Page Name</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <x-alerts />
                <div class="card">

                    <div class="card-header">
                        <h3 class="card-title">Attendance Register</h3>
                    </div>
                    <div class="card-header" x-data="{ is_open: true }">



                        <div class="col-12 col-md-8 col-md-sm-8" id="form">


                            <form wire:submit='save'>



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
                                            type="radio" wire:model="meetingCategory" id="training" value="TRAINING">
                                        <label class="form-check-label" for="training">Training</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input @error('meetingCategory') is-invalid @enderror"
                                            type="radio" wire:model="meetingCategory" id="meeting" value="MEETING">
                                        <label class="form-check-label" for="meeting">Meeting</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input @error('meetingCategory') is-invalid @enderror"
                                            type="radio" wire:model="meetingCategory" id="workshop" value="WORKSHOP">
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
                                            type="checkbox" wire:model="rtcCrop" value="CASSAVA" id="cassava">
                                        <label class="form-check-label" for="cassava">Cassava</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input @error('rtcCrop') is-invalid @enderror"
                                            type="checkbox" wire:model="rtcCrop" value="POTATO" id="potato">
                                        <label class="form-check-label" for="potato">Potato</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input @error('rtcCrop') is-invalid @enderror"
                                            type="checkbox" wire:model="rtcCrop" value="SWEET POTATO" id="sweetPotato">
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
                                        <input type="text" wire:model="designation"
                                            class="form-control @error('designation') is-invalid @enderror"
                                            id="designation">
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


                                <button type="submit" class="px-5 btn btn-primary btn-lg"
                                    @click="window.scrollTo({
                                    top: 0,
                                    behavior: 'smooth'
                                })">Submit</button>


                            </form>

                        </div>





                    </div>
                    <div class="card-body" id="table">
                        <livewire:tables.rtc-market.attendance-register-table :count="5" />
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
