{{-- DO YOU HAVE ANY RTC MARKET CONTRACTUAL AGREEMENT? –SPECIFY PARTNER --}}

<div x-data="{
    has_rtc_market_contract: $wire.entangle('has_rtc_market_contract'),
    inputOne: $wire.entangle('inputOne')
}" x-init="$watch('has_rtc_market_contract', (v) => {

    if (v != 1) {
        $wire.resetValues('inputOne');
    }
});" x-show='has_rtc_market_contract == 1'>
    <div class="alert alert-warning" id="section-f" role="alert">
        <strong>DO YOU HAVE ANY RTC MARKET CONTRACTUAL AGREEMENT? –SPECIFY PARTNER</strong>
    </div>


    @php

        $countOne = 1;
    @endphp
    @foreach ($inputOne as $index => $input)
        <h3>{{ $countOne++ }}</h3>
        <div class="mb-3">
            <label for="conc_date_recorded_{{ $index }}" class="form-label">DATE RECORDED</label>
            <input type="date"
                class="form-control    @error('inputOne.' . $index . '.conc_date_recorded') is-invalid @enderror"
                id="conc_date_recorded_{{ $index }}"
                wire:model="inputOne.{{ $index }}.conc_date_recorded">

            @error('inputOne.' . $index . '.conc_date_recorded')
                <x-error>{{ $message }}</x-error>
            @enderror
        </div>

        <div class="mb-3">
            <label for="conc_partner_name_{{ $index }}" class="form-label">PARTNER NAME</label>
            <input type="text"
                class="form-control  @error('inputOne.' . $index . '.conc_partner_name') is-invalid @enderror"
                id="conc_partner_name_{{ $index }}" wire:model="inputOne.{{ $index }}.conc_partner_name">
            @error('inputOne.' . $index . '.conc_partner_name')
                <x-error>{{ $message }}</x-error>
            @enderror
        </div>

        <div class="mb-3">
            <label for="conc_country_{{ $index }}" class="form-label">COUNTRY</label>
            <input type="text"
                class="form-control  @error('inputOne.' . $index . '.conc_country') is-invalid @enderror"
                id="conc_country_{{ $index }}" wire:model="inputOne.{{ $index }}.conc_country">
            @error('inputOne.' . $index . '.conc_country')
                <x-error>{{ $message }}</x-error>
            @enderror
        </div>

        <div class="mb-3">
            <label for="conc_date_of_maximum_sale_{{ $index }}" class="form-label">DATE OF MAXIMUM
                SALE</label>
            <input type="date"
                class="form-control  @error('inputOne.' . $index . '.conc_date_of_maximum_sale') is-invalid @enderror"
                id="conc_date_of_maximum_sale_{{ $index }}"
                wire:model="inputOne.{{ $index }}.conc_date_of_maximum_sale">
            @error('inputOne.' . $index . '.conc_date_of_maximum_sale')
                <x-error>{{ $message }}</x-error>
            @enderror
        </div>

        <div class="mb-3">
            <label for="conc_product_type_{{ $index }}" class="form-label">PRODUCT TYPE</label>


            <select
                class="form-select form-select-md  @error('inputOne.' . $index . '.conc_product_type') is-invalid @enderror"
                wire:model="inputOne.{{ $index }}.conc_product_type">
                <option value="">Select one</option>
                <option>SEED</option>
                <option>WARE</option>
                <option>VALUE ADDED PRODUCTS</option>
            </select>

            @error('inputOne.' . $index . '.conc_product_type')
                <x-error>{{ $message }}</x-error>
            @enderror
        </div>

        <div class="mb-3">
            <label for="conc_volume_sold_previous_period_{{ $index }}" class="form-label">VOLUME SOLD
                PREVIOUS
                PERIOD (METRIC TONNES)</label>
            <input type="number" min="0" step="any" step="any"
                class="form-control  @error('inputOne.' . $index . '.conc_volume_sold_previous_period') is-invalid @enderror"
                id="conc_volume_sold_previous_period_{{ $index }}"
                wire:model="inputOne.{{ $index }}.conc_volume_sold_previous_period">
            @error('inputOne.' . $index . '.conc_volume_sold_previous_period')
                <x-error>{{ $message }}</x-error>
            @enderror
        </div>

        <div class="mb-3">
            <label for="conc_financial_value_of_sales_{{ $index }}" class="form-label">FINANCIAL VALUE OF
                SALES
                (MALAWI KWACHA)
            </label>
            <input type="number" min="0" step="any" step="any"
                class="form-control  @error('inputOne.' . $index . '.conc_financial_value_of_sales') is-invalid @enderror "
                id="conc_financial_value_of_sales_{{ $index }}"
                wire:model="inputOne.{{ $index }}.conc_financial_value_of_sales">
            @error('inputOne.' . $index . '.conc_financial_value_of_sales')
                <x-error>{{ $message }}</x-error>
            @enderror
        </div>
        <div class="my-2">

            <div class="text-center btn-group" role="group" aria-label="Button group name" x-data>
                @if ($index > 0)
                    <button type="button" class="btn btn-theme-red" wire:click='removeInputOne({{ $index }})'>
                        <i class="bx bx-minus fs-6"></i>
                    </button>
                @endif
                <button type="button" class="btn btn-secondary" @click='$wire.addInputOne()'>
                    <i class="bx bx-plus"></i>
                </button>

            </div>

        </div>
    @endforeach

