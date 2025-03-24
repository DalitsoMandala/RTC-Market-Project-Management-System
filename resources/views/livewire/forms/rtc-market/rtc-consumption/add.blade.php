<div>
    @section('title')
        Add Rtc Consumption Data
    @endsection

    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Dashboard</h4>

                    <div class="page-title-right" wire:ignore>
                        @php
                            use Ramsey\Uuid\Uuid;
                            $uuid = Uuid::uuid4()->toString();
                            $currentUrl = url()->current();
                            $replaceUrl = str_replace('add', 'upload', $currentUrl) . "/{$uuid}";

                        @endphp
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active">Add Data</li>

                            <li class="breadcrumb-item">
                                <a href="{{ $replaceUrl }}">Upload Data</a>
                            </li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <h3 class="mb-5 text-center text-warning">RTC CONSUMPTION FORM</h3>

                <x-alerts />

                @if (!$targetSet)
                    <livewire:forms.rtc-market.set-targets-form :submissionTargetIds="$targetIds" />
                @endif

                @if ($openSubmission === false)
                    <div class="alert alert-danger" role="alert">
                        You can not submit a form right now
                        because submissions are closed for the moment!
                    </div>
                @endif



                <div class="mb-1 row justify-content-center  @if ($openSubmission === false) opacity-25  pe-none @endif"
                    x-data="{
                        selectedFinancialYear: $wire.entangle('selectedFinancialYear').live,
                        selectedMonth: $wire.entangle('selectedMonth').live,
                        selectedIndicator: $wire.entangle('selectedIndicator').live,
                    }">



                    <div class="col-12 col-md-8">
                        <form wire:submit='save'>
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        Add Data
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="location_data_entity_name" class="form-label">Entity Name</label>
                                        <input type="text"
                                            class="form-control    @error('location_data.entity_name')
                                            is-invalid
                                        @enderror"
                                            id="location_data_entity_name" wire:model="location_data.entity_name">
                                        @error('location_data.entity_name')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="" class="form-label">Entity Type</label>
                                        <select
                                            class="form-select @error('entity_type') is-invalid

                                        @enderror"
                                            wire:model="entity_type">
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

                                        <select
                                            class="form-select     @error('location_data.district') is-invalid @enderror"
                                            wire:model='location_data.district'>
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
                                            id="location_data_epa" wire:model="location_data.epa">
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
                                            id="location_data_section" wire:model="location_data.section">
                                        @error('location_data.section')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="date" class="form-label">Date</label>
                                        <input type="date"
                                            class="form-control @error('date')
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
                                                <input class="form-check-input me-1" type="checkbox" wire:model='crop'
                                                    value="cassava" />
                                                Cassava
                                            </label>
                                            <label class="mb-0 list-group-item text-capitalize">
                                                <input class="form-check-input me-1" type="checkbox" wire:model='crop'
                                                    value="potato" />
                                                Potato
                                            </label>
                                            <label class="mb-0 list-group-item text-capitalize">
                                                <input class="form-check-input me-1" wire:model='crop' type="checkbox"
                                                    value="sweet_potato" />
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
                                        <label for="male_count" class="form-label">Males</label>
                                        <input type="number"
                                            class="form-control @error('male_count')
                                            is-invalid
                                        @enderror"
                                            id="male_count" wire:model.live.debounce.600ms="male_count">
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
                                            id="female_count" wire:model.live.debounce.600ms="female_count">
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
                                            id="total" wire:model="total">
                                        @error('total')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>


                                    <div class="d-grid justify-content-center">

                                        <button class="px-5 mt-2 btn btn-warning "
                                            @click="window.scrollTo({
                                            top: 0,
                                            behavior: 'smooth'
                                        })"
                                            type="submit">Submit Data</button>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>




                </div>

            </div>
        </div>






    </div>

</div>
@assets
    <style>
        input[type="text"] {
            text-transform: uppercase;
        }
    </style>
@endassets
@script
    <script>
        // Function to transform input text to uppercase
        function enforceUppercase(element) {
            element.value = element.value.toUpperCase();
        }

        // Attach event listener to all current and future text inputs
        document.addEventListener('input', function(event) {
            if (event.target.tagName === 'INPUT' && event.target.type === 'text') {
                enforceUppercase(event.target);
            }
        });
    </script>
@endscript
