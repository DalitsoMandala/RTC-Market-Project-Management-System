<div>
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Forms</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active">Forms</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <div class="card ">
                    <div class="card-header fw-bold">
                        Forms Table
                    </div>
                    <div class="px-0 card-body">
                        <livewire:tables.form-table />
                    </div>
                </div>
            </div>
        </div>





        <div x-data x-init="$wire.on('showModal', (e) => {
            $wire.setData(e.rowId);
            const myModal = new bootstrap.Modal(document.getElementById(e.name), {})
            myModal.show();
        })
        $wire.on('hideModal', (e) => {
            const modals = document.querySelectorAll('.modal.show');
        
            // Iterate over each modal and hide it using Bootstrap's modal hide method
            modals.forEach(modal => {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            });
        })">


            <x-modal id="view-form-modal" title="edit">
                <form wire:submit='save'>
                    <div class="mb-3">

                        <x-text-input placeholder="Name of form..." wire:model='name' />
                        @error('name')
                            <span class="my-1 text-danger">{{ $message }}</span>
                        @enderror
                    </div>


                    <div class="mb-3">

                        <select class="form-select form-select-sm" wire:model='type'>
                            <option disabled value="baseline">Baseline</option>
                            <option value="routine/recurring">routine/recurring</option>

                        </select>

                        @error('type')
                            <span class="my-1 text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-warning">Save changes</button>

                    </div>
                </form>
            </x-modal>

        </div>




    </div>

</div>
