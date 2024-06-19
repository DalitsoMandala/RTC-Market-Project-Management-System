{{-- DO YOU HAVE ANY RTC MARKET CONTRACTUAL AGREEMENT? –SPECIFY PARTNER --}}

<div x-data="{
    has_rtc_market_contract: $wire.entangle('has_rtc_market_contract'),
    inputOne: $wire.entangle('inputOne')
}" x-init="$watch('has_rtc_market_contract', (v) => {

    if (v != 1) {
        $wire.resetValues('inputOne');
    }
});" x-show='has_rtc_market_contract == 1'>
    <div class="alert alert-primary" id="section-f" role="alert">
        <strong>DO YOU HAVE ANY RTC MARKET CONTRACTUAL AGREEMENT? –SPECIFY PARTNER</strong>
    </div>
    @php

        $countOne = 1;
    @endphp
    @foreach ($inputOne as $index => $input)
        <div class="card card-body">
            <h3>{{ $countOne++ }}</h3>
            <div class="mb-3">
                <label for="conc_date_recorded_{{ $index }}" class="form-label">DATE RECORDED</label>
                <input type="date" class="form-control" id="conc_date_recorded_{{ $index }}"
                    wire:model="inputOne.{{ $index }}.conc_date_recorded">
            </div>

            <div class="mb-3">
                <label for="conc_partner_name_{{ $index }}" class="form-label">PARTNER NAME</label>
                <input type="text" class="form-control" id="conc_partner_name_{{ $index }}"
                    wire:model="inputOne.{{ $index }}.conc_partner_name">
            </div>

            <div class="mb-3">
                <label for="conc_country_{{ $index }}" class="form-label">COUNTRY</label>
                <input type="text" class="form-control" id="conc_country_{{ $index }}"
                    wire:model="inputOne.{{ $index }}.conc_country">
            </div>

            <div class="mb-3">
                <label for="conc_date_of_maximum_sale_{{ $index }}" class="form-label">DATE OF MAXIMUM
                    SALE</label>
                <input type="date" class="form-control" id="conc_date_of_maximum_sale_{{ $index }}"
                    wire:model="inputOne.{{ $index }}.conc_date_of_maximum_sale">
            </div>

            <div class="mb-3">
                <label for="conc_product_type_{{ $index }}" class="form-label">PRODUCT TYPE</label>


                <select class="form-select form-select-md" wire:model="inputOne.{{ $index }}.conc_product_type">

                    <option>SEED</option>
                    <option>WARE</option>
                    <option>VALUE ADDED PRODUCTS</option>
                </select>


            </div>

            <div class="mb-3">
                <label for="conc_volume_sold_previous_period_{{ $index }}" class="form-label">VOLUME SOLD
                    PREVIOUS
                    PERIOD (METRIC TONNES)</label>
                <input type="number" class="form-control" id="conc_volume_sold_previous_period_{{ $index }}"
                    wire:model="inputOne.{{ $index }}.conc_volume_sold_previous_period">
            </div>

            <div class="mb-3">
                <label for="conc_financial_value_of_sales_{{ $index }}" class="form-label">FINANCIAL VALUE OF
                    SALES
                    (MALAWI KWACHA)
                </label>
                <input type="number" class="form-control" id="conc_financial_value_of_sales_{{ $index }}"
                    wire:model="inputOne.{{ $index }}.conc_financial_value_of_sales">
            </div>
            <div class="my-2">

                <div class="text-center btn-group" role="group" aria-label="Button group name" x-data>
                    @if ($index > 0)
                        <button type="button" class="btn btn-danger" wire:click='removeInputOne({{ $index }})'>
                            <i class="bx bx-minus fs-6"></i>
                        </button>
                    @endif
                    <button type="button" class="btn btn-secondary" @click='$wire.addInputOne()'>
                        <i class="bx bx-plus"></i>
                    </button>

                </div>

            </div>
        </div>
    @endforeach

</div>

<hr>


