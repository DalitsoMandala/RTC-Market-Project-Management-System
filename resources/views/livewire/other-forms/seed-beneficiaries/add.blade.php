<div>
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Add Data</h4>

                    <div class="page-title-right" wire:ignore>
                        @php
                            use Ramsey\Uuid\Uuid;
                            $uuid = Uuid::uuid4()->toString();
                            $currentUrl = url()->current();
                            $replaceUrl = str_replace('add', 'upload', $currentUrl) . "/{$uuid}";

                        @endphp
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active">Add Data</li>

                            <li class="breadcrumb-item">
                                <a href="{{ $replaceUrl }}">Upload Data</a>
                            </li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">


                <h3 class="mb-5 text-center text-warning">SEED DISTRIBUTION REGISTER</h3>
                <x-alerts />





                @if (!$targetSet)
                    <livewire:forms.rtc-market.set-targets-form :submissionTargetIds="$targetIds" />
                @endif

                @if ($openSubmission === false)
                    <div class="alert alert-warning" role="alert">
                        You can not submit a form right now
                        because submissions are closed for the moment!
                    </div>
                @endif

                <div class="card @if ($openSubmission === false) opacity-25 pe-none @endif">



                    <div class="card-body">
                        <form wire:submit.prevent="save">
                            <!-- Crop Radio Buttons -->
                            <div class="mb-3">
                                <label class="form-label">Crop</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input @error('crop') is-invalid @enderror"
                                            type="radio" wire:model="crop" id="crop_ofsp" value="OFSP">
                                        <label class="form-check-label text-uppercase" for="crop_ofsp">OFSP</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input @error('crop') is-invalid @enderror"
                                            type="radio" wire:model="crop" id="crop_potato" value="Potato">
                                        <label class="form-check-label" for="crop_potato">Potato</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input @error('crop') is-invalid @enderror"
                                            type="radio" wire:model="crop" id="crop_cassava" value="Cassava">
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
                                <select class="form-select @error('district') is-invalid @enderror"
                                    wire:model="district">
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
                                <input type="text"
                                    class="form-control @error('aedo_phone_number') is-invalid @enderror"
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
                                <div class="form-text text-muted">1 = Married, 2 = Single, 3 = Divorced, 4 =
                                    Widow/er</div>
                            </div>

                            <!-- Household Head -->
                            <div class="mb-3">
                                <label class="form-label">Household Head</label>
                                <select class="form-select @error('hh_head') is-invalid @enderror"
                                    wire:model="hh_head">
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
                                <input type="number"
                                    class="form-control @error('household_size') is-invalid @enderror"
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
                                <button class="px-5 btn btn-warning"
                                    @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
                                    type="submit">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@script
    <script>
        $wire.on('complete-submission', () => {
            $wire.send();
        });
    </script>
@endscript
