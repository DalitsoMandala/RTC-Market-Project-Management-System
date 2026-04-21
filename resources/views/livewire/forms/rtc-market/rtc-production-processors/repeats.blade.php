<div x-data="{
    has_rtc_market_contract: $wire.entangle('has_rtc_market_contract'),
    inputOne: $wire.entangle('inputOne')
}" x-init="$watch('has_rtc_market_contract', (v) => {
    if (v != 1) {
        $wire.resetValues('inputOne');
    }
});" x-show='has_rtc_market_contract == 1'>

    <div class="my-3 border-left alert alert-warning" id="section-f" role="alert">
        <i class="bx bx-info-circle me-2"></i>
        <strong>DO YOU HAVE ANY RTC MARKET CONTRACTUAL AGREEMENT? – SPECIFY PARTNER</strong>
    </div>

    @foreach ($inputOne as $index => $input)
        <div class="mb-4 border card" wire:key="rtc-card-{{ $index }}">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-body">#{{ $loop->iteration }}</h5>
                @if (count($inputOne) > 1)
                    <button type="button" wire:loading.attr="disabled" class="btn btn-outline-danger btn-sm"
                        wire:click='removeInputOne({{ $index }})'>
                        <i class="bx bx-trash"></i> Remove
                    </button>
                @endif
            </div>

            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">DATE RECORDED</label>
                            <input type="date"
                                class="form-control @error('inputOne.' . $index . '.conc_date_recorded') is-invalid @enderror"
                                wire:model="inputOne.{{ $index }}.conc_date_recorded">
                            @error('inputOne.' . $index . '.conc_date_recorded') <x-error>{{ $message }}</x-error> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">PARTNER NAME</label>
                            <input type="text" placeholder="Enter partner name"
                                class="form-control @error('inputOne.' . $index . '.conc_partner_name') is-invalid @enderror"
                                wire:model="inputOne.{{ $index }}.conc_partner_name">
                            @error('inputOne.' . $index . '.conc_partner_name') <x-error>{{ $message }}</x-error> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">COUNTRY</label>
                            <select class="form-select @error('inputOne.' . $index . '.conc_country') is-invalid @enderror"
                                wire:model="inputOne.{{ $index }}.conc_country">
                                <option value="">Select one</option>
                                @include('layouts.countries-options')
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">DATE OF MAXIMUM SALE</label>
                            <input type="date"
                                class="form-control @error('inputOne.' . $index . '.conc_date_of_maximum_sale') is-invalid @enderror"
                                wire:model="inputOne.{{ $index }}.conc_date_of_maximum_sale">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">PRODUCT TYPE</label>
                            <select class="form-select @error('inputOne.' . $index . '.conc_product_type') is-invalid @enderror"
                                wire:model="inputOne.{{ $index }}.conc_product_type">
                                <option value="">Select one</option>
                                <option>Seed</option>
                                <option>Ware</option>
                                <option>Value added products</option>
                            </select>
                            @error('inputOne.' . $index . '.conc_product_type') <x-error>{{ $message }}</x-error> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">VOLUME SOLD (METRIC TONNES)</label>
                            <input type="number" step="any" min="0" placeholder="0.00"
                                class="form-control @error('inputOne.' . $index . '.conc_volume_sold_previous_period') is-invalid @enderror"
                                wire:model="inputOne.{{ $index }}.conc_volume_sold_previous_period">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">FINANCIAL VALUE (MALAWI KWACHA)</label>
                            <input type="number" step="any" min="0" placeholder="MK 0.00"
                                class="form-control @error('inputOne.' . $index . '.conc_financial_value_of_sales') is-invalid @enderror"
                                wire:model="inputOne.{{ $index }}.conc_financial_value_of_sales">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <div class="py-3 text-center">
        <button type="button" wire:loading.attr="disabled" class="px-4 btn btn-light btn-sm" wire:click="addInputOne">
            <i class="bx bx-plus-circle"></i> Add Another Partner Record
        </button>
    </div>

</div>

