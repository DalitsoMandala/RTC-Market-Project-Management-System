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

        <x-alerts />
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
                        <a class="btn btn-warning btn-sm" href="{{ $routePrefix }}/seed-beneficiaries/add">Add Data
                            +</a>
                    </div>
                    <div class="card-header text-muted">
                        <strong>Column Information:</strong>
                        <ul class="mb-0">
                            <li><strong>Sex:</strong> 1 = Male, 2 = Female</li>
                            <li><strong>Marital Status:</strong> 1 = Married, 2 = Single, 3 = Divorced, 4 = Widow/er
                            </li>
                            <li><strong>HH Head:</strong> 1 = MHH, 2 = FHH, 3 = CHH</li>

                        </ul>
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
                                    type="button" role="tab" aria-controls="ofsp" aria-selected="false">OFSP</button>
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
                                aria-labelledby="potato-tab" wire:ignore.self>
                                <livewire:tables.seed-beneficiary-table :crop="'Potato'" />
                            </div>

                            <!-- OFSP Table -->
                            <div class="tab-pane fade" id="ofsp" role="tabpanel" aria-labelledby="ofsp-tab"
                                wire:ignore.self>
                                <livewire:tables.seed-beneficiary-table :crop="'OFSP'" />
                            </div>

                            <!-- Cassava Table -->
                            <div class="tab-pane fade" id="cassava" role="tabpanel" aria-labelledby="cassava-tab"
                                wire:ignore.self>
                                <livewire:tables.seed-beneficiary-table :crop="'Cassava'" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>



        <div x-data x-init="$wire.on('edit-showModal', (e) => {

            const myModal = new bootstrap.Modal(document.getElementById(e.name), {})
            $wire.setData(e.id);
            myModal.show();
        })

        $wire.on('deleteRecord', (e) => {
            const myModal = new bootstrap.Modal(document.getElementById(e.name), {})
            $wire.setData(e.id);
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
            <x-modal id="delete-detail-modal" title="Delete Record">
                <x-alerts />
                <h4 class="text-center h4">Please confirm whether you would like to delete this record?

                </h4>


                <form wire:submit='deleteDetail'>



                    <div class="d-flex border-top-0 justify-content-center mt-5" x-data>
                        <button type="button" wire:loading.attr="disabled" class="btn btn-secondary me-2"
                            data-bs-dismiss="modal">No, cancel</button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="deleteDetail"
                            class="btn btn-danger" @click=" window.scrollTo({
                                top: 0,
                                behavior: 'smooth'
                            })">Yes,
                            I'm sure</button>
                    </div>
                </form>
            </x-modal>


            <x-modal size="modal-lg" id="view-detail-modal" title="Edit Details">
                <form wire:submit='saveChanges'>
                    <div class="row">

                        <!-- Crop Radio Buttons -->
                        <div class="mb-3">
                            <label class="form-label">Crop</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('crop') is-invalid @enderror" type="radio"
                                        wire:model="crop" id="crop_ofsp" value="OFSP">
                                    <label class="form-check-label text-uppercase" for="crop_ofsp">OFSP</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('crop') is-invalid @enderror" type="radio"
                                        wire:model="crop" id="crop_potato" value="Potato">
                                    <label class="form-check-label" for="crop_potato">Potato</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('crop') is-invalid @enderror" type="radio"
                                        wire:model="crop" id="crop_cassava" value="Cassava">
                                    <label class="form-check-label" for="crop_cassava">Cassava</label>
                                </div>
                            </div>
                            @error('crop')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- District -->
                        <div class="mb-3">
                            <label for="district" class="form-label">District</label>
                            <select class="form-select @error('district') is-invalid @enderror" wire:model="district">
                                @include('layouts.district-options')
                            </select>
                            @error('district')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- EPA -->
                        <div class="mb-3">
                            <label class="form-label">EPA</label>
                            <input type="text" class="form-control @error('epa') is-invalid @enderror" wire:model="epa">
                            @error('epa')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Section -->
                        <div class="mb-3">
                            <label class="form-label">Section</label>
                            <input type="text" class="form-control @error('section') is-invalid @enderror"
                                wire:model="section">
                            @error('section')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Name of AEDO -->
                        <div class="mb-3">
                            <label class="form-label">Name of AEDO</label>
                            <input type="text" class="form-control @error('name_of_aedo') is-invalid @enderror"
                                wire:model="name_of_aedo">
                            @error('name_of_aedo')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- AEDO Phone Number -->
                        <div class="mb-3">
                            <label class="form-label">AEDO Phone Number</label>
                            <input type="text" class="form-control @error('aedo_phone_number') is-invalid @enderror"
                                wire:model="aedo_phone_number">
                            @error('aedo_phone_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Date -->
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control @error('date') is-invalid @enderror"
                                wire:model="date">
                            @error('date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Name of Recipient -->
                        <div class="mb-3">
                            <label class="form-label">Name of Recipient</label>
                            <input type="text" class="form-control @error('name_of_recipient') is-invalid @enderror"
                                wire:model="name_of_recipient">
                            @error('name_of_recipient')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Village -->
                        <div class="mb-3">
                            <label class="form-label">Village</label>
                            <input type="text" class="form-control @error('village') is-invalid @enderror"
                                wire:model="village">
                            @error('village')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Sex -->
                        <div class="mb-3">
                            <label class="form-label">Sex</label>
                            <select class="form-select @error('sex') is-invalid @enderror" wire:model="sex">
                                <option value="">Select Sex</option>
                                <option value="1">Male</option>
                                <option value="2">Female</option>
                            </select>
                            @error('sex')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <div class="form-text text-muted">1 = Male, 2 = Female</div>
                        </div>

                        <!-- Age -->
                        <div class="mb-3">
                            <label class="form-label">Age</label>
                            <input type="number" class="form-control @error('age') is-invalid @enderror"
                                wire:model="age" min="1">
                            @error('age')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Marital Status -->
                        <div class="mb-3">
                            <label class="form-label">Marital Status</label>
                            <select class="form-select @error('marital_status') is-invalid @enderror"
                                wire:model="marital_status">
                                <option value="">Select Status</option>
                                <option value="1">Married</option>
                                <option value="2">Single</option>
                                <option value="3">Divorced</option>
                                <option value="4">Widow/er</option>
                            </select>
                            @error('marital_status')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <div class="form-text text-muted">1 = Married, 2 = Single, 3 = Divorced, 4 =
                                Widow/er</div>
                        </div>

                        <!-- Household Head -->
                        <div class="mb-3">
                            <label class="form-label">Household Head</label>
                            <select class="form-select @error('hh_head') is-invalid @enderror" wire:model="hh_head">
                                <option value="">Select HH Head</option>
                                <option value="1">MHH</option>
                                <option value="2">FHH</option>
                                <option value="3">CHH</option>
                            </select>
                            @error('hh_head')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <div class="form-text text-muted">1 = MHH, 2 = FHH, 3 = CHH</div>
                        </div>

                        <!-- Household Size -->
                        <div class="mb-3">
                            <label class="form-label">Household Size</label>
                            <input type="number" class="form-control @error('household_size') is-invalid @enderror"
                                wire:model="household_size" min="1">
                            @error('household_size')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Children Under 5 in HH -->
                        <div class="mb-3">
                            <label class="form-label">Children Under 5 in HH</label>
                            <input type="number" class="form-control @error('children_under_5') is-invalid @enderror"
                                wire:model="children_under_5" min="0">
                            @error('children_under_5')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Variety Received -->
                        <div class="mb-3">
                            <label class="form-label">Variety Received</label>
                            <input type="text" class="form-control @error('variety_received') is-invalid @enderror"
                                wire:model="variety_received">
                            @error('variety_received')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Bundles Received -->
                        <div class="mb-3">
                            <label class="form-label">Bundles Received</label>
                            <input type="number" class="form-control @error('bundles_received') is-invalid @enderror"
                                wire:model="bundles_received" min="1">
                            @error('bundles_received')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Phone / National ID -->
                        <div class="mb-3">
                            <label class="form-label">Phone / National ID</label>
                            <input type="text" class="form-control @error('phone_or_national_id') is-invalid @enderror"
                                wire:model="phone_or_national_id">
                            @error('phone_or_national_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>




                    </div>

                    <div class="modal-footer border-top-0" x-data>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-warning" @click=" window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        })">Save
                            changes</button>

                    </div>
                </form>
            </x-modal>

        </div>




    </div>

</div>