<div x-data="{
    sells_to_domestic_markets: $wire.entangle('sells_to_domestic_markets'),
    inputTwo: $wire.entangle('inputTwo')
}" x-init="$watch('sells_to_domestic_markets', (v) => {

    if (v != 1) {
        $wire.resetValues('inputTwo');
    }
});" x-show='sells_to_domestic_markets == 1'>
    {{-- DO YOU SELL YOUR RTC PRODUCTS TO DOMESTIC MARKETS? –SPECIFY PARTNER --}}
    <div class="alert alert-primary" id="section-g" role="alert">
        <strong>DO YOU SELL YOUR RTC PRODUCTS TO DOMESTIC MARKETS? –SPECIFY PARTNER</strong>
    </div>

    @php
        $countTwo = 1;

    @endphp
    @foreach ($inputTwo as $index => $input)
        <div class="card card-body">


            <h3>{{ $countTwo++ }}</h3>
            <div class="mb-3">
                <label for="dom_date_recorded_{{ $index }}" class="form-label">DATE RECORDED</label>
                <input type="date" class="form-control" id="dom_date_recorded_{{ $index }}"
                    wire:model="inputTwo.{{ $index }}.dom_date_recorded">
            </div>

            <div class="mb-3">
                <label for="dom_crop_type_{{ $index }}" class="form-label">CROP TYPE</label>


                <select class="form-select form-select-md" wire:model="inputTwo.{{ $index }}.dom_crop_type">

                    <option>CASSAVA</option>
                    <option>POTATO</option>
                    <option>SWEET POTATO</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="dom_market_name_{{ $index }}" class="form-label">MARKET NAME</label>
                <input type="text" class="form-control" id="dom_market_name_{{ $index }}"
                    wire:model="inputTwo.{{ $index }}.dom_market_name">
            </div>

            <div class="mb-3">
                <label for="dom_district_{{ $index }}" class="form-label">DISTRICT</label>
                <input type="text" class="form-control" id="dom_district_{{ $index }}"
                    wire:model="inputTwo.{{ $index }}.dom_district">
            </div>

            <div class="mb-3">
                <label for="dom_date_of_maximum_sale_{{ $index }}" class="form-label">DATE OF MAXIMUM
                    SALE</label>
                <input type="date" class="form-control" id="dom_date_of_maximum_sale_{{ $index }}"
                    wire:model="inputTwo.{{ $index }}.dom_date_of_maximum_sale">
            </div>

            <div class="mb-3">
                <label for="dom_product_type_{{ $index }}" class="form-label">PRODUCT TYPE</label>


                <select class="form-select form-select-md"
                    wire:model="inputTwo.{{ $index }}.dom_product_type">

                    <option>SEED</option>
                    <option>WARE</option>
                    <option>VALUE ADDED PRODUCTS</option>
                </select>

            </div>

            <div class="mb-3">
                <label for="dom_volume_sold_previous_period_{{ $index }}" class="form-label">VOLUME SOLD
                    PREVIOUS
                    PERIOD (METRIC TONNES)</label>
                <input type="number" class="form-control" id="dom_volume_sold_previous_period_{{ $index }}"
                    wire:model="inputTwo.{{ $index }}.dom_volume_sold_previous_period">
            </div>

            <div class="mb-3">
                <label for="dom_financial_value_of_sales_{{ $index }}" class="form-label">FINANCIAL VALUE OF
                    SALES</label>
                <input type="number" class="form-control" id="dom_financial_value_of_sales_{{ $index }}"
                    wire:model="inputTwo.{{ $index }}.dom_financial_value_of_sales">
            </div>
            <div class="my-2">

                <div class="text-center btn-group" role="group" aria-label="Button group name" x-data>
                    @if ($index > 0)
                        <button type="button" class="btn btn-danger"
                            wire:click='removeInputTwo({{ $index }})'>
                            <i class="bx bx-minus fs-6"></i>
                        </button>
                    @endif
                    <button type="button" class="btn btn-secondary" @click='$wire.addInputTwo()'>
                        <i class="bx bx-plus"></i>
                    </button>

                </div>

            </div>
        </div>
    @endforeach
</div>

<hr>

