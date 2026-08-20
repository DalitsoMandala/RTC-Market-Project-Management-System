<x-form-component :showAlpineAlerts="true" title="Add Rtc Consumption Data" pageTitle="Add Data" :formTitle="$form_name"
    :openSubmission="$openSubmission" :targetSet="$targetSet" :targetIds="$targetIds" :showTargetForm="true" :formName="$form_name" :hideSubmitButtons="true"
    :bypassTargets="true" :skipDraftScript="true">

    <div x-data="">

        <x-display-id :id="$uniqueId" />

        {{-- Location --}}
        <div class="mb-4">

            <div class="row g-3">

                {{-- District --}}
                <div class="col-md-12">
                    <label class="form-label">
                        District
                    </label>

                    <input type="text" class="form-control @error('district') is-invalid @enderror"
                        wire:model="district">

                    @error('district')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>


                {{-- EPA --}}
                <div class="col-md-4">
                    <label class="form-label">
                        EPA
                    </label>

                    <input type="text" class="form-control @error('epa') is-invalid @enderror" wire:model="epa">

                    @error('epa')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>


                {{-- Section --}}
                <div class="col-md-4">
                    <label class="form-label">
                        Section
                    </label>

                    <input type="text" class="form-control @error('section') is-invalid @enderror"
                        wire:model="section">

                    @error('section')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>


                {{-- Enterprise --}}
                <div class="col-md-4">
                    <label class="form-label">
                        Enterprise / Crop
                    </label>

                    <select class="form-select @error('enterprise') is-invalid @enderror" wire:model="enterprise">
                        <option value="">Select one</option>

                        @foreach (\App\Helpers\CoreFunctions::getCropsWithNull() as $enterprise)
                            @if ($enterprise != null)
                                <option value="{{ $enterprise }}">
                                    {{ $enterprise }}
                                </option>
                            @endif
                        @endforeach
                    </select>

                    @error('enterprise')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>


                {{-- Group Name --}}
                <div class="col-md-6">
                    <label class="form-label">
                        Group Name
                    </label>

                    <input type="text" class="form-control @error('group_name') is-invalid @enderror"
                        wire:model="group_name">

                    @error('group_name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>


                {{-- Type of Farming --}}
                <div class="col-md-6">
                    <label class="form-label">
                        Type of Farming
                    </label>

                    <select class="form-select @error('type_of_farming') is-invalid @enderror"
                        wire:model="type_of_farming">
                        <option value="">Select one</option>
                        <option value="Table Potato">Table Potato</option>
                        <option value="Seed">Seed</option>
                    </select>

                    @error('type_of_farming')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>


                {{-- Season --}}
                <div class="col-md-12">
                    <label class="form-label">
                        Season
                    </label>

                    <select class="form-select @error('season') is-invalid @enderror" wire:model="season">
                        <option value="">Select one</option>
                        <option value="Rainfed">Rainfed</option>
                        <option value="Winter">Winter</option>
                    </select>

                    @error('season')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

            </div>

        </div>


        <hr class="my-4">


        {{-- Group Chair --}}
        <div class="mb-4">

            <div class="row g-3">

                {{-- Chairperson Name --}}
                <div class="col-md-6">
                    <label class="form-label">
                        Chairperson Name
                    </label>

                    <input type="text" class="form-control @error('group_chair_name') is-invalid @enderror"
                        wire:model="group_chair_name">

                    @error('group_chair_name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>


                {{-- Chairperson Contact --}}
                <div class="col-md-6">
                    <label class="form-label">
                        Chairperson Contact
                    </label>

                    <input type="text" class="form-control @error('group_chair_contact') is-invalid @enderror"
                        wire:model="group_chair_contact">

                    @error('group_chair_contact')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

            </div>

        </div>


        <hr class="my-4">


        {{-- Farmer --}}
        <div class="mb-4">

            <div class="row g-3">

                {{-- Farmer Name --}}
                <div class="col-md-6">
                    <label class="form-label">
                        Farmer Name
                    </label>

                    <input type="text" class="form-control @error('farmer_name') is-invalid @enderror"
                        wire:model="farmer_name">

                    @error('farmer_name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>


                {{-- Farmer ID / Phone --}}
                <div class="col-md-6">
                    <label class="form-label">
                        Farmer ID / Phone
                    </label>

                    <input type="text" class="form-control @error('farmer_id_phone') is-invalid @enderror"
                        wire:model="farmer_id_phone">

                    @error('farmer_id_phone')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>


                {{-- Sex --}}
                <div class="col-md-4">
                    <label class="form-label">
                        Sex
                    </label>

                    <select class="form-select @error('sex') is-invalid @enderror" wire:model="sex">
                        <option value="">Select sex</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>

                    @error('sex')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>


                {{-- Age --}}
                <div class="col-md-4">
                    <label class="form-label">
                        Age
                    </label>

                    <input type="number" min="0" class="form-control @error('age') is-invalid @enderror"
                        wire:model="age">

                    @error('age')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

            </div>

        </div>


        <hr class="my-4">


        {{-- Production --}}
        <div class="mb-4">

            <div class="row g-3">

                {{-- Area Grown --}}
                <div class="col-md-4">
                    <label class="form-label">
                        Area Grown (Acres)
                    </label>

                    <input type="number" step="0.01" min="0"
                        class="form-control @error('area_grown_acre') is-invalid @enderror"
                        wire:model="area_grown_acre">

                    @error('area_grown_acre')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>


                {{-- Variety --}}
                <div class="col-md-4">
                    <label class="form-label">
                        Variety
                    </label>

                    <select class="form-select @error('variety') is-invalid @enderror" wire:model="variety">
                        <option value="">Select one</option>

                        @foreach (\App\Models\CropVariety::all() as $Variety)
                            <option value="{{ strtolower($Variety->name) }}">
                                {{ ucfirst($Variety->name) }}
                            </option>
                        @endforeach
                    </select>

                    @error('variety')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>


                {{-- Harvesting Units --}}
                <div class="col-md-4">
                    <label class="form-label">
                        Harvesting Units
                    </label>

                    <input type="text" class="form-control @error('harvesting_units') is-invalid @enderror"
                        wire:model="harvesting_units">

                    @error('harvesting_units')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>


                {{-- Unit Weight --}}
                <div class="col-md-4">
                    <label class="form-label">
                        Unit Weight (kg)
                    </label>

                    <input type="number" step="0.01" min="0"
                        class="form-control @error('unit_weight_kg') is-invalid @enderror"
                        wire:model="unit_weight_kg">

                    @error('unit_weight_kg')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>


                {{-- Quantity --}}
                <div class="col-md-4">
                    <label class="form-label">
                        Quantity
                    </label>

                    <input type="number" step="0.01" min="0"
                        class="form-control @error('qty') is-invalid @enderror" wire:model="qty">

                    @error('qty')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

            </div>

        </div>


        <hr class="my-4">


        {{-- Marketing --}}
        <div>

            <div class="row g-3">

                {{-- Selling Price --}}
                <div class="col-md-6">
                    <label class="form-label">
                        Selling Price
                    </label>

                    <div class="input-group">
                        <span class="input-group-text">
                            MWK
                        </span>

                        <input type="number" step="0.01" min="0"
                            class="form-control @error('selling_price') is-invalid @enderror"
                            wire:model="selling_price">
                    </div>

                    @error('selling_price')
                        <div class="mt-1 text-danger small">
                            {{ $message }}
                        </div>
                    @enderror
                </div>


                {{-- Main Buyer --}}
                <div class="col-md-6">
                    <label class="form-label">
                        Main Buyer
                    </label>

                    <input type="text" class="form-control @error('main_buyer') is-invalid @enderror"
                        wire:model="main_buyer">

                    @error('main_buyer')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

            </div>

        </div>

    </div>

</x-form-component>
