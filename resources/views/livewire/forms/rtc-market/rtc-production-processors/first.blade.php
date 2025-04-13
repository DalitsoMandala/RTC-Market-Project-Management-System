<div class="mb-5 alert alert-warning" id="section-b" role="alert">
    <strong>SECTION A: RTC MARKETING </strong>
</div>

<!-- Date of Follow Up -->
<div class="mb-3" x-data="{
    date_of_followup: $wire.entangle('date_of_followup').live,



}">
    <label for="dateOfFollowUp" class="form-label">Date of Follow Up</label>
    <x-flatpickr x-model='date_of_followup' :max-date="'today'" />

</div>


<!-- Market Segment (Multiple Responses) -->
<div class="mb-3">
    <label for="marketSegment" class="form-label">Market Segment (Multiple
        Responses)</label>
    <div class="">
        <div class="form-check">
            <input class="form-check-input @error('market_segment')is-invalid @enderror" type="checkbox"
                id="marketSegmentFresh" wire:model="market_segment" value="Fresh">
            <label class="form-check-label" for="marketSegmentFresh">Fresh</label>
        </div>
        <div class="form-check">
            <input class="form-check-input @error('market_segment')is-invalid @enderror" type="checkbox"
                id="marketSegmentProcessed" value="Processed" wire:model="market_segment">
            <label class="form-check-label" for="marketSegmentProcessed">Processed</label>
        </div>
    </div>


    @error('market_segment')
        <x-error>{{ $message }}</x-error>
    @enderror
</div>

<!-- RTC Market Contractual Agreement -->
<div class="mb-3" x-data="{
    has_rtc_market_contract: $wire.entangle('has_rtc_market_contract')
}">
    <label class="form-label">Do You Have Any RTC Market Contractual Agreement</label>
    <div class="@error('has_rtc_market_contract') border border-primary @enderror">
        <div class="form-check">
            <input class="form-check-input" type="radio" id="rtcMarketContractYes" value="1"
                x-model="has_rtc_market_contract">
            <label class="form-check-label" for="rtcMarketContractYes">Yes</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" id="rtcMarketContractNo" value="0"
                x-model="has_rtc_market_contract">
            <label class="form-check-label" for="rtcMarketContractNo">No</label>
        </div>
    </div>
    @error('has_rtc_market_contract')
        <x-error>{{ $message }}</x-error>
    @enderror
</div>



<!-- Total Volume of Production in Previous Season (Metric Tonnes) -->
<div class="px-2 mb-3" x-data="{
    total_vol_production_previous_season: $wire.entangle('total_vol_production_previous_season'),
    total_vol_production_previous_season_produce: $wire.entangle('total_vol_production_previous_season_produce'),
    total_vol_production_previous_season_seed: $wire.entangle('total_vol_production_previous_season_seed'),
    total_vol_production_previous_season_cuttings: $wire.entangle('total_vol_production_previous_season_cuttings'),

}"
    x-effect="total_vol_production_previous_season = Number(total_vol_production_previous_season_produce || 0)
     + Number(total_vol_production_previous_season_seed || 0) +
      Number(total_vol_production_previous_season_cuttings || 0)">
    <label for="totalVolumeProduction" class="form-label">Total Volume of
        Production in Previous Season (Metric Tonnes)</label>


    <table class="table table-bordered table-striped table-hover table-responsive">

        <tbody>
            <tr>
                <td>Produce (MT)</td>
                <td>
                    <input type="number" min="0" step="any"
                        class="form-control @error('total_vol_production_previous_season_produce') is-invalid @enderror"
                        x-model='total_vol_production_previous_season_produce'>
                    @error('total_vol_production_previous_season_produce')
                        <x-error>{{ $message }}</x-error>
                    @enderror
                </td>

            </tr>
            <tr>
                <td>Seed (MT/Bundles)</td>
                <td>
                    <input type="number" min="0" step="any"
                        class="form-control @error('total_vol_production_previous_season_seed') is-invalid @enderror"
                        x-model='total_vol_production_previous_season_seed'>
                    @error('total_vol_production_previous_season_seed')
                        <x-error>{{ $message }}</x-error>
                    @enderror
                </td>
            </tr>

            <tr>
                <td>Cuttings (MT)</td>
                <td>
                    <input type="number" min="0" step="any"
                        class="form-control @error('total_vol_production_previous_season_cuttings') is-invalid @enderror"
                        x-model='total_vol_production_previous_season_cuttings'>
                    @error('total_vol_production_previous_season_cuttings')
                        <x-error>{{ $message }}</x-error>
                    @enderror
                </td>
            </tr>

        </tbody>
        <tfoot>
            <tr>
                <td class="fw-bold">Total</td>
                <td>
                    <input type="number" min="0" step="any" readonly
                        class="form-control @error('total_vol_production_previous_season') is-invalid @enderror bg-subtle-warning"
                        x-model='total_vol_production_previous_season'>

                    @error('total_vol_production_previous_season')
                        <x-error>{{ $message }}</x-error>
                    @enderror
                </td>
            </tr>
        </tfoot>

    </table>

