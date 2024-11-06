<div>
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">View Seed Beneficiaries</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active">View Seed Beneficiaries</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row" x-data="{
            showCard: false,
            toggleShow() {
                this.showCard = !this.showCard;
        
            }
        
        }" @close-form="showCard = false">

            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title">Seed Beneficiaries</h5>
                        @php
                            $routePrefix = \Illuminate\Support\Facades\Route::current()->getPrefix();
                        @endphp
                        <a class="btn btn-primary btn-sm" href="{{ $routePrefix }}/seed-beneficiaries/add">Add Data
                            +</a>
                    </div>
                    <div class="card-header">
                        <!-- Tabs for each crop type -->
                        <ul class="nav nav-tabs" id="seedBeneficiaryTabs" role="tablist" wire:ignore>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="potato-tab" data-bs-toggle="tab"
                                    data-bs-target="#potato" type="button" role="tab" aria-controls="potato"
                                    aria-selected="true">Potato</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="ofsp-tab" data-bs-toggle="tab" data-bs-target="#ofsp"
                                    type="button" role="tab" aria-controls="ofsp"
                                    aria-selected="false">OFSP</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="cassava-tab" data-bs-toggle="tab" data-bs-target="#cassava"
                                    type="button" role="tab" aria-controls="cassava"
                                    aria-selected="false">Cassava</button>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <!-- Tab Content for Livewire tables -->
                        <div class="tab-content mt-3" id="seedBeneficiaryTabsContent">
                            <!-- Potato Table -->
                            <div class="tab-pane fade show active" id="potato" role="tabpanel"
                                aria-labelledby="potato-tab">
                                <livewire:tables.seed-beneficiaries-table :crop="'Potato'" />
                            </div>

                            <!-- OFSP Table -->
                            <div class="tab-pane fade" id="ofsp" role="tabpanel" aria-labelledby="ofsp-tab">
                                <livewire:tables.seed-beneficiaries-table :crop="'OFSP'" />
                            </div>

                            <!-- Cassava Table -->
                            <div class="tab-pane fade" id="cassava" role="tabpanel" aria-labelledby="cassava-tab">
                                <livewire:tables.seed-beneficiaries-table :crop="'Cassava'" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>



        {{-- <div x-data x-init="$wire.on('showModal', (e) => {

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
        })
        ">


            <x-modal id="view-indicator-modal" title="edit">
                <form>
                    <div class="mb-3">

                        <x-text-input placeholder="Name of indicator..." />
                    </div>

                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>

                    </div>
                </form>
            </x-modal>

        </div> --}}




    </div>

</div>
