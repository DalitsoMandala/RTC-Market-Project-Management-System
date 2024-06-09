{{-- DO YOU HAVE ANY RTC MARKET CONTRACTUAL AGREEMENT? –SPECIFY PARTNER --}}
<div class="alert alert-primary" role="alert">
    <strong>DO YOU HAVE ANY RTC MARKET CONTRACTUAL AGREEMENT? –SPECIFY PARTNER</strong>
</div>


<div class="mb-3">
    <label for="date_recorded" class="form-label">DATE RECORDED</label>
    <input type="date" class="form-control" id="date_recorded" wire:model="conc_date_recorded">
</div>

<div class="mb-3">
    <label for="partner_name" class="form-label">PARTNER NAME</label>
    <input type="text" class="form-control" id="partner_name" wire:model="conc_partner_name">
</div>

<div class="mb-3">
    <label for="country" class="form-label">COUNTRY</label>
    <input type="text" class="form-control" id="country" wire:model="conc_country">
</div>

<div class="mb-3">
    <label for="date_of_maximum_sale" class="form-label">DATE OF MAXIMUM SALE</label>
    <input type="date" class="form-control" id="date_of_maximum_sale" wire:model="conc_date_of_maximum_sale">
</div>

<div class="mb-3">
    <label for="product_type" class="form-label">PRODUCT TYPE</label>
    <input type="text" class="form-control" id="product_type" wire:model="conc_product_type">
</div>

<div class="mb-3">
    <label for="volume_sold_previous_period" class="form-label">VOLUME SOLD PREVIOUS PERIOD (METRIC TONNES)</label>
    <input type="number" class="form-control" id="volume_sold_previous_period"
        wire:model="conc_volume_sold_previous_period">
</div>

<div class="mb-3">
    <label for="financial_value_of_sales" class="form-label">FINANCIAL VALUE OF SALES (MALAWI KWACHA)</label>
    <input type="number" class="form-control" id="financial_value_of_sales" wire:model="conc_financial_value_of_sales">
</div>

<div class="my-2">
    <div class="text-center btn-group" role="group" aria-label="Button group name">
        <button type="button" class="btn btn-danger">
            <i class="bx bx-minus fs-6"></i>
        </button>
        <button type="button" class="btn btn-secondary">
            <i class="bx bx-plus"></i>
        </button>

    </div>

</div>
{{-- DO YOU SELL YOUR RTC PRODUCTS TO DOMESTIC MARKETS? –SPECIFY PARTNER --}}
<div class="alert alert-primary" role="alert">
    <strong>DO YOU SELL YOUR RTC PRODUCTS TO DOMESTIC MARKETS? –SPECIFY PARTNER</strong>
</div>

<div class="mb-3">
    <label for="date_recorded" class="form-label">DATE RECORDED</label>
    <input type="date" class="form-control" id="date_recorded" wire:model="dom_date_recorded">
</div>

<div class="mb-3">
    <label for="crop_type" class="form-label">CROP TYPE</label>
    <input type="text" class="form-control" id="crop_type" wire:model="dom_crop_type">
</div>

<div class="mb-3">
    <label for="market_name" class="form-label">MARKET NAME</label>
    <input type="text" class="form-control" id="market_name" wire:model="dom_market_name">
</div>

<div class="mb-3">
    <label for="district" class="form-label">DISTRICT</label>
    <input type="text" class="form-control" id="district" wire:model="dom_district">
</div>

<div class="mb-3">
    <label for="date_of_maximum_sale" class="form-label">DATE OF MAXIMUM SALE</label>
    <input type="date" class="form-control" id="date_of_maximum_sale" wire:model="dom_date_of_maximum_sale">
</div>

<div class="mb-3">
    <label for="product_type" class="form-label">PRODUCT TYPE</label>
    <input type="text" class="form-control" id="product_type" wire:model="dom_product_type">
</div>

<div class="mb-3">
    <label for="volume_sold_previous_period" class="form-label">VOLUME SOLD PREVIOUS PERIOD (METRIC TONNES)</label>
    <input type="number" class="form-control" id="volume_sold_previous_period"
        wire:model="dom_volume_sold_previous_period">
</div>

<div class="mb-3">
    <label for="financial_value_of_sales" class="form-label">FINANCIAL VALUE OF SALES</label>
    <input type="number" class="form-control" id="financial_value_of_sales"
        wire:model="dom_financial_value_of_sales">
</div>
<div class="my-2">
    <div class="text-center btn-group" role="group" aria-label="Button group name">
        <button type="button" class="btn btn-danger">
            <i class="bx bx-minus fs-6"></i>
        </button>
        <button type="button" class="btn btn-secondary">
            <i class="bx bx-plus"></i>
        </button>

    </div>

</div>
{{-- DO YOU SELL YOUR RTC PRODUCTS TO INTERNATIONAL MARKETS? –SPECIFY PARTNER --}}
<div class="alert alert-primary" role="alert">
    <strong>DO YOU SELL YOUR RTC PRODUCTS TO INTERNATIONAL MARKETS? –SPECIFY PARTNER</strong>
</div>

<div class="mb-3">
    <label for="date_recorded" class="form-label">DATE RECORDED</label>
    <input type="date" class="form-control" id="date_recorded" wire:model="inter_date_recorded">
</div>

<div class="mb-3">
    <label for="crop_type" class="form-label">CROP TYPE</label>
    <input type="text" class="form-control" id="crop_type" wire:model="inter_crop_type">
</div>

<div class="mb-3">
    <label for="market_name" class="form-label">MARKET NAME</label>
    <input type="text" class="form-control" id="market_name" wire:model="inter_market_name">
</div>

<div class="mb-3">
    <label for="country" class="form-label">COUNTRY</label>
    <input type="text" class="form-control" id="country" wire:model="inter_country">
</div>

<div class="mb-3">
    <label for="date_of_maximum_sale" class="form-label">DATE OF MAXIMUM SALE</label>
    <input type="date" class="form-control" id="date_of_maximum_sale" wire:model="inter_date_of_maximum_sale">
</div>

<div class="mb-3">
    <label for="product_type" class="form-label">PRODUCT TYPE</label>
    <input type="text" class="form-control" id="product_type" wire:model="inter_product_type">
</div>

<div class="mb-3">
    <label for="volume_sold_previous_period" class="form-label">VOLUME SOLD PREVIOUS PERIOD (METRIC TONNES)</label>
    <input type="number" class="form-control" id="volume_sold_previous_period"
        wire:model="inter_volume_sold_previous_period">
</div>

<div class="mb-3">
    <label for="financial_value_of_sales" class="form-label">FINANCIAL VALUE OF SALES</label>
    <input type="number" class="form-control" id="financial_value_of_sales"
        wire:model="inter_financial_value_of_sales">
</div>
<div class="my-2">
    <div class="text-center btn-group" role="group" aria-label="Button group name">
        <button type="button" class="btn btn-danger">
            <i class="bx bx-minus fs-6"></i>
        </button>
        <button type="button" class="btn btn-secondary">
            <i class="bx bx-plus"></i>
        </button>

    </div>

</div>