</div>

<!-- Total Value Production Previous Season (Financial Value-MWK) -->
<div class="px-2 mb-3" :class="{ 'border border-danger p-2': dateSet === false }" x-data="{
    total_production_value_previous_season: $wire.entangle('total_production_value_previous_season'),
    cuttingsTotal: 0,
    seedTotal: 0,
    produceTotal: 0,
    subTotal: 0,
    lastCalculatedTotal: null, // Track the last calculated total
    dateSet: null,
    init() {
        this.$watch('total_production_value_previous_season', (value) => {
            this.produceTotal = Number(value.produce_value || 0) * Number(value.produce_prevailing_price || 0);
            this.seedTotal = Number(value.seed_value || 0) * Number(value.seed_prevailing_price || 0);
            this.cuttingsTotal = Number(value.cuttings_value || 0) * Number(value.cuttings_prevailing_price || 0);
            this.subTotal = this.produceTotal + this.seedTotal + this.cuttingsTotal;
            this.total_production_value_previous_season.value = this.subTotal;
        }, { deep: true });
    },

    calculate() {
        // Only proceed if the total has changed since last calculation
        let dateOfFollowUp = $wire.date_of_followup;
        this.dateSet = dateOfFollowUp ? true : false;

        if (this.lastCalculatedTotal !== this.subTotal && dateOfFollowUp) {
            this.$wire.exchangeRateCalculateProduction();
            this.lastCalculatedTotal = this.subTotal; // Update the last calculated total
        }
    }
}">
    <label for="totalValueProduction" class="my-3 form-label">Total Value
        Production
        Previous Season (Financial Value-MWK) *</label> <br>
    <small class="fw-bold"></small>



    <div class="alert alert-warning alert-dismissible alert-label-icon label-arrow fade show" role="alert">
        <i class="mdi mdi-alert-outline label-icon"></i><strong>Note</strong> - Please fill the inputs below after
        setting the <span class="text-decoration-underline">date
            of
            follow up</span>, then click <span class="fw-bold">Calculate Now</span> to get the financial value.


    </div>

    <table class="table mt-4 fs-6 table-bordered table-striped table-hover table-responsive">
        <thead>
            <tr class="">
                <th></th>
                <th>Value</th>
                <th>Prevailing Market Price per Kg/Bundle</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Produce (MT)</td>
                <td>
                    <input type="number" min="0" step="any"
                        class="form-control @error('total_production_value_previous_season.produce_value') is-invalid @enderror"
                        x-model='total_production_value_previous_season.produce_value'>
                </td>
                <td>
                    <input type="number" min="0" step="any"
                        class="form-control @error('total_production_value_previous_season.produce_prevailing_price') is-invalid @enderror"
                        x-model='total_production_value_previous_season.produce_prevailing_price'>

                </td>
                <td>
                    <input type="number" min="0" step="any" readonly class="form-control bg-subtle-warning"
                        x-model='produceTotal'>
                </td>

            </tr>
            <tr>
                <td>Seed (MT/Bundles)</td>
                <td>
                    <input type="number" min="0" step="any"
                        class="form-control @error('total_production_value_previous_season.seed_value') is-invalid @enderror"
                        x-model='total_production_value_previous_season.seed_value'>
                </td>

                <td>
                    <input type="number" min="0" step="any"
                        class="form-control @error('total_production_value_previous_season.seed_prevailing_price') is-invalid @enderror"
                        x-model='total_production_value_previous_season.seed_prevailing_price'>

                </td>

                <td>
                    <input type="number" min="0" step="any" readonly
                        class="form-control bg-subtle-warning" x-model='seedTotal'>
                </td>
            </tr>

            <tr>
                <td>Cuttings (MT)</td>
                <td>
                    <input type="number" min="0" step="any"
                        class="form-control @error('total_production_value_previous_season.cuttings_value') is-invalid @enderror"
                        x-model='total_production_value_previous_season.cuttings_value'>
                </td>

                <td>
                    <input type="number" min="0" step="any"
                        class="form-control @error('total_production_value_previous_season.cuttings_prevailing_price') is-invalid @enderror"
                        x-model='total_production_value_previous_season.cuttings_prevailing_price'>

                </td>
                <td>
                    <input type="number" min="0" step="any" readonly
                        class="form-control bg-subtle-warning" x-model='cuttingsTotal'>
                </td>

            </tr>

        </tbody>
        <tfoot>

            <tr>
                <td>
                    <button wire:loading.attr='disabled' type="button" @click="calculate"
                        :class="{
                            'btn-warning': lastCalculatedTotal !== subTotal,
                            'btn-secondary': lastCalculatedTotal === subTotal
                        }"
                        class="btn btn-sm" :disabled="lastCalculatedTotal === subTotal">
                        Calculate Now
                        <i class="bx bx-arrow-to-right"></i>

                    </button>
                </td>
                <td>
                    <label for="">Total (MWK)</label>
                    <input type="number" min="0" step="any" readonly
                        class="form-control @error('total_production_value_previous_season.value') is-invalid @enderror bg-subtle-warning"
                        x-model='total_production_value_previous_season.value'>
                </td>
                <td class="fw-bold">
                    <label for="">USD Rate ($)</label>
                    <input type="number" min="0" step="any" readonly
                        class="form-control @error('total_production_value_previous_season.rate') is-invalid @enderror bg-subtle-warning"
                        x-model='total_production_value_previous_season.rate'>
                </td>

                <td>
                    <label for="">Financial Value (USD)</label>
                    <input type="text" min="0" step="any" readonly
                        class="form-control @error('total_production_value_previous_season.total') is-invalid @enderror bg-subtle-warning"
                        x-model='total_production_value_previous_season.total'>
                    @error('total_production_value_previous_season.total')
                        <x-error>{{ $message }}</x-error>
                    @enderror
                </td>
            </tr>

        </tfoot>

    </table>

    <span class="text-danger" x-show="dateSet === false">
        Date of follow up is not set! Please set the date first.
    </span>


