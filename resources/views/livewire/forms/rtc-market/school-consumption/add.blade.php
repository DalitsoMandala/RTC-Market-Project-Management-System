<div>

    <style>
        input,
        select,
        label {
            text-transform: uppercase;
        }
    </style>
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
                <h3 class="mb-5 text-center text-primary">SCHOOL CONSUMPTION FORM</h3>

                <x-alerts />


                @if ($openSubmission === false)
                    <div class="alert alert-warning" role="alert">
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
                                        <label for="location_data_school_name" class="form-label">SCHOOL NAME</label>
                                        <input type="text" class="form-control    @error('location_data.school_name')
                                            is-invalid
                                        @enderror" id="location_data_school_name"
                                            wire:model="location_data.school_name">
                                        @error('location_data.school_name')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="location_data_district" class="form-label">DISTRICT</label>

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
                                        <input type="text" class="form-control @error('location_data.epa')
                                            is-invalid
                                        @enderror" id="location_data_epa" wire:model="location_data.epa">
                                        @error('location_data.epa')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="location_data_section" class="form-label">SECTION</label>
                                        <input type="text" class="form-control @error('location_data.section')
                                            is-invalid
                                        @enderror" id="location_data_section" wire:model="location_data.section">
                                        @error('location_data.section')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="date" class="form-label">DATE</label>
                                        <input type="date" class="form-control @error('date')
                                            is-invalid
                                        @enderror" id="date" wire:model="date">
                                        @error('date')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="crop" class="form-label">CROP</label>

                                        <select class="form-select form-select-md" wire:model="crop">

                                            <option>CASSAVA</option>
                                            <option>POTATO</option>
                                            <option>SWEET POTATO</option>
                                        </select>

                                        @error('crop')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="male_count" class="form-label">MALES</label>
                                        <input type="number" class="form-control @error('male_count')
                                            is-invalid
                                        @enderror" id="male_count" wire:model.live.debounce.600ms="male_count">
                                        @error('male_count')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="female_count" class="form-label">FEMALE</label>
                                        <input type="number" class="form-control @error('female_count')
                                            is-invalid
                                        @enderror" id="female_count" wire:model.live.debounce.600ms="female_count">
                                        @error('female_count')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="total" class="form-label">TOTAL</label>
                                        <input type="number" readonly class="form-control bg-light @error('total')
                                            is-invalid
                                        @enderror" id="total" wire:model="total">
                                        @error('total')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>


                                    <div class="d-grid justify-content-center">

                                        <button class="btn btn-success btn-lg" @click="window.scrollTo({
                                            top: 0,
                                            behavior: 'smooth'
                                        })" type="submit">Submit</button>
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
    document.addEventListener('input', function (event) {
        if (event.target.tagName === 'INPUT' && event.target.type === 'text') {
            enforceUppercase(event.target);
        }
    });
</script>
@endscript