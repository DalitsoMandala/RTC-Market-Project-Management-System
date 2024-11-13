<div>
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Lead partners</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active text-capitalize">Manage Indicators and lead partners</li>
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
                        <h5 class="card-title text-capitalize">Indicator leads Table</h5>
                    </div>
                    <div class="card-body">
                        <livewire:admin.indicator-lead-table />
                    </div>
                </div>
            </div>
        </div>



        <div x-data x-init="$wire.on('showModal', (e) => {

            const myModal = new bootstrap.Modal(document.getElementById(e.name), {})
            myModal.show();
        })

        $wire.on('refresh', (e) => {
            const modals = document.querySelectorAll('.modal.show');

            // Iterate over each modal and hide it using Bootstrap's modal hide method
            modals.forEach(modal => {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            });
        })">


            <x-modal id="view-modal" title="Edit record">
                <form wire:submit='save'>
                    <div class="mb-3">
                        <label for="" class="form-label">Indicator</label>
                        <select class="form-select form-select bg-light" name="" disabled x-data>
                            @foreach ($indicators as $indicator)
                                <option value="{{ $indicator->id }}">{{ $indicator->indicator_name }}</option>
                            @endforeach


                        </select>


                    </div>


                    <div class="mb-3">
                        <label for="" class="form-label">Lead partners</label>

                        <div x-data="{
                            selected: {},

                        }" x-init="() => {

                            $('#selectInput').select2({
                                width: '100%',
                                theme: 'bootstrap-5',
                                containerCssClass: 'select2--small',
                                dropdownCssClass: 'select2--small',
                            });

                            $wire.on('updateSelect', (e) => {
                                $('#selectInput').val(e.data);
                                $('#selectInput').trigger('change');

                            })

                            $('#selectInput').on('change', function(e) {
                                var selectedValue = $(this).val();
                                $wire.selectedLeadPartners = selectedValue;
                            });
                        }" wire:ignore>
                            <select class="form-select form-select " name="" id="selectInput" multiple
                                x-model="selected">
                                <option value="" disabled>Select one...</option>
                                @foreach ($leadPartners as $organisation)
                                    <option value="{{ $organisation->id }}">{{ $organisation->name }}</option>
                                @endforeach


                            </select>

                        </div>


                        @error('selectedLeadPartners')
                            <x-error>{{ $message }}</x-error>
                        @enderror

                    </div>

                    <div class="mb-3">
                        <label for="" class="form-label">Forms</label>

                        <div x-data="{
                            selected: {},

                        }" x-init="() => {

                            $('#selectInput2').select2({
                                width: '100%',
                                theme: 'bootstrap-5',
                                containerCssClass: 'select2--small',
                                dropdownCssClass: 'select2--small',
                            });

                            $wire.on('updateSelect', (e) => {
                                $('#selectInput2').val(e.formData);
                                $('#selectInput2').trigger('change');

                            })

                            $('#selectInput2').on('change', function(e) {
                                var selectedValue = $(this).val();
                                $wire.selectedForms = selectedValue;
                            });
                        }" wire:ignore>
                            <select class="form-select form-select " name="" id="selectInput2" multiple
                                x-model="selected">
                                <option value="" disabled>Select one...</option>
                                @foreach ($forms as $form)
                                    <option value="{{ $form->id }}">{{ $form->name }}</option>
                                @endforeach


                            </select>

                        </div>


                        @error('selectedForms')
                            <x-error>{{ $message }}</x-error>
                        @enderror

                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-warning mt-5">
                            Submit
                        </button>
                    </div>

                </form>
            </x-modal>

        </div>




    </div>

</div>