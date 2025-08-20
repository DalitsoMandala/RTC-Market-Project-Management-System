<div>
    @section('title')
        System setup
    @endsection
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">System setup</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active">System setup</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <x-alerts />

                <!-- Site Name/Title Card -->
                <div class="mb-4 card">
                    <div class="card-header">
                        <h5>System Details</h5>
                    </div>
                    <div class="card-body">
                        <form wire:submit.prevent="save">

                            <!-- Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label">Site Name</label>
                                <input type="text" class="form-control" id="name" wire:model="name">
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Address -->
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" wire:model="address"></textarea>
                                @error('address')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Website -->
                            <div class="mb-3">
                                <label for="website" class="form-label">Website</label>
                                <input type="text" class="form-control" id="website" wire:model="website">
                                @error('website')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phone" wire:model="phone">
                                @error('phone')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" class="form-control" id="email" wire:model="email">
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>


                            <button type="submit" class="btn btn-warning">Save Changes</button>
                        </form>
                    </div>
                </div>


                <!-- Maintenance Mode Card -->
                <div class="mb-4 card col-5">
                    <div class="card-header">
                        <h5>Maintenance Mode</h5>
                    </div>
                    <div class="card-body">

                        <div class="mb-3 form-check form-switch" x-data="{ maintenance_mode: $wire.entangle('maintenance_mode') }">
                            <input class="form-check-input" type="checkbox" id="maintenance_mode" :checked="maintenance_mode"
                                wire:model.live.debounce.600ms="maintenance_mode">
                            <label class="form-check-label" for="maintenance_mode">Enable Maintenance Mode</label>
                        </div>

                        <div class=" form-icon right" x-data="{ tooltip: 'Copy to clipboard',
                        tooltipIcon: 'bx-copy',
                        secretKey: $wire.entangle('secretKey'),
                        toggleTooltip() {
                        this.tooltip = this.tooltip === 'Copy to clipboard' ? 'Copied!' : 'Copy to clipboard';
                        this.tooltipIcon = this.tooltipIcon === 'bx-copy' ? 'bx-check' : 'bx-copy';
                        setTimeout(() => {
                            this.tooltip = 'Copy to clipboard';
                            this.tooltipIcon = 'bx-copy';
                        }, 1500);
                        }
                        }">
                            <input type="text" readonly class="form-control form-control-icon" id="iconrightInput"
                                placeholder="Secret Key" wire:model="secretKey">
                            <i class="p-0 cursor-pointer bx btn custom-tooltip text-warning" :class="tooltipIcon" @click="toggleTooltip" :title="tooltip"></i>

                        </div>
  <small class="mt-1 mb-3 text-muted d-block">Use this secret key to bypass Maintenance Mode</small>
                        <button class="btn btn-warning" data-bs-toggle="modal"
                            data-bs-target="#confirmingMaintenanceMode">Confirm</button>
                    </div>
                </div>


                <!-- if you want to close by clicking outside the modal, delete the last endpoint:data-bs-backdrop and data-bs-keyboard -->
                <div wire:ignore.self class="modal fade" id="confirmingMaintenanceMode" tabindex="-1"
                    data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-md" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Confirm Maintenance Mode</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to {{ $maintenance_mode ? 'enable' : 'disable' }} Maintenance
                                    Mode?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-warning" wire:click="saveMaintananceMode">Yes,
                                    Proceed</button>
                            </div>
                        </div>
                    </div>
                </div>



            </div>
        </div>

<div x-data x-init="     $wire.on('hideModal', (e) => {
            const modals = document.querySelectorAll('.modal.show');

            // Iterate over each modal and hide it using Bootstrap's modal hide method
            modals.forEach(modal => {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            });
window.scrollTo({
        top: 0,
        behavior: 'smooth'
    })

        })">

</div>

        {{-- <div x-data x-init="$wire.on('showModal', (e) => {

            const myModal = new bootstrap.Modal(document.getElementById(e.name), {})
            myModal.show();
        })">


            <x-modal id="view-indicator-modal" title="edit">
                <form>
                    <div class="mb-3">

                        <x-text-input placeholder="Name of indicator..." />
                    </div>

                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-warning">Save changes</button>

                    </div>
                </form>
            </x-modal>

        </div> --}}




    </div>

</div>