</div>

<!-- Sell RTC Products to Domestic Markets -->
<div class="mb-3" x-data="{
    sells_to_domestic_markets: $wire.entangle('sells_to_domestic_markets')
}">
    <label class="form-label">Do You Sell Your RTC Products to Domestic Markets</label>
    <div class=" @error('sells_to_domestic_markets') is-invalid @enderror">
        <div class="form-check">
            <input class="form-check-input" type="radio" id="sellToDomesticMarketsYes" value="1"
                x-model="sells_to_domestic_markets">
            <label class="form-check-label" for="sellToDomesticMarketsYes">Yes</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" id="sellToDomesticMarketsNo" value="0"
                x-model="sells_to_domestic_markets">
            <label class="form-check-label" for="sellToDomesticMarketsNo">No</label>
        </div>
    </div>

    @error('sells_to_domestic_markets')
        <x-error>{{ $message }}</x-error>
    @enderror
</div>

<!-- Sell Products to International Markets -->
<div class="mb-3" x-data="{
    sells_to_international_markets: $wire.entangle('sells_to_international_markets')
}">
    <label class="form-label">Do You Sell Your Products to International Markets</label>
    <div class=" @error('sells_to_international_markets') border border-primary @enderror">
        <div class="form-check">
            <input class="form-check-input" type="radio" id="sellToInternationalMarketsYes" value="1"
                x-model="sells_to_international_markets">
            <label class="form-check-label" for="sellToInternationalMarketsYes">Yes</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" id="sellToInternationalMarketsNo" value="0"
                x-model="sells_to_international_markets">
            <label class="form-check-label" for="sellToInternationalMarketsNo">No</label>
        </div>
    </div>
    @error('sells_to_international_markets')
        <x-error>{{ $message }}</x-error>
    @enderror
</div>


<!-- Sell Products Through Market Information Systems -->
<div class="mb-3" x-data="{
    uses_market_information_systems: $wire.entangle('uses_market_information_systems')
}">
    <label class="form-label">Do You Sell Your Products Through Market Information Systems</label>
    <div class=" @error('uses_market_information_systems') border border-danger @enderror">
        <div class="form-check">
            <input class="form-check-input" type="radio" id="sellThroughMarketInfoYes" value="1"
                x-model="uses_market_information_systems">
            <label class="form-check-label" for="sellThroughMarketInfoYes">Yes</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" id="sellThroughMarketInfoNo" value="0"
                x-model="uses_market_information_systems">
            <label class="form-check-label" for="sellThroughMarketInfoNo">No</label>
        </div>
    </div>
    @error('uses_market_information_systems')
        <x-error>{{ $message }}</x-error>
    @enderror
