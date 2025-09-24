<div>
    @section('title')
        System setup
    @endsection
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">


                    <div class="page-title-left col-12">
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

                <!-- System Details Card -->
                <div class="mb-4 card">
                    <x-card-header>System Details</x-card-header>
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

                <div class="row">
                    <!-- Maintenance Mode Card -->
                    <div class="mb-4 col-md-6">
                        <div class="card h-100">
                            <x-card-header>System Maintenance</x-card-header>
                            <div class="card-body">

                                <div class="mb-3 form-check form-switch" x-data="{ maintenance_mode: $wire.entangle('maintenance_mode') }">
                                    <input class="form-check-input" type="checkbox" id="maintenance_mode"
                                        :checked="maintenance_mode" wire:model.live.debounce.600ms="maintenance_mode">
                                    <label class="form-check-label" for="maintenance_mode"> Maintenance
                                        Mode <span class="badge " :class="maintenance_mode ? 'bg-success' : 'bg-danger'"
                                            x-text="maintenance_mode ? 'ON' : 'OFF'"></span></label>
                                </div>

                                <div class="form-icon right" x-data="{
                                    tooltip: 'Copy to clipboard',
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
                                    <input type="text" readonly class="form-control form-control-icon"
                                        placeholder="Secret Key" wire:model="secretKey">
                                    <i class="p-0 cursor-pointer bx btn custom-tooltip text-warning"
                                        :class="tooltipIcon" @click="toggleTooltip" :title="tooltip"></i>
                                </div>

                                <small class="mt-1 mb-3 text-muted d-block">Use this secret key to bypass Maintenance
                                    Mode</small>

                                <button class="btn btn-warning"
                                    onclick="   window.scrollTo({
                     top: 0,
                     behavior: 'smooth'
                 })"
                                    data-bs-toggle="modal" data-bs-target="#confirmingMaintenanceMode">Confirm</button>
                            </div>
                        </div>
                    </div>

                    <!-- Cache Clearing Card -->
                    <div class="mb-4 col-md-6">
                        <div class="card h-100">
                            <x-card-header>Cache Clearing</x-card-header>
                            <div class="card-body">
                                <form wire:submit='clearCache'>
                                    <p class="text-muted">Clear application, config, and route cache.
                                        Recommended during off-peak hours.</p>

                                    <button class="btn btn-warning" onclick="window.scrollTo({
                     top: 0,
                     behavior: 'smooth'
                 })" type="submit">
                                        Clear Cache
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Database Backup Card -->
                    <div class="mb-4 col-md-12">
                        <div class="card h-100">
                            <x-card-header>Database Backup</x-card-header>
                            <div class="card-body">
                                <p class="text-muted">Backup the database and export it locally for safekeeping.</p>
                                <button class="btn btn-danger" wire:click="backupDatabase">
                                    Backup & Download
                                </button>
                            </div>
                        </div>
                    </div>
                </div>



            </div>
        </div>


        <div x-data x-init=" $wire.on('hideModal', (e) => {
             const modals = document.querySelectorAll('.modal.show');

             // Iterate over each modal and hide it using Bootstrap's modal hide method
             modals.forEach(modal => {
                 const modalInstance = bootstrap.Modal.getInstance(modal);
                 if (modalInstance) {
                     modalInstance.hide();
                 }
             });


             if (e.data !== null ) {
                 let blob = new Blob([e.data], { type: 'text/plain' });
                 let link = document.createElement('a');
                 link.href = window.URL.createObjectURL(blob);
                 link.download = 'maintenance_mode' + Date.now() + '.txt';
                 link.click();
                 window.reload();
             }





         })">

        </div>


        <!-- Modal for Maintenance Mode Confirmation -->

        <div class="modal fade" id="confirmingMaintenanceMode" role="dialog" aria-labelledby="modalTitleId"
            aria-hidden="true" wire:ignore.self data-bs-backdrop='static'>
            <div class="modal-dialog modal-md" role="document">
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
                        <form wire:submit="saveMaintananceMode">
                            <button type="button" class="btn btn-secondary" wire:loading.attr="disabled"
                                data-bs-dismiss="modal">Cancel</button>


                            <button type="submit" class="btn btn-warning" wire:loading.attr="disabled">
                                Yes, Proceed
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>




    </div>

</div>