<div x-data="{
    sells_to_domestic_markets: $wire.entangle('sells_to_domestic_markets'),
    inputTwo: $wire.entangle('inputTwo')
}" x-init="$watch('sells_to_domestic_markets', (v) => {
    if (v != 1) {
        $wire.resetValues('inputTwo');
    }
});" x-show='sells_to_domestic_markets == 1'>

    <div class="my-3 alert alert-warning" id="section-g" role="alert">
        <i class="bx bx-store-alt me-2"></i>
        <strong>DO YOU SELL YOUR RTC PRODUCTS TO DOMESTIC MARKETS? – SPECIFY PARTNER</strong>
    </div>

    @foreach ($inputTwo as $index => $input)
        <div class="mb-4 border card" wire:key="domestic-market-{{ $index }}">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-body"> #{{ $loop->iteration }}</h5>
                @if (count($inputTwo) > 1)
                    <button type="button" class="btn btn-outline-danger btn-sm" wire:loading.attr="disabled"
                        wire:click='removeInputTwo({{ $index }})'>
                        <i class="bx bx-trash"></i> Remove
                    </button>
                @endif
            </div>

            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">CROP TYPE</label>
                            <select class="form-select @error('inputTwo.' . $index . '.dom_crop_type') is-invalid @enderror"
                                wire:model="inputTwo.{{ $index }}.dom_crop_type">
                                <option value="">Select one</option>
                                <option>Cassava</option>
                                <option>Potato</option>
                                <option>Sweet potato</option>
                            </select>
                            @error('inputTwo.' . $index . '.dom_crop_type') <x-error>{{ $message }}</x-error> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">MARKET NAME</label>
                            <input type="text" placeholder="e.g. Limbe Market"
                                class="form-control @error('inputTwo.' . $index . '.dom_market_name') is-invalid @enderror"
                                wire:model="inputTwo.{{ $index }}.dom_market_name">
                            @error('inputTwo.' . $index . '.dom_market_name') <x-error>{{ $message }}</x-error> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">DISTRICT</label>
                            <x-district-input wire:model="inputTwo.{{ $index }}.dom_district" />
                            @error('inputTwo.' . $index . '.dom_district')
                                <x-error>{{ $message }}</x-error>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">PRODUCT TYPE</label>
                            <select class="form-select @error('inputTwo.' . $index . '.dom_product_type') is-invalid @enderror"
                                wire:model="inputTwo.{{ $index }}.dom_product_type">
                                <option value="">Select one</option>
                                <option>Seed</option>
                                <option>Ware</option>
                                <option>Value added products</option>
                            </select>
                            @error('inputTwo.' . $index . '.dom_product_type') <x-error>{{ $message }}</x-error> @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">DATE RECORDED</label>
                            <input type="date"
                                class="form-control @error('inputTwo.' . $index . '.dom_date_recorded') is-invalid @enderror"
                                wire:model="inputTwo.{{ $index }}.dom_date_recorded">
                            @error('inputTwo.' . $index . '.dom_date_recorded') <x-error>{{ $message }}</x-error> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">DATE OF MAXIMUM SALE</label>
                            <input type="date"
                                class="form-control @error('inputTwo.' . $index . '.dom_date_of_maximum_sale') is-invalid @enderror"
                                wire:model="inputTwo.{{ $index }}.dom_date_of_maximum_sale">
                            @error('inputTwo.' . $index . '.dom_date_of_maximum_sale') <x-error>{{ $message }}</x-error> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">VOLUME SOLD (MT)</label>
                            <input type="number" step="any" min="0" placeholder="0.00"
                                class="form-control @error('inputTwo.' . $index . '.dom_volume_sold_previous_period') is-invalid @enderror"
                                wire:model="inputTwo.{{ $index }}.dom_volume_sold_previous_period">
                            @error('inputTwo.' . $index . '.dom_volume_sold_previous_period') <x-error>{{ $message }}</x-error> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">FINANCIAL VALUE (MWK)</label>
                            <input type="number" step="any" min="0" placeholder="MK 0.00"
                                class="form-control @error('inputTwo.' . $index . '.dom_financial_value_of_sales') is-invalid @enderror"
                                wire:model="inputTwo.{{ $index }}.dom_financial_value_of_sales">
                            @error('inputTwo.' . $index . '.dom_financial_value_of_sales') <x-error>{{ $message }}</x-error> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <div class="pb-4 text-center">
        <button type="button" wire:loading.attr="disabled" class="px-4 btn btn-light btn-sm" @click='$wire.addInputTwo()'>
            <i class="bx bx-plus-circle"></i> Add Domestic Market Record
        </button>
    </div>

