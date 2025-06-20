<div>
    @section('title')
        View Seed Beneficiaries Data
    @endsection
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">View Data</h4>

                    <div class="page-title-right" wire:ignore>
                        @php
                            $routePrefix = \Illuminate\Support\Facades\Route::current()->getPrefix();
                        @endphp
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ $routePrefix }}/forms">Forms</a></li>
                            <li class="breadcrumb-item active">View Data</li>
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
                <ul class=" nav nav-tabs" id="seedBeneficiaryTabs" role="tablist" wire:ignore>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="potato-tab" data-bs-toggle="tab" data-bs-target="#potato"
                            type="button" role="tab" aria-controls="potato" aria-selected="true">Potato
                            Beneficiaries</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="ofsp-tab" data-bs-toggle="tab" data-bs-target="#ofsp"
                            type="button" role="tab" aria-controls="ofsp" aria-selected="false">OFSP
                            Beneficiaries</button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="cassava-tab" data-bs-toggle="tab" data-bs-target="#cassava"
                            type="button" role="tab" aria-controls="cassava" aria-selected="false">Cassava
                            Beneficiaries</button>
                    </li>

                </ul>
                <div class="card">


                    <div class="card-body">


                        <!-- Tab Content for Livewire tables -->
                        <div class="mt-3 tab-content" id="seedBeneficiaryTabsContent">
                            <!-- Potato Table -->
                            <div class="tab-pane fade show active" id="potato" role="tabpanel"
                                aria-labelledby="potato-tab" wire:ignore.self>
                                <livewire:tables.seed-beneficiary-table :crop="'Potato'" />
                            </div>

                            <!-- OFSP Table -->
                            <div class="tab-pane fade" id="ofsp" role="tabpanel" aria-labelledby="ofsp-tab"
                                wire:ignore.self>
                                <livewire:tables.seed-beneficiaries-ofsp-table :crop="'OFSP'" />
                            </div>

                            <!-- Cassava Table -->
                            <div class="tab-pane fade" id="cassava" role="tabpanel" aria-labelledby="cassava-tab"
                                wire:ignore.self>
                                <livewire:tables.seed-beneficiaries-cassava-table :crop="'Cassava'" />
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



                    <div class="mt-5 d-flex border-top-0 justify-content-center" x-data>
                        <button type="button" wire:loading.attr="disabled" class="btn btn-secondary me-2"
                            data-bs-dismiss="modal">No, cancel</button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="deleteDetail"
                            class="btn btn-theme-red"
                            @click=" window.scrollTo({
                                top: 0,
                                behavior: 'smooth'
                            })">Yes,
                            I'm sure</button>
                    </div>
                </form>
            </x-modal>


            <x-modal size="modal-lg" id="view-detail-modal" title="Edit Details">

                <form wire:submit='saveChanges'>
                    <!-- Crop Radio Buttons -->
                    <div class="mb-3" x-data="{
                        selectedCrop: $wire.entangle('crop')
                    }">
                        <label class="form-label">Crop</label>
                        <div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('crop') is-invalid @enderror" type="radio"
                                    wire:model.live="crop" id="crop_potato" value="Potato">
                                <label class="form-check-label" for="crop_potato">Potato</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('crop') is-invalid @enderror" type="radio"
                                    wire:model.live="crop" id="crop_ofsp" value="OFSP">
                                <label class="form-check-label text-uppercase" for="crop_ofsp">OFSP</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('crop') is-invalid @enderror" type="radio"
                                    wire:model.live="crop" id="crop_cassava" value="Cassava">
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
                        <input type="text" class="form-control @error('district') is-invalid @enderror"
                            wire:model="district" />
                        @error('district')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- EPA -->
                    <div class="mb-3">
                        <label class="form-label">EPA</label>
                        <input type="text" class="form-control @error('epa') is-invalid @enderror"
                            wire:model="epa">
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
                    <!-- Age -->
                    <div class="mb-3">
                        <label class="form-label">Group Name</label>
                        <input type="text" class="form-control @error('group_name') is-invalid @enderror"
                            wire:model="group_name">
                        @error('group_name')
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
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                        @error('sex')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

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

                            <option>Single</option>
                            <option>Married</option>
                            <option>Divorced</option>
                            <option>Separated</option>
                            <option>Widowed</option>
                            <option>Polygamy</option>
                        </select>
                        @error('marital_status')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                    </div>

                    <!-- Household Head -->
                    <div class="mb-3">
                        <label class="form-label">Household Head</label>
                        <select class="form-select @error('hh_head') is-invalid @enderror" wire:model="hh_head">
                            <option value="">Select HH Head</option>
                            <option value="MHH">MHH</option>
                            <option value="FHH">FHH</option>
                            <option value="CHH">CHH</option>
                        </select>
                        @error('hh_head')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

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



                    <div class="varieties ">

                        <div class="">


                            <div class="mb-3 " wire:ignore x-data="{
                            
                                selectedVarieties: $wire.entangle('selectedVarieties'),
                                variety_received: $wire.entangle('variety_received'),
                                varieties: $wire.entangle('varieties'),
                            }" x-init="() => {
                            
                                $('#select-crop').select2({
                                    width: '100%',
                                    theme: 'bootstrap-5',
                                    containerCssClass: 'select2--small',
                                    dropdownCssClass: 'select2--small',
                                });
                            
                                $wire.on('get-varieties', (e) => {
                                    const selectElement = $('#select-crop');
                                    const arrayOfObjects = e.data;
                                    const editVarieties = e.variety_received || [];
                                    selectElement.empty();
                            
                            
                            
                                    arrayOfObjects.forEach(data => {
                                        let name = data.name;
                            
                                        let isSelected = editVarieties.includes(name);
                            
                                        if (isSelected) {
                                            selectedVarieties.push(data);
                                        }
                                        let newOption = new Option(name.replace('_', ' '), data.id, isSelected, isSelected);
                            
                                        selectElement.append(newOption);
                                    });
                                    // Refresh Select2 to reflect changes
                                    selectElement.trigger('change');
                            
                                })
                            
                                $('#select-crop').on('select2:select', function(e) {
                                    const data = $(this).select2('data');
                                    selectedVarieties = data.map((item) => {
                                        return {
                                            id: item.id,
                                            name: item.text
                                        }
                                    });
                            
                            
                            
                            
                                });
                            
                                $('#select-crop').on('select2:unselect', function(e) {
                                    const data = $(this).select2('data');
                                    if (data.length === 0) {
                            
                                        selectedVarieties = [];
                            
                                    } else {
                            
                                        selectedVarieties = data.map((item) => {
                                            return {
                                                id: item.id,
                                                name: item.text
                                            }
                                        });
                            
                                    }
                            
                            
                            
                            
                            
                                });
                            
                            }">
                                <label for="" class="form-label">Variety recieved</label>

                                <select x-ref="select" class="form-select" id="select-crop" multiple>
                                    <template x-for="variety in varieties">
                                        <option :value="variety.id" x-text="variety.name"></option>
                                    </template>
                                    {{-- @foreach ($varieties as $variety)
                    <option value="{{ $variety['id'] }}" class="text-capitalize">
                        {{ str_replace('_', ' ', $variety['name']) }}</option>
                @endforeach --}}
                                </select>
                            </div>
                        </div>


                        @error('variety_received')
                            <x-error>{{ $message }}</x-error>
                        @enderror
                    </div>


                    <!-- Bundles Received -->
                    <div class="mb-3" x-data="{
                        seed_type: 'Ton/KG',
                        selectedCrop: $wire.entangle('crop')
                    }" x-init="() => {
                        $wire.on('get-varieties', (e) => {
                            if (selectedCrop === 'Potato') {
                                seed_type = 'Tons/KG';
                            } else {
                                seed_type = 'Bundles'
                            }
                        })
                    
                        $watch('selectedCrop', (value) => {
                    
                            if (value === 'Potato') {
                                seed_type = 'Tons/KG';
                            } else {
                                seed_type = 'Bundles'
                            }
                        })
                    
                    }">
                        <label class="form-label">Amount Of Seed Received <span class="fw-bold"
                                x-text="'('+seed_type+')'"></span></label>
                        <input type="number" class="form-control @error('bundles_received') is-invalid @enderror"
                            wire:model="bundles_received" min="1">
                        @error('bundles_received')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Phone / National ID -->
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" class="form-control @error('phone_number') is-invalid @enderror"
                            wire:model="phone_number">
                        @error('phone_number')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Phone / National ID -->
                    <div class="mb-3">
                        <label class="form-label">National ID</label>
                        <input type="text" class="form-control @error('national_id') is-invalid @enderror"
                            wire:model="national_id">
                        @error('national_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="" class="form-label">Type of Actor</label>
                        <select class="form-select " wire:model="type_of_actor">
                            <option selected value="">Select one</option>
                            <option value="Caregroup">Caregroup</option>
                            <option value="School feeding">School feeding</option>
                            <option value="Commercial">Commercial</option>
                        </select>
                        @error('type_of_actor')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="" class="form-label">Type of Plot</label>
                        <select class="form-select " wire:model="type_of_plot">
                            <option selected>Select one</option>
                            <option value="Mother">Mother</option>
                            <option value="Baby">Baby</option>
                            <option value="Ordinary demonstration">Ordinary demonstration</option>
                        </select>
                        @error('type_of_plot')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>


                    <div class="mb-3">
                        <label for="" class="form-label">Season Type</label>
                        <select class="form-select " wire:model="season_type">
                            <option selected value="">Select one</option>
                            <option value="Rainfed">Rainfed</option>
                            <option value="Winter">Winter</option>

                        </select>
                        @error('season_type')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>


                    <div class="modal-footer border-top-0" x-data>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-warning"
                            @click=" window.scrollTo({
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
