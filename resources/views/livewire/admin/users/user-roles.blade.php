<div>

    @section('title')
        User roles
    @endsection
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">User roles</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active">Manage User Roles</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                  <ul class=" nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="batch-tab" data-bs-toggle="tab" data-bs-target="#normal"
                            type="button" role="tab" aria-controls="home" aria-selected="true">
                            USER ROLES
                        </button>
                    </li>




                </ul>
                <div class="card ">
                    <div class="card-header d-flex justify-content-end">
                        <button class="btn btn-warning disabled"
                            wire:click="$dispatch('showModal', {name: 'view-role-modal'})">Create new role</button>
                    </div>
                    <div class="card-body">
                        <livewire:tables.user-roles-table />
                    </div>
                </div>
            </div>
        </div>



        <div x-data x-init="$wire.on('showModal', (e) => {

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


            <x-modal id="view-role-modal" title="Manage roles">
                <x-alpine-alerts />
                <form wire:submit='save'>
                    <div class="mb-3">
                        <label for="">User role</label>
                        <input type="text" class="form-control @error('role') is-invalid @enderror"
                            placeholder="Name of role..." wire:model='role' />
                        @error('role')
                            <x-error>{{ $message }}</x-error>
                        @enderror
                    </div>

                    <div class="modal-footer border-top-0 d-flex justify-content-center">
                        <button type="button" class="px-5 btn btn-theme-red" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-5 btn btn-warning">Submit</button>

                    </div>
                </form>
            </x-modal>

        </div>




    </div>

</div>