</div>




<div x-data="{
    sells_to_domestic_markets: $wire.entangle('sells_to_domestic_markets'),
    inputTwo: $wire.entangle('inputTwo')
}" x-init="$watch('sells_to_domestic_markets', (v) => {

    if (v != 1) {
        $wire.resetValues('inputTwo');
    }
});" x-show='sells_to_domestic_markets == 1'>
    {{-- DO YOU SELL YOUR RTC PRODUCTS TO DOMESTIC MARKETS? –SPECIFY PARTNER --}}
    <div class="alert alert-warning" id="section-g" role="alert">
        <strong>DO YOU SELL YOUR RTC PRODUCTS TO DOMESTIC MARKETS? –SPECIFY PARTNER</strong>
    </div>

    @php
        $countTwo = 1;

    @endphp
    @foreach ($inputTwo as $index => $input)
        <h3>{{ $countTwo++ }}</h3>
        <div class="mb-3">
            <label for="dom_date_recorded_{{ $index }}" class="form-label">DATE RECORDED</label>
            <input type="date"
                class="form-control    @error('inputTwo.' . $index . '.dom_date_recorded') is-invalid @enderror"
                id="dom_date_recorded_{{ $index }}"
                wire:model="inputTwo.{{ $index }}.dom_date_recorded">
            @error('inputTwo.' . $index . '.dom_date_recorded')
                <x-error>{{ $message }}</x-error>
            @enderror
        </div>

        <div class="mb-3">
            <label for="dom_crop_type_{{ $index }}" class="form-label">CROP TYPE</label>


            <select
                class="form-select form-select-md  @error('inputTwo.' . $index . '.dom_crop_type') is-invalid @enderror"
                wire:model="inputTwo.{{ $index }}.dom_crop_type">
                <option value="">Select one</option>
                <option>CASSAVA</option>
                <option>POTATO</option>
                <option>SWEET POTATO</option>
            </select>
            @error('inputTwo.' . $index . '.dom_crop_type')
                <x-error>{{ $message }}</x-error>
            @enderror
        </div>

        <div class="mb-3">
            <label for="dom_market_name_{{ $index }}" class="form-label">MARKET NAME</label>
            <input type="text"
                class="form-control  @error('inputTwo.' . $index . '.dom_market_name') is-invalid @enderror"
                id="dom_market_name_{{ $index }}" wire:model="inputTwo.{{ $index }}.dom_market_name">
            @error('inputTwo.' . $index . '.dom_market_name')
                <x-error>{{ $message }}</x-error>
            @enderror
        </div>

        <div class="mb-3">
            <label for="dom_district_{{ $index }}" class="form-label">DISTRICT</label>
            <input type="text"
                class="form-control  @error('inputTwo.' . $index . '.dom_district') is-invalid @enderror"
                id="dom_district_{{ $index }}" wire:model="inputTwo.{{ $index }}.dom_district">
            @error('inputTwo.' . $index . '.dom_district')
                <x-error>{{ $message }}</x-error>
            @enderror
        </div>

        <div class="mb-3">
            <label for="dom_date_of_maximum_sale_{{ $index }}" class="form-label">DATE OF MAXIMUM
                SALE</label>
            <input type="date"
                class="form-control  @error('inputTwo.' . $index . '.dom_date_of_maximum_sale') is-invalid @enderror"
                id="dom_date_of_maximum_sale_{{ $index }}"
                wire:model="inputTwo.{{ $index }}.dom_date_of_maximum_sale">
            @error('inputTwo.' . $index . '.dom_date_of_maximum_sale')
                <x-error>{{ $message }}</x-error>
            @enderror
        </div>

        <div class="mb-3">
            <label for="dom_product_type_{{ $index }}" class="form-label">PRODUCT TYPE</label>


            <select
                class="form-select form-select-md  @error('inputTwo.' . $index . '.dom_product_type') is-invalid @enderror"
                wire:model="inputTwo.{{ $index }}.dom_product_type">

                <option>SEED</option>
                <option>WARE</option>
                <option>VALUE ADDED PRODUCTS</option>
            </select>
            @error('inputTwo.' . $index . '.dom_product_type')
                <x-error>{{ $message }}</x-error>
            @enderror

        </div>

        <div class="mb-3">
            <label for="dom_volume_sold_previous_period_{{ $index }}" class="form-label">VOLUME SOLD
                PREVIOUS
                PERIOD (METRIC TONNES)</label>
            <input type="number" min="0" step="any" step="any"
                class="form-control  @error('inputTwo.' . $index . '.dom_volume_sold_previous_period') is-invalid @enderror"
                id="dom_volume_sold_previous_period_{{ $index }}"
                wire:model="inputTwo.{{ $index }}.dom_volume_sold_previous_period">
            @error('inputTwo.' . $index . '.dom_volume_sold_previous_period')
                <x-error>{{ $message }}</x-error>
            @enderror
        </div>

        <div class="mb-3">
            <label for="dom_financial_value_of_sales_{{ $index }}" class="form-label">FINANCIAL VALUE OF
                SALES</label>
            <input type="number" min="0" step="any" step="any"
                class="form-control  @error('inputTwo.' . $index . '.dom_financial_value_of_sales') is-invalid @enderror"
                id="dom_financial_value_of_sales_{{ $index }}"
                wire:model="inputTwo.{{ $index }}.dom_financial_value_of_sales">
            @error('inputTwo.' . $index . '.dom_financial_value_of_sales')
                <x-error>{{ $message }}</x-error>
            @enderror
        </div>
        <div class="my-2">

            <div class="text-center btn-group" role="group" aria-label="Button group name" x-data>
                @if ($index > 0)
                    <button type="button" class="btn btn-theme-red"
                        wire:click='removeInputTwo({{ $index }})'>
                        <i class="bx bx-minus fs-6"></i>
                    </button>
                @endif
                <button type="button" class="btn btn-secondary" @click='$wire.addInputTwo()'>
                    <i class="bx bx-plus"></i>
                </button>

            </div>

        </div>
    @endforeach