</div>

<div class="mb-3" x-data="{
    uses_market_information_systems: $wire.entangle('uses_market_information_systems'),

}" x-init="$watch('uses_market_information_systems', (v) => {

    if (v != 1) {
        $wire.resetValues('market_information_systems');

    }
});">



    <div class="mb-3" x-show='uses_market_information_systems == 1'>





        <div class="px-2 row" x-data>
            <label for="" class="form-label">Specify Market Information System</label>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>


                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($market_information_systems as $index => $value)
                        <tr>
                            <td>
                                <input
                                    class="form-control form-control-sm @error('market_information_systems.' . $index . '.name') is-invalid @enderror"
                                    wire:model='market_information_systems.{{ $index }}.name' />
                                @error('market_information_systems.' . $index . '.name')
                                    <x-error>{{ $message }}</x-error>
                                @enderror
                            </td>


                            <td>
                                <button @click="$wire.removeMIS({{ $index }})"
                                    @if (count($market_information_systems) <= 1) disabled @endif
                                    class="btn btn-danger btn-sm">Remove
                                    <i class="bx bx-trash"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr></tr>
                    <td colspan="2">
                        <button type="button" class="btn btn-warning btn-sm" @click='$wire.addMIS()'>
                            Add Row <i class="bx bx-plus"></i>
                        </button>
                    </td>
                    </tr>
                </tfoot>
            </table>



        </div>






    </div>


</div>


<!-- Sell RTC Produce Through Aggregation Centers -->

<div x-data="{
    sells_to_aggregation_centers: $wire.entangle('sells_to_aggregation_centers'),



}" x-init="$watch('sells_to_aggregation_centers', (v) => {

    if (v != 1) {

        $wire.resetValues('aggregation_center_sales');
        $wire.total_vol_aggregation_center_sales = 0
    } else {
        $wire.total_vol_aggregation_center_sales = null
    }
});">
    <div class="mb-3">
        <label for="sellThroughAggregationCenters" class="my-3 form-label ">Do
            You Sell RTC
            Produce Through Aggregation Centers</label>

        <div class=" @error('sells_to_aggregation_centers') border border-primary @enderror">
            <div class="form-check">
                <input class="form-check-input" type="radio" id="aggregationCenterResponseYes" value="1"
                    wire:model='sells_to_aggregation_centers'>
                <label class="form-check-label">Yes</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" id="aggregationCenterResponseNo" value="0"
                    wire:model='sells_to_aggregation_centers'>
                <label class="form-check-label">No</label>
            </div>
        </div>

        @error('aggregation_centers')
            <x-error>{{ $message }}</x-error>
        @enderror
    </div>



    <!-- Total Volume of RTC Sold Through Aggregation Centers in Previous Season (Metric Tonnes) -->
    <div class="mb-3" x-show='sells_to_aggregation_centers == 1'>



        <div class="px-2 row" x-data>
            <label for="totalVolumeSoldThroughAggregation" class="form-label">Specify Aggregation Center</label>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>


                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($aggregation_center_sales as $index => $value)
                        <tr>
                            <td>
                                <input
                                    class="form-control form-control-sm @error('aggregation_center_sales.' . $index . '.name') is-invalid @enderror"
                                    wire:model='aggregation_center_sales.{{ $index }}.name' />
                                @error('aggregation_center_sales.' . $index . '.name')
                                    <x-error>{{ $message }}</x-error>
                                @enderror
                            </td>


                            <td>
                                <button @click="$wire.removeSales({{ $index }})"
                                    @if (count($aggregation_center_sales) <= 1) disabled @endif
                                    class="btn btn-danger btn-sm">Remove
                                    <i class="bx bx-trash"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr></tr>
                    <td colspan="2">
                        <button type="button" class="btn btn-warning btn-sm" @click='$wire.addSales()'>
                            Add Row <i class="bx bx-plus"></i>
                        </button>
                    </td>
                    </tr>
                </tfoot>
            </table>



        </div>




    </div>

</div>


<div class="mb-3">
    <label for="" class="form-label">Total Volume
        of RTC Sold Through Aggregation Centers in Previous Season (Metric
        Tonnes)</label>
    <input type="number" min="0" step="any"
        class="form-control @error('total_vol_aggregation_center_sales') is-invalid @enderror"
        wire:model='total_vol_aggregation_center_sales' id="" aria-describedby="helpId" placeholder="" />
    @error('total_vol_aggregation_center_sales')
        <x-error>{{ $message }}</x-error>
    @enderror
</div>
