<div>
    @section('title')
        Organisation forms
    @endsection
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Organisation forms</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active"> Organisation forms</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <div class="card ">
                    <div class="card-header">
                        <h5 class="card-title text-capitalize">Forms & Organisations</h5>
                    </div>
                    <div class="px-0 card-body">
                        <livewire:admin.organisation-forms-table />
                    </div>
                </div>
            </div>
        </div>



        <div x-data x-init="$wire.on('showModal', (e) => {

            const myModal = new bootstrap.Modal(document.getElementById(e.name), {})
            myModal.show();
        })">


            <x-modal id="view-modal" title="Edit Record">

                <x-alerts />
                <form wire:submit='save'>


                    <div class="mb-3">
                        <label for="" class="form-label">Indicator</label>
                        <select class="form-select bg-light" name="" disabled x-data>
                            @foreach ($indicators as $indicator)
                                <option value="{{ $indicator->id }}">{{ $indicator->indicator_name }}</option>
                            @endforeach


                        </select>


                    </div>


                    <div class="mb-3 d-none">
                        <label for="" class="form-label">Lead partners</label>

                        <div>
                            <div class="list-group">
                                @foreach ($leadPartners as $organisation)
                                    <label class="list-group-item">
                                        <input class="form-check-input me-1" checked type="checkbox" disabled
                                            value="{{ $organisation->name }}" />
                                        {{ $organisation->name }}
                                    </label>
                                @endforeach
                            </div>


                        </div>


                        

                    </div>

                    <div class="mb-3">
                        <label for="" class="form-label">Select partner for this indicator</label>
                        <select class="form-select  @error('selectedLeadPartner') is-invalid @enderror" name=""
                            id="" wire:model='selectedLeadPartner' multiple>

                            @foreach ($leadPartners as $organisation)
                                <option value="{{ $organisation->id }}">{{ $organisation->name }}</option>
                            @endforeach
                        </select>

                        @error('selectedLeadPartner')
                            <x-error>{{ $message }}</x-error>
                        @enderror
                    </div>

                    <!-- Some borders are removed -->
                    <h5>Forms for this indicator</h5>
                    <ul class="list-group list-group-flush">

                        @foreach ($forms as $form)
                            <li class="italic list-group-item">
                                <i class="bx bx-check text-success"></i> {{ $form->name }}
                            </li>
                        @endforeach

                    </ul>


                    {{-- <div class="mb-3">
                        <label for="" class="form-label">Select forms</label>
                        <select
                            class="form-select form-select @if (!$selectedLeadPartner) pe-none opacity-25 @endif"
                            name="" id="mySelect" wire:model='selectedForms' wire:loading.class='opacity-25'
                            multiple>
                            <option value="" disabled>Select one</option>
                            @foreach ($forms as $form)
                                <option value="{{ $form->id }}">{{ $form->name }}</option>
                            @endforeach
                        </select>


                        @error('selectedForms')
                            <x-error>{{ $message }}</x-error>
                        @enderror
                    </div> --}}


                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-warning">Save changes</button>

                    </div>
                </form>
            </x-modal>

        </div>




    </div>

</div>
@script
    <script>
        $('#mySelect').on('change', function() {
            let selectedValues = $(this).val(); // Get selected values

            if (selectedValues.includes('6')) { // If "All" (value 1) is selected
                // Deselect all other options
                $(this).find('option').not('[value="6"]').prop('disabled', true).prop('selected', false);
            } else {
                // Enable all options
                $(this).find('option').prop('disabled', false);
            }
        });
    </script>
@endscript