</div>



<div x-data="{
    sells_to_international_markets: $wire.entangle('sells_to_international_markets'),
    inputThree: $wire.entangle('inputThree')
}" x-init="$watch('sells_to_international_markets', (v) => {

    if (v != 1) {
        $wire.resetValues('inputThree');
    }
});" x-show='sells_to_international_markets == 1'>



    {{-- DO YOU SELL YOUR RTC PRODUCTS TO INTERNATIONAL MARKETS? –SPECIFY PARTNER --}}
    <div class="alert alert-warning" id="section-h" role="alert">
        <strong>DO YOU SELL YOUR RTC PRODUCTS TO INTERNATIONAL MARKETS? –SPECIFY PARTNER</strong>
    </div>

    @php

        $countThree = 1;
    @endphp
    @foreach ($inputThree as $index => $input)
        <h3>{{ $countThree++ }}</h3>

        <div class="mb-3">
            <label for="inter_date_recorded_{{ $index }}" class="form-label">DATE RECORDED</label>
            <input type="date"
                class="form-control   @error('inputThree.' . $index . '.inter_date_recorded') is-invalid @enderror"
                id="inter_date_recorded_{{ $index }}"
                wire:model="inputThree.{{ $index }}.inter_date_recorded">
            @error('inputThree.' . $index . '.inter_date_recorded')
                <x-error>{{ $message }}</x-error>
            @enderror
        </div>

        <div class="mb-3">
            <label for="inter_crop_type_{{ $index }}" class="form-label">CROP TYPE</label>


            <select
                class="form-select form-select-md   @error('inputThree.' . $index . '.inter_crop_type') is-invalid @enderror"
                wire:model="inputThree.{{ $index }}.inter_crop_type">

                <option>CASSAVA</option>
                <option>POTATO</option>
                <option>SWEET POTATO</option>
            </select>
            @error('inputThree.' . $index . '.inter_crop_type')
                <x-error>{{ $message }}</x-error>
            @enderror

        </div>

        <div class="mb-3">
            <label for="inter_market_name_{{ $index }}" class="form-label">MARKET NAME</label>
            <input type="text"
                class="form-control   @error('inputThree.' . $index . '.inter_market_name') is-invalid @enderror"
                id="inter_market_name_{{ $index }}"
                wire:model="inputThree.{{ $index }}.inter_market_name">
            @error('inputThree.' . $index . '.inter_market_name')
                <x-error>{{ $message }}</x-error>
            @enderror
        </div>

        <div class="mb-3">
            <label for="inter_country_{{ $index }}" class="form-label">COUNTRY</label>
            <input type="text"
                class="form-control   @error('inputThree.' . $index . '.inter_country') is-invalid @enderror"
                id="inter_country_{{ $index }}" wire:model="inputThree.{{ $index }}.inter_country">
            @error('inputThree.' . $index . '.inter_country')
                <x-error>{{ $message }}</x-error>
            @enderror
        </div>

        <div class="mb-3">
            <label for="inter_date_of_maximum_sale_{{ $index }}" class="form-label">DATE OF MAXIMUM
                SALE</label>
            <input type="date"
                class="form-control   @error('inputThree.' . $index . '.inter_date_of_maximum_sale') is-invalid @enderror"
                id="inter_date_of_maximum_sale_{{ $index }}"
                wire:model="inputThree.{{ $index }}.inter_date_of_maximum_sale">
            @error('inputThree.' . $index . '.inter_date_of_maximum_sale')
                <x-error>{{ $message }}</x-error>
            @enderror
        </div>

        <div class="mb-3">
            <label for="inter_product_type_{{ $index }}" class="form-label">PRODUCT TYPE</label>

            <select
                class="form-select form-select-md   @error('inputThree.' . $index . '.inter_product_type') is-invalid @enderror"
                wire:model="inputThree.{{ $index }}.inter_product_type">


                <option>SEED</option>
                <option>WARE</option>
                <option>VALUE ADDED PRODUCTS</option>
            </select>
            @error('inputThree.' . $index . '.inter_product_type')
                <x-error>{{ $message }}</x-error>
            @enderror

        </div>

        <div class="mb-3">
            <label for="inter_volume_sold_previous_period_{{ $index }}" class="form-label">VOLUME SOLD
                PREVIOUS PERIOD (METRIC TONNES)</label>
            <input type="number" min="0" step="any" step="any"
                class="form-control   @error('inputThree.' . $index . '.inter_volume_sold_previous_period') is-invalid @enderror"
                id="inter_volume_sold_previous_period_{{ $index }}"
                wire:model="inputThree.{{ $index }}.inter_volume_sold_previous_period">
            @error('inputThree.' . $index . '.inter_volume_sold_previous_period')
                <x-error>{{ $message }}</x-error>
            @enderror
        </div>

        <div class="mb-3">
            <label for="inter_financial_value_of_sales_{{ $index }}" class="form-label">FINANCIAL VALUE OF
                SALES</label>
            <input type="number" min="0" step="any" step="any"
                class="form-control   @error('inputThree.' . $index . '.inter_financial_value_of_sales') is-invalid @enderror"
                id="inter_financial_value_of_sales_{{ $index }}"
                wire:model="inputThree.{{ $index }}.inter_financial_value_of_sales">
            @error('inputThree.' . $index . '.inter_financial_value_of_sales')
                <x-error>{{ $message }}</x-error>
            @enderror
        </div>
        <div class="my-2">

            <div class="text-center btn-group" role="group" aria-label="Button group name" x-data>
                @if ($index > 0)
                    <button type="button" class="btn btn-theme-red"
                        wire:click='removeInputThree({{ $index }})'>
                        <i class="bx bx-minus fs-6"></i>
                    </button>
                @endif
                <button type="button" class="btn btn-secondary" @click='$wire.addInputThree()'>
                    <i class="bx bx-plus"></i>
                </button>

            </div>

        </div>
    @endforeach

</div>