</div>



<div x-data="{
    sells_to_international_markets: $wire.entangle('sells_to_international_markets'),
    inputThree: $wire.entangle('inputThree')
}" x-init="$watch('sells_to_international_markets', (v) => {
    if (v != 1) {
        $wire.resetValues('inputThree');
    }
});" x-show='sells_to_international_markets == 1'>

    <div class="my-3 alert alert-warning" id="section-h" role="alert">
        <i class="bx bx-globe me-2"></i>
        <strong>DO YOU SELL YOUR RTC PRODUCTS TO INTERNATIONAL MARKETS? – SPECIFY PARTNER</strong>
    </div>

    @foreach ($inputThree as $index => $input)
        <div class="mb-4 border card" wire:key="inter-market-{{ $index }}">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-body">#{{ $loop->iteration }}</h5>
                @if (count($inputThree) > 1)
                    <button type="button" class="btn btn-outline-danger btn-sm"
                    wire:loading.attr="disabled"
                        wire:click='removeInputThree({{ $index }})'>
                        <i class="bx bx-trash"></i> Remove
                    </button>
                @endif
            </div>

            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">CROP TYPE</label>
                            <select class="form-select @error('inputThree.' . $index . '.inter_crop_type') is-invalid @enderror"
                                wire:model="inputThree.{{ $index }}.inter_crop_type">
                                <option value="">Select one</option>
                                <option>Cassava</option>
                                <option>Potato</option>
                                <option>Sweet potato</option>
                            </select>
                            @error('inputThree.' . $index . '.inter_crop_type') <x-error>{{ $message }}</x-error> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">MARKET/PARTNER NAME</label>
                            <input type="text" placeholder="Enter international partner"
                                class="form-control @error('inputThree.' . $index . '.inter_market_name') is-invalid @enderror"
                                wire:model="inputThree.{{ $index }}.inter_market_name">
                            @error('inputThree.' . $index . '.inter_market_name') <x-error>{{ $message }}</x-error> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">COUNTRY</label>
                            <select class="form-select @error('inputThree.' . $index . '.inter_country') is-invalid @enderror"
                                wire:model="inputThree.{{ $index }}.inter_country">
                                <option value="">Select destination country</option>
                                @include('layouts.countries-options')
                            </select>
                            @error('inputThree.' . $index . '.inter_country') <x-error>{{ $message }}</x-error> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">PRODUCT TYPE</label>
                            <select class="form-select @error('inputThree.' . $index . '.inter_product_type') is-invalid @enderror"
                                wire:model="inputThree.{{ $index }}.inter_product_type">
                                <option value="">Select one</option>
                                <option>Seed</option>
                                <option>Ware</option>
                                <option>Value added products</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">DATE RECORDED</label>
                            <input type="date"
                                class="form-control @error('inputThree.' . $index . '.inter_date_recorded') is-invalid @enderror"
                                wire:model="inputThree.{{ $index }}.inter_date_recorded">
                            @error('inputThree.' . $index . '.inter_date_recorded') <x-error>{{ $message }}</x-error> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">DATE OF MAXIMUM SALE</label>
                            <input type="date"
                                class="form-control @error('inputThree.' . $index . '.inter_date_of_maximum_sale') is-invalid @enderror"
                                wire:model="inputThree.{{ $index }}.inter_date_of_maximum_sale">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">VOLUME SOLD (MT)</label>
                            <input type="number" step="any" min="0" placeholder="0.00"
                                class="form-control @error('inputThree.' . $index . '.inter_volume_sold_previous_period') is-invalid @enderror"
                                wire:model="inputThree.{{ $index }}.inter_volume_sold_previous_period">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">FINANCIAL VALUE (MWK)</label>
                            <input type="number" step="any" min="0" placeholder="MK 0.00"
                                class="form-control @error('inputThree.' . $index . '.inter_financial_value_of_sales') is-invalid @enderror"
                                wire:model="inputThree.{{ $index }}.inter_financial_value_of_sales">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <div class="pb-4 text-center">
        <button type="button" wire:loading.attr="disabled" class="px-4 btn btn-light btn-sm" @click='$wire.addInputThree()'>
            <i class="bx bx-plus-circle"></i> Add International Market Record
        </button>
    </div>

</div>
