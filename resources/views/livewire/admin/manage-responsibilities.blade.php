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
                    <div class="card-body">
                        <livewire:tables.responsibilities-table />
                    </div>
                </div>
            </div>
        </div>



        <div x-data x-init="$wire.on('showModal', (e) => {
            $wire.set('rowId', e.rowId);
            const myModal = new bootstrap.Modal(document.getElementById(e.name), {})
            myModal.show();
        })">

            <x-modal id="view-people-modal" title="Assign forms to partner">
                <form wire:submit.debounce.600ms='save'>
                    <x-alerts />
                    <div class="mb-3">
                        <label for="" class="form-label">Organisation</label>
                        <input type="text" wire:model='organisation' readonly class="form-control"
                            aria-describedby="helpId" />

                    </div>
                    <div class="mb-3">
                        <label for="" class="form-label">Indicator</label>
                        <input type="text" wire:model='indicator' readonly class="form-control"
                            aria-describedby="helpId" />

                    </div>


                    <div class="mb-3">
                        <label for="" class="form-label">Forms</label>

                        <div wire:ignore x-data="{
                        
                        
                        
                        }" x-init="() => {
                            $('#form-select').select2({
                                dropdownParent: $('#view-people-modal'),
                                width: '100%',
                                theme: 'bootstrap-5',
                                containerCssClass: 'select2--small',
                                dropdownCssClass: 'select2--small',
                            });
                        
                        
                            $('#form-select').on('change', function() {
                        
                                data = $(this).val();
                        
                                $wire.selectedForms = data;
                        
                        
                            });
                        
                            $wire.on('select-forms', (e) => {
                                data = e.data;
                                $('#form-select').val(data).trigger('change');
                        
                        
                        
                        
                            })
                        
                            $wire.on('refreshSelect2', (e) => {
                                $('#form-select').empty(); // Clear current options
                        
                                const forms = e.data;
                                forms.forEach(form => {
                                    var newOption = new Option(form.name, form.id, false, false);
                                    $('#form-select').append(newOption).trigger('change');
                                });
                            });
                        
                        }">
                            <select class="form-select " multiple name="" id="form-select">


                                @foreach ($forms as $form)
                                    <option value="{{ $form->id }}">{{ $form->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('selectedForms')
                            <x-error>{{ $message }}</x-error>
                        @enderror
                    </div>



                    <div class="modal-footer border-top-0" x-data x-init="$wire.on('closeModal', () => {
                        $('#closeModal').trigger('click');
                    })">
                        <button type="button" class="btn btn-secondary" id="closeModal"
                            data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>

                    </div>
                </form>
            </x-modal>

        </div>




    </div>

</div>