<div x-data="{
    sells_to_international_markets: $wire.entangle('sells_to_international_markets'),
    inputThree: $wire.entangle('inputThree')
}" x-init="$watch('sells_to_international_markets', (v) => {

    if (v != 1) {
        $wire.resetValues('inputThree');
    }
});" x-show='sells_to_international_markets == 1'>



    {{-- DO YOU SELL YOUR RTC PRODUCTS TO INTERNATIONAL MARKETS? –SPECIFY PARTNER --}}
    <div class="alert alert-primary" id="section-h" role="alert">
        <strong>DO YOU SELL YOUR RTC PRODUCTS TO INTERNATIONAL MARKETS? –SPECIFY PARTNER</strong>
    </div>

    @php

        $countThree = 1;
    @endphp
    @foreach ($inputThree as $index => $input)
        <div class="card card-body">
            <h3>{{ $countThree++ }}</h3>

            <div class="mb-3">
                <label for="inter_date_recorded_{{ $index }}" class="form-label">DATE RECORDED</label>
                <input type="date" class="form-control" id="inter_date_recorded_{{ $index }}"
                    wire:model="inputThree.{{ $index }}.inter_date_recorded">
            </div>

            <div class="mb-3">
                <label for="inter_crop_type_{{ $index }}" class="form-label">CROP TYPE</label>


                <select class="form-select form-select-md"
                    wire:model="inputThree.{{ $index }}.inter_crop_type">

                    <option>CASSAVA</option>
                    <option>POTATO</option>
                    <option>SWEET POTATO</option>
                </select>

            </div>

            <div class="mb-3">
                <label for="inter_market_name_{{ $index }}" class="form-label">MARKET NAME</label>
                <input type="text" class="form-control" id="inter_market_name_{{ $index }}"
                    wire:model="inputThree.{{ $index }}.inter_market_name">
            </div>

            <div class="mb-3">
                <label for="inter_country_{{ $index }}" class="form-label">COUNTRY</label>
                <input type="text" class="form-control" id="inter_country_{{ $index }}"
                    wire:model="inputThree.{{ $index }}.inter_country">
            </div>

            <div class="mb-3">
                <label for="inter_date_of_maximum_sale_{{ $index }}" class="form-label">DATE OF MAXIMUM
                    SALE</label>
                <input type="date" class="form-control" id="inter_date_of_maximum_sale_{{ $index }}"
                    wire:model="inputThree.{{ $index }}.inter_date_of_maximum_sale">
            </div>

            <div class="mb-3">
                <label for="inter_product_type_{{ $index }}" class="form-label">PRODUCT TYPE</label>

                <select class="form-select form-select-md"
                    wire:model="inputThree.{{ $index }}.inter_product_type">

                    <option>SEED</option>
                    <option>WARE</option>
                    <option>VALUE ADDED PRODUCTS</option>
                </select>

            </div>

            <div class="mb-3">
                <label for="inter_volume_sold_previous_period_{{ $index }}" class="form-label">VOLUME SOLD
                    PREVIOUS PERIOD (METRIC TONNES)</label>
                <input type="number" class="form-control"
                    id="inter_volume_sold_previous_period_{{ $index }}"
                    wire:model="inputThree.{{ $index }}.inter_volume_sold_previous_period">
            </div>

            <div class="mb-3">
                <label for="inter_financial_value_of_sales_{{ $index }}" class="form-label">FINANCIAL VALUE OF
                    SALES</label>
                <input type="number" class="form-control" id="inter_financial_value_of_sales_{{ $index }}"
                    wire:model="inputThree.{{ $index }}.inter_financial_value_of_sales">
            </div>
            <div class="my-2">

                <div class="text-center btn-group" role="group" aria-label="Button group name" x-data>
                    @if ($index > 0)
                        <button type="button" class="btn btn-danger"
                            wire:click='removeInputThree({{ $index }})'>
                            <i class="bx bx-minus fs-6"></i>
                        </button>
                    @endif
                    <button type="button" class="btn btn-secondary" @click='$wire.addInputThree()'>
                        <i class="bx bx-plus"></i>
                    </button>

                </div>

            </div>
        </div>
    @endforeach

</div>
