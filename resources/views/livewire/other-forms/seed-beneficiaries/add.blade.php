<div>
    <x-alerts />
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h5>Add New Seed Beneficiary</h5>
            <button class="btn btn-primary  btn-sm" wire:click="$dispatch('close-form')">Close Form</button>
        </div>
        <div class="card-header">
            <!-- Tabs for Manual Entry and Batch Upload -->
            <ul class="nav nav-tabs" id="beneficiaryTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="manual-tab" data-bs-toggle="tab" data-bs-target="#manual"
                        type="button" role="tab" aria-controls="manual" aria-selected="true">Add Manually</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="batch-tab" data-bs-toggle="tab" data-bs-target="#batch" type="button"
                        role="tab" aria-controls="batch" aria-selected="false">Upload Batch</button>
                </li>
            </ul>

        </div>
        <div class="card-body">

            <!-- Tab Content -->
            <div class="tab-content mt-3" id="beneficiaryTabsContent">
                <!-- Manual Entry Form -->
                <div class="tab-pane fade show active" id="manual" role="tabpanel" aria-labelledby="manual-tab">
                    <form wire:submit.prevent="save">
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
                            <input type="text"
                                class="form-control @error('name_of_recipient') is-invalid @enderror"
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
                            <div class="form-text text-muted">1 = Married, 2 = Single, 3 = Divorced, 4 = Widow/er</div>
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
                            <input type="number"
                                class="form-control @error('children_under_5') is-invalid @enderror"
                                wire:model="children_under_5" min="0">
                            @error('children_under_5')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Variety Received -->
                        <div class="mb-3">
                            <label class="form-label">Variety Received</label>
                            <input type="text"
                                class="form-control @error('variety_received') is-invalid @enderror"
                                wire:model="variety_received">
                            @error('variety_received')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Bundles Received -->
                        <div class="mb-3">
                            <label class="form-label">Bundles Received</label>
                            <input type="number"
                                class="form-control @error('bundles_received') is-invalid @enderror"
                                wire:model="bundles_received" min="1">
                            @error('bundles_received')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Phone / National ID -->
                        <div class="mb-3">
                            <label class="form-label">Phone / National ID</label>
                            <input type="text"
                                class="form-control @error('phone_or_national_id') is-invalid @enderror"
                                wire:model="phone_or_national_id">
                            @error('phone_or_national_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid col-12 justify-content-center" x-data>
                            <button class="px-5 btn btn-primary"
                                @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
                                type="submit">Submit</button>
                        </div>
                    </form>
                </div>

                <!-- Batch Upload Form -->
                <div class="tab-pane fade" id="batch" role="tabpanel" aria-labelledby="batch-tab">
                    <div class="mb-3">
                        <p class="alert bg-info-subtle text-uppercase">Download the Seed Beneficiaries template &
                            upload
                            your
                            data.</p>
                        <button class="btn btn-soft-primary" wire:click="downloadTemplate">Download
                            Template</button>
                    </div>

                    <form wire:submit.prevent="uploadBatch">
                        <div class="mb-3">
                            <label class="form-label">Upload File</label>
                            <input type="file" class="form-control @error('batchFile') is-invalid @enderror"
                                wire:model="batchFile">
                            @error('batchFile')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Submit Button for Batch Upload -->
                        <div class="d-grid col-12 justify-content-center" x-data>
                            <button class="px-5 btn btn-primary"
                                @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
                                type="submit">Upload</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
