<div class="alert alert-warning" id="section-b" role="alert">
    <strong>SECTION A: RTC PRODUCTION </strong>
</div>

<!-- Date of Follow Up -->
<div class="mb-3" x-data="{
    date_of_followup: $wire.entangle('date_of_followup'),



}" x-effect="$dispatch('date-change')">
    <label for="dateOfFollowUp" class="form-label">Date of Follow Up</label>
    <div class="@error('date_of_followup') border border-danger rounded p-0 @enderror">
        <x-flatpickr x-model='date_of_followup' :max-date="'today'" />
    </div>

    @error('date_of_followup')
        <x-error>{{ $message }}</x-error>
    @enderror
</div>



<!-- Area Under Cultivation (Number of Acres) by Variety -->
<div class="mb-3">
    <label for="areaUnderCultivation" class="my-3 form-label fw-bold">Area Under
        Cultivation
        (Number of Acres) by Variety</label>



    <div class="px-2 row" x-data="{
        area_under_cultivation: $wire.entangle('area_under_cultivation').live,
        init() {
            const indices = [];
            const structuredData = [];
            const draftData = this.draftData;
            const { count, data } = extractNestedData(draftData, 'area_under_cultivation');
            data.forEach((item, index) => {
                this.area_under_cultivation[index] = item;
            })
    
    
        }
    }">

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Variety</th>
                    <th>Area in Acres</th>

                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($area_under_cultivation as $index => $value)
                    <tr>
                        <td>
                            <input
                                class="form-control form-control-sm @error('area_under_cultivation.' . $index . '.variety') is-invalid @enderror"
                                wire:model='area_under_cultivation.{{ $index }}.variety' />
                            @error('area_under_cultivation.' . $index . '.variety')
                                <x-error>{{ $message }}</x-error>
                            @enderror
                        </td>
                        <td>
                            <input type="number" step="any" min="0"
                                class="form-control form-control-sm @error('area_under_cultivation.' . $index . '.area') is-invalid @enderror"
                                wire:model='area_under_cultivation.{{ $index }}.area' />
                            @error('area_under_cultivation.' . $index . '.area')
                                <x-error>{{ $message }}</x-error>
                            @enderror
                        </td>

                        <td>
                            <button @click="$wire.removeAreaofCultivation({{ $index }})"
                                @if (count($area_under_cultivation) <= 1) disabled @endif class="btn btn-danger btn-sm">Remove
                                <i class="bx bx-trash"></i></button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr></tr>
                <td colspan="3">
                    <button type="button" class="btn btn-warning btn-sm" @click='$wire.addAreaofCultivation()'>
                        Add Row <i class="bx bx-plus"></i>
                    </button>
                </td>
                </tr>
            </tfoot>
        </table>



    </div>


</div>


<!-- Number of Plantlets Produced -->
<div class="mb-3" x-data="{
    group: $wire.entangle('group'),
    number_of_plantlets_produced: $wire.entangle('number_of_plantlets_produced')
}">

    <label for="numberOfPlantlets" class="my-3 form-label fw-bold">Number of
        Plantlets
        Produced </label>


    <span class="text-danger fw-bold">[For early generation
        seed producers only]</span>
    <div class="px-2 row">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <td>Cassava</td>
                    <td><input type="number" min="0" step="any"
                            class="form-control form-control-sm @error('number_of_plantlets_produced.cassava') is-invalid @enderror"
                            id="cassavaPlantlets" x-model="number_of_plantlets_produced.cassava">
                        @error('number_of_plantlets_produced.cassava')
                            <x-error>{{ $message }}</x-error>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <td>Sweet Potato</td>
                    <td> <input type="number" min="0" step="any"
                            class="form-control form-control-sm @error('number_of_plantlets_produced.sweet_potato') is-invalid @enderror"
                            id="sweetPotatoPlantlets" x-model="number_of_plantlets_produced.sweet_potato">
                        @error('number_of_plantlets_produced.sweet_potato')
                            <x-error>{{ $message }}</x-error>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <td>Potato</td>
                    <td> <input type="number" min="0" step="any"
                            class="form-control form-control-sm @error('number_of_plantlets_produced.potato') is-invalid @enderror"
                            id="potatoPlantlets" x-model="number_of_plantlets_produced.potato">
                        @error('number_of_plantlets_produced.potato')
                            <x-error>{{ $message }}</x-error>
                        @enderror
                    </td>
                </tr>
            </tbody>
        </table>


    </div>


</div>

<!-- Number of Screen House Vines Harvested (Sweet Potatoes) -->
<div class="mb-3" x-data="{
    group: $wire.entangle('group'),
    number_of_screen_house_vines_harvested: $wire.entangle('number_of_screen_house_vines_harvested'),
    init() {

    }

}">
    <label for="numberOfScreenHouseVines" class="form-label">Number of Screen
        House Vines Harvested (Sweet Potatoes)</label>
    <span class="text-danger fw-bold">[For early generation
        seed producers only]</span>
    <input type="number" min="0" step="any"
        class="form-control @error('number_of_screen_house_vines_harvested') is-invalid @enderror"
        id="numberOfScreenHouseVines" x-model='number_of_screen_house_vines_harvested'>
    @error('number_of_screen_house_vines_harvested')
        <x-error>{{ $message }}</x-error>
    @enderror
</div>
<!-- Number of Screen House Mini-Tubers Harvested (Potato) -->
<div class="mb-3" x-data="{
    group: $wire.entangle('group'),
    number_of_screen_house_min_tubers_harvested: $wire.entangle('number_of_screen_house_min_tubers_harvested'),
    init() {

    }
}">
    <label for="numberOfMiniTubers" class="form-label">Number of Screen House
        Mini-Tubers Harvested (Potato)</label>
    <span class="text-danger fw-bold">[For early generation
        seed producers only]</span>
    <input type="number" min="0" step="any"
        class="form-control @error('number_of_screen_house_min_tubers_harvested') is-invalid @enderror"
        id="numberOfMiniTubers" x-model='number_of_screen_house_min_tubers_harvested'>
    @error('number_of_screen_house_min_tubers_harvested')
        <x-error>{{ $message }}</x-error>
    @enderror
</div>
<!-- Number of SAH Plants Produced (Cassava) -->
<div class="mb-3" x-data="{
    group: $wire.entangle('group'),
    number_of_sah_plants_produced: $wire.entangle('number_of_sah_plants_produced'),
    init() {

    }
}">
    <label for="numberOfSAHPlants" class="form-label">Number of SAH Plants
        Produced (Cassava)</label>
    <span class="text-danger fw-bold">[For early generation
        seed producers only]</span>
    <input type="number" min="0" step="any"
        class="form-control @error('number_of_sah_plants_produced') is-invalid @enderror" id="numberOfSAHPlants"
        x-model='number_of_sah_plants_produced'>
    @error('number_of_sah_plants_produced')
        <x-error>{{ $message }}</x-error>
    @enderror
</div>

<!-- Area Under Basic Seed Multiplication (Number of Acres) -->
<div class="mb-3" x-data="{
    group: $wire.entangle('group'),
    area_under_basic_seed_multiplication: $wire.entangle('area_under_basic_seed_multiplication'),

    init() {
        const indices = [];
        const structuredData = [];
        const draftData = this.draftData;
        const { count, data } = extractNestedData(draftData, 'area_under_basic_seed_multiplication');
        data.forEach((item, index) => {
            this.area_under_basic_seed_multiplication[index] = item;
        })

    }

}">
    <label for="areaUnderBasicSeed" class="my-3 form-label fw-bold">Area Under
        Basic Seed
        Multiplication (Number of Acres)</label>
    <span class="text-danger fw-bold">[For early generation
        seed producers only]</span>


    <div class="px-2 row">

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Variety</th>
                    <th>Area in Acres</th>

                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($area_under_basic_seed_multiplication as $index => $value)
                    <tr>
                        <td>
                            <input
                                class="form-control form-control-sm @error('area_under_basic_seed_multiplication.' . $index . '.variety') is-invalid @enderror"
                                wire:model='area_under_basic_seed_multiplication.{{ $index }}.variety' />
                            @error('area_under_basic_seed_multiplication.' . $index . '.variety')
                                <x-error>{{ $message }}</x-error>
                            @enderror
                        </td>
                        <td>
                            <input type="number" min="0" step="any"
                                class="form-control form-control-sm @error('area_under_basic_seed_multiplication.' . $index . '.area') is-invalid @enderror"
                                wire:model='area_under_basic_seed_multiplication.{{ $index }}.area' />
                            @error('area_under_basic_seed_multiplication.' . $index . '.area')
                                <x-error>{{ $message }}</x-error>
                            @enderror
                        </td>

                        <td>
                            <button @click="$wire.removeBasicSeed({{ $index }})"
                                @if (count($area_under_basic_seed_multiplication) <= 1) disabled @endif class="btn btn-danger btn-sm">Remove
                                <i class="bx bx-trash"></i></button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr></tr>
                <td colspan="3">
                    <button type="button" class="btn btn-warning btn-sm" @click='$wire.addBasicSeed()'>
                        Add Row <i class="bx bx-plus"></i>
                    </button>
                </td>
                </tr>
            </tfoot>
        </table>




    </div>


</div>

<!-- Area Under Certified Seed Multiplication -->
<div class="mb-3">
    <label for="areaUnderCertifiedSeed" class="my-3 form-label fw-bold">Area Under
        Certified
        Seed Multiplication</label>
    <div class="px-2 row" x-data="{
        area_under_certified_seed_multiplication: $wire.entangle('area_under_certified_seed_multiplication'),
    
        init() {
            const indices = [];
            const structuredData = [];
            const draftData = this.draftData;
            const { count, data } = extractNestedData(draftData, 'area_under_certified_seed_multiplication');
            data.forEach((item, index) => {
                this.area_under_certified_seed_multiplication[index] = item;
            })
        }
    }">

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Variety</th>
                    <th>Area in Acres</th>

                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($area_under_certified_seed_multiplication as $index => $value)
                    <tr>
                        <td>
                            <input
                                class="form-control form-control-sm @error('area_under_certified_seed_multiplication.' . $index . '.variety') is-invalid @enderror"
                                wire:model='area_under_certified_seed_multiplication.{{ $index }}.variety' />
                            @error('area_under_certified_seed_multiplication.' . $index . '.variety')
                                <x-error>{{ $message }}</x-error>
                            @enderror
                        </td>
                        <td>
                            <input type="number" step="any" min="0"
                                class="form-control form-control-sm @error('area_under_certified_seed_multiplication.' . $index . '.area') is-invalid @enderror"
                                wire:model='area_under_certified_seed_multiplication.{{ $index }}.area' />
                            @error('area_under_certified_seed_multiplication.' . $index . '.area')
                                <x-error>{{ $message }}</x-error>
                            @enderror
                        </td>

                        <td>
                            <button @click="$wire.removeCertifiedSeed({{ $index }})"
                                @if (count($area_under_certified_seed_multiplication) <= 1) disabled @endif class="btn btn-danger btn-sm">Remove
                                <i class="bx bx-trash"></i></button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr></tr>
                <td colspan="3">
                    <button type="button" class="btn btn-warning btn-sm" @click='$wire.addCertifiedSeed()'>
                        Add Row <i class="bx bx-plus"></i>
                    </button>
                </td>
                </tr>
            </tfoot>
        </table>





    </div>



</div>

<!-- Are You a Registered Seed Producer -->
<div class="mb-3" x-data="{
    is_registered_seed_producer: $wire.entangle('is_registered_seed_producer'),
}">

    <label class="form-label">Are You a Registered Seed Producer</label>
    <div class="@error('is_registered_seed_producer') border border-danger @enderror">
        <div class="form-check">
            <input class="form-check-input" type="radio" id="registeredSeedProducerYes" value="1"
                x-model="is_registered_seed_producer">
            <label class="form-check-label" for="registeredSeedProducerYes">Yes</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" checked type="radio" id="registeredSeedProducerNo" value="0"
                x-model="is_registered_seed_producer">
            <label class="form-check-label" for="registeredSeedProducerNo">No</label>
        </div>
    </div>

    @error('is_registered_seed_producer')
        <x-error>{{ $message }}</x-error>
    @enderror
</div>


<!-- Registration Details (Seed Services Unit) -->

<div x-data="{
    is_registered_seed_producer: $wire.entangle('is_registered_seed_producer'),
    registrations: $wire.entangle('registrations'),

    init() {
        const indices = [];
        const structuredData = [];
        const draftData = this.draftData;
        const { count, data } = extractNestedData(draftData, 'registrations');
        data.forEach((item, index) => {
            this.registrations[index] = item;
        })
    }

}" x-show="is_registered_seed_producer==1" x-init="$watch('is_registered_seed_producer', (v) => {
    if (v != 1) { $wire.resetValues('registrations'); }
})" class="px-2">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Variety</th>
                <th>Reg. Date</th>
                <th>Reg. No.</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($registrations as $index => $reg)
                <tr>
                    <td><input type="text" wire:model="registrations.{{ $index }}.variety"
                            class="form-control form-control-sm @error('registrations.' . $index . '.variety') is-invalid @enderror" />

                        @error('registrations.' . $index . '.variety')
                            <x-error>{{ $message }}</x-error>
                        @enderror
                    </td>
                    <td><input type="date" wire:model="registrations.{{ $index }}.reg_date"
                            class="form-control form-control-sm @error('registrations.' . $index . '.reg_date') is-invalid @enderror" />

                        @error('registrations.' . $index . '.reg_date')
                            <x-error>{{ $message }}</x-error>
                        @enderror
                    </td>
                    <td><input type="text" wire:model="registrations.{{ $index }}.reg_no"
                            class="form-control form-control-sm @error('registrations.' . $index . '.reg_no') is-invalid @enderror" />

                        @error('registrations.' . $index . '.reg_no')
                            <x-error>{{ $message }}</x-error>
                        @enderror
                    </td>
                    <td>
                        <button wire:click.debounce.1000ms="removeRegistration({{ $index }})"
                            @if (count($registrations) <= 1) disabled @endif class="btn btn-danger btn-sm">Remove <i
                                class="bx bx-trash"></i></button>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>

                <td colspan="3">
                    <button @click="$wire.addRegistration()" @if (count($registrations) >= 10) disabled @endif
                        class="btn btn-warning btn-sm">Add Row <i class="bx bx-plus"></i></button>
                </td>
            </tr>
        </tfoot>
        </tfoot>
    </table>



</div>


<!-- Do You Use Certified Seed -->
<div class="mb-3">
    <label class="form-label">Do You Use Certified Seed</label>
    <div class="@error('uses_certified_seed') border border-danger @enderror">
        <div class="form-check">
            <input class="form-check-input" type="radio" id="useCertifiedSeedYes" value="1"
                wire:model="uses_certified_seed">
            <label class="form-check-label" for="useCertifiedSeedYes">Yes</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" checked type="radio" id="useCertifiedSeedNo" value="0"
                wire:model="uses_certified_seed">
            <label class="form-check-label" for="useCertifiedSeedNo">No</label>
        </div>
    </div>
    @error('uses_certified_seed')
        <x-error>{{ $message }}</x-error>
    @enderror
</div>


<div class="mb-5 alert alert-warning" id="section-c" role="alert">
    <strong>SECTION B: RTC MARKETING </strong>
</div>
<!-- Market Segment (Multiple Responses) -->
<div class="mb-3">
    <label for="marketSegment" class="form-label">Market Segment (Multiple
        Responses)</label>
    <div x-data="{ market_segment: $wire.entangle('market_segment') }">
        <div class="form-check">
            <input class="form-check-input @error('market_segment') is-invalid @enderror" type="checkbox"
                value="Fresh" x-model="market_segment">
            <label class="form-check-label">Fresh</label>
        </div>

        <div class="form-check">
            <input class="form-check-input @error('market_segment') is-invalid @enderror" type="checkbox"
                value="Processed" x-model="market_segment">
            <label class="form-check-label">Processed</label>
        </div>

        <div class="form-check">
            <input class="form-check-input @error('market_segment') is-invalid @enderror" type="checkbox"
                value="Seed" x-model="market_segment">
            <label class="form-check-label">Seed</label>
        </div>

        <div class="form-check">
            <input class="form-check-input @error('market_segment') is-invalid @enderror" type="checkbox"
                value="Cuttings" x-model="market_segment">
            <label class="form-check-label">Cuttings</label>
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
    <div class="">
        <div class="form-check">
            <input class="form-check-input  @error('has_rtc_market_contract') is-invalid @enderror" type="radio"
                id="rtcMarketContractYes" value="1" x-model="has_rtc_market_contract">
            <label class="form-check-label" for="rtcMarketContractYes">Yes</label>
        </div>
        <div class="form-check">
            <input class="form-check-input  @error('has_rtc_market_contract') is-invalid @enderror" type="radio"
                checked id="rtcMarketContractNo" value="0" x-model="has_rtc_market_contract">
            <label class="form-check-label" for="rtcMarketContractNo">No</label>
        </div>
    </div>
    @error('has_rtc_market_contract')
        <x-error>{{ $message }}</x-error>
    @enderror
</div>

<!-- Total Volume of Production   (Metric Tonnes) -->
<div class="px-2 mb-3" x-data="{
    total_vol_production_previous_season: $wire.entangle('total_vol_production_previous_season'),
    total_vol_production_previous_season_produce: $wire.entangle('total_vol_production_previous_season_produce'),
    total_vol_production_previous_season_seed: $wire.entangle('total_vol_production_previous_season_seed'),
    total_vol_production_previous_season_cuttings: $wire.entangle('total_vol_production_previous_season_cuttings'),
    enterprise: $wire.entangle('location_data.enterprise'),
    bundle_multiplier: $wire.entangle('bundle_multiplier'),
    bundle_total: 0,
}"
    x-effect="

    if(enterprise !='Potato'){
   bundle_total = Number(total_vol_production_previous_season_seed || 0) * bundle_multiplier;
    }else{
    bundle_total = Number(total_vol_production_previous_season_seed || 0);
    }
    total_vol_production_previous_season = Number(total_vol_production_previous_season_produce || 0)
     + bundle_total +
      Number(total_vol_production_previous_season_cuttings || 0)">
    <label for="totalVolumeProduction" class="form-label">Total Volume of
        Production (Metric Tonnes)</label>


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
                <td>Seed (<span x-text="enterprise === 'Potato' ? 'MT': 'Bundles'"></span>)</td>
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
    total_production_value_previous_season_total: $wire.entangle('total_production_value_previous_season_total'),
    total_production_value_previous_season_cuttings_value: $wire.entangle('total_production_value_previous_season_cuttings_value'),
    total_production_value_previous_season_seed_value: $wire.entangle('total_production_value_previous_season_seed_value'),
    total_production_value_previous_season_seed_bundle: $wire.entangle('total_production_value_previous_season_seed_bundle'),
    total_production_value_previous_season_produce_value: $wire.entangle('total_production_value_previous_season_produce_value'),
    total_production_value_previous_season_seed_prevailing_price: $wire.entangle('total_production_value_previous_season_seed_prevailing_price'),
    total_production_value_previous_season_cuttings_prevailing_price: $wire.entangle('total_production_value_previous_season_cuttings_prevailing_price'),
    total_production_value_previous_season_produce_prevailing_price: $wire.entangle('total_production_value_previous_season_produce_prevailing_price'),
    total_production_value_previous_season_value: $wire.entangle('total_production_value_previous_season_value'),
    total_production_value_previous_season_rate: $wire.entangle('total_production_value_previous_season_rate'),
    bundle_multiplier: $wire.entangle('bundle_multiplier'),
    cuttingsTotal: 0,
    seedTotal: 0,
    produceTotal: 0,
    subTotal: 0,
    lastCalculatedTotal: null, // Track the last calculated total
    dateSet: null,
    seedInputType: 'metric tonnes',
    dateOfFollowUp: $wire.entangle('date_of_followup'),
    enterprise: $wire.entangle('location_data.enterprise'),

    getProduceTotal(value, price) {
        return (parseFloat(value) || 0) * (parseFloat(price) || 0);
    },
    getSeedTotal(value, price) {

        if (this.seedInputType === 'metric tonnes') {
            this.total_production_value_previous_season_seed_bundle = 0;
            return (parseFloat(value) || 0) * (parseFloat(price) || 0);
        }

        multiplier = this.bundle_multiplier;
        bundle = this.total_production_value_previous_season_seed_bundle;
        total_value = (parseFloat(bundle) || 0) * (parseFloat(multiplier) || 0);
        this.total_production_value_previous_season_seed_value = total_value;
        return total_value * (parseFloat(price) || 0);

    },
    getCuttingsTotal(value, price) {
        return (parseFloat(value) || 0) * (parseFloat(price) || 0);
    },
    getSubTotal() {
        return this.produceTotal + this.seedTotal + this.cuttingsTotal;
    },
    calculateTotal() {
        this.produceTotal = this.getProduceTotal(this.total_production_value_previous_season_produce_value, this.total_production_value_previous_season_produce_prevailing_price);
        this.seedTotal = this.getSeedTotal(this.total_production_value_previous_season_seed_value, this.total_production_value_previous_season_seed_prevailing_price);
        this.cuttingsTotal = this.getCuttingsTotal(this.total_production_value_previous_season_cuttings_value, this.total_production_value_previous_season_cuttings_prevailing_price);
        this.subTotal = this.getSubTotal();
        this.total_production_value_previous_season_value = this.subTotal;
        if (this.lastCalculatedTotal !== this.subTotal && this.dateOfFollowUp) {
            this.total_production_value_previous_season_total = null;
            this.total_production_value_previous_season_rate = 0;

        }
    },

    init() {
        this.$watch('enterprise', (v) => {


            if (v === 'Potato') {
                this.seedInputType = 'metric tonnes';
            } else {
                this.seedInputType = 'bundles';
            }

        })

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
}"
    x-effect="calculateTotal()">
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
                <th>Type</th>
                <th>Value (Metric Tonnes) </th>
                <th>Prevailing Market Price per Kg/Bundle</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>

            <tr>
                <td>Produce (MT)</td>
                <td class="bundle">
                    <select class="mb-2 form-select" disabled>
                        <option value="">--Select Type--</option>
                        <option value="bundles">Bundles</option>
                        <option selected value="mt">MT</option>
                    </select>

                </td>
                <td>
                    <input type="number" min="0" step="any" class="form-control"
                        x-model="total_production_value_previous_season_produce_value">
                </td>
                <td>
                    <input type="number" min="0" step="any" class="form-control"
                        x-model="total_production_value_previous_season_produce_prevailing_price">
                </td>
                <td>
                    <input type="number" readonly class="form-control bg-subtle-warning"
                        :value="getProduceTotal(total_production_value_previous_season_produce_value,
                            total_production_value_previous_season_produce_prevailing_price)">
                </td>
            </tr>


            <tr>
                <td>Seed </td>
                <td class="bundle">
                    <select class="mb-2 form-select" disabled x-model="seedInputType">

                        <option value="bundles">Bundles</option>
                        <option value="metric tonnes">MT</option>
                    </select>
                    <input type="number" min="0" step="any" class="form-control"
                        :readonly="seedInputType !== 'bundles'" x-show="seedInputType === 'bundles'"
                        x-model="total_production_value_previous_season_seed_bundle">

                </td>
                <td>
                    <input type="number" min="0" step="any" class="form-control"
                        :readonly="seedInputType === 'bundles'"
                        x-model="total_production_value_previous_season_seed_value">
                </td>
                <td>
                    <input type="number" min="0" step="any" class="form-control"
                        x-model="total_production_value_previous_season_seed_prevailing_price">
                </td>
                <td>
                    <input type="number" readonly class="form-control bg-subtle-warning"
                        :value="getSeedTotal(total_production_value_previous_season_seed_value,
                            total_production_value_previous_season_seed_prevailing_price)">
                </td>
            </tr>


            <tr>
                <td>Cuttings (MT)</td>
                <td class="bundle">
                    <select class="mb-2 form-select" disabled>
                        <option value="">--Select Type--</option>
                        <option value="bundles">Bundles</option>
                        <option selected value="mt">MT</option>
                    </select>

                </td>
                <td>
                    <input type="number" min="0" step="any" class="form-control"
                        x-model="total_production_value_previous_season_cuttings_value">
                </td>
                <td>
                    <input type="number" min="0" step="any" class="form-control"
                        x-model="total_production_value_previous_season_cuttings_prevailing_price">
                </td>
                <td>
                    <input type="number" readonly class="form-control bg-subtle-warning"
                        :value="getCuttingsTotal(total_production_value_previous_season_cuttings_value,
                            total_production_value_previous_season_cuttings_prevailing_price)">
                </td>
            </tr>

        </tbody>

        <tfoot>

            <tr>

                <td colspan="2">
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
                        class="form-control @error('total_production_value_previous_season_value') is-invalid @enderror bg-subtle-warning"
                        x-model="total_production_value_previous_season_value" :value="subTotal">
                </td>
                <td class="fw-bold">
                    <label for="">USD Rate ($)</label>
                    <input type="number" min="0" step="any" readonly
                        class="form-control @error('total_production_value_previous_season_rate') is-invalid @enderror bg-subtle-warning"
                        x-model='total_production_value_previous_season_rate'>
                </td>

                <td>
                    <label for="">Financial Value (USD)</label>
                    <input type="number" min="0" step="any" readonly
                        class="form-control @error('total_production_value_previous_season_total') is-invalid @enderror bg-subtle-warning"
                        x-model='total_production_value_previous_season_total'>
                    @error('total_production_value_previous_season_total')
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

<!-- Total Volume of Production   from Irrigation Farming (Metric Tonnes) -->

<div class="px-2 mb-3" x-data="{

    total_vol_irrigation_production_previous_season: $wire.entangle('total_vol_irrigation_production_previous_season'),
    total_vol_irrigation_production_previous_season_produce: $wire.entangle('total_vol_irrigation_production_previous_season_produce'),
    total_vol_irrigation_production_previous_season_seed: $wire.entangle('total_vol_irrigation_production_previous_season_seed'),
    total_vol_irrigation_production_previous_season_cuttings: $wire.entangle('total_vol_irrigation_production_previous_season_cuttings'),

}"
    x-effect="total_vol_irrigation_production_previous_season = Number(total_vol_irrigation_production_previous_season_produce || 0)
     + Number(total_vol_irrigation_production_previous_season_seed || 0) +
      Number(total_vol_irrigation_production_previous_season_cuttings || 0)">
    <label for="totalVolumeProduction" class="form-label">Total Volume of
        Production from Irrigation Farming (Metric
        Tonnes)</label>


    <table class="table table-bordered table-striped table-hover table-responsive">

        <tbody>
            <tr>
                <td>Produce (MT)</td>
                <td>
                    <input type="number" min="0" step="any"
                        class="form-control @error('total_vol_irrigation_production_previous_season_produce') is-invalid @enderror"
                        x-model='total_vol_irrigation_production_previous_season_produce'>
                </td>

            </tr>
            <tr>
                <td>Seed </td>
                <td>
                    <input type="number" min="0" step="any"
                        class="form-control @error('total_vol_irrigation_production_previous_season_seed') is-invalid @enderror"
                        x-model='total_vol_irrigation_production_previous_season_seed'>
                </td>
            </tr>

            <tr>
                <td>Cuttings (MT)</td>
                <td>
                    <input type="number" min="0" step="any"
                        class="form-control @error('total_vol_irrigation_production_previous_season_cuttings') is-invalid @enderror"
                        x-model='total_vol_irrigation_production_previous_season_cuttings'>
                </td>
            </tr>

        </tbody>
        <tfoot>
            <tr>
                <td class="fw-bold">Total</td>
                <td>
                    <input type="number" min="0" step="any" readonly
                        class="form-control @error('total_vol_irrigation_production_previous_season') is-invalid @enderror bg-subtle-warning"
                        x-model='total_vol_irrigation_production_previous_season'>

                    @error('total_vol_irrigation_production_previous_season')
                        <x-error>{{ $message }}</x-error>
                    @enderror
                </td>
            </tr>
        </tfoot>

    </table>

</div>

<!-- Total Value of Irrigation Production   (Financial Value-MWK) -->

<div class="px-2 mb-3" :class="{ 'border border-danger p-2': dateSet === false }" x-data="{
    total_irrigation_production_value_previous_season_total: $wire.entangle('total_irrigation_production_value_previous_season_total'),
    total_irrigation_production_value_previous_season_cuttings_value: $wire.entangle('total_irrigation_production_value_previous_season_cuttings_value'),
    total_irrigation_production_value_previous_season_seed_value: $wire.entangle('total_irrigation_production_value_previous_season_seed_value'),
    total_irrigation_production_value_previous_season_seed_bundle: $wire.entangle('total_irrigation_production_value_previous_season_seed_bundle'),
    total_irrigation_production_value_previous_season_produce_value: $wire.entangle('total_irrigation_production_value_previous_season_produce_value'),
    total_irrigation_production_value_previous_season_seed_prevailing_price: $wire.entangle('total_irrigation_production_value_previous_season_seed_prevailing_price'),
    total_irrigation_production_value_previous_season_cuttings_prevailing_price: $wire.entangle('total_irrigation_production_value_previous_season_cuttings_prevailing_price'),
    total_irrigation_production_value_previous_season_produce_prevailing_price: $wire.entangle('total_irrigation_production_value_previous_season_produce_prevailing_price'),
    total_irrigation_production_value_previous_season_value: $wire.entangle('total_irrigation_production_value_previous_season_value'),
    total_irrigation_production_value_previous_season_rate: $wire.entangle('total_irrigation_production_value_previous_season_rate'),
    bundle_multiplier_irrigation: $wire.entangle('bundle_multiplier_irrigation'),
    cuttingsTotal: 0,
    seedTotal: 0,
    produceTotal: 0,
    subTotal: 0,
    lastCalculatedTotal: null, // Track the last calculated total
    dateSet: null,
    seedInputType2: 'metric tonnes',
    enterprise: $wire.entangle('location_data.enterprise'),
    dateOfFollowUp: $wire.entangle('date_of_followup'),

    getProduceTotal(value, price) {
        return (parseFloat(value) || 0) * (parseFloat(price) || 0);
    },
    getSeedTotal(value, price) {

        if (this.seedInputType2 === 'metric tonnes') {
            this.total_irrigation_production_value_previous_season_seed_bundle = 0;
            return (parseFloat(value) || 0) * (parseFloat(price) || 0);
        }

        multiplier = this.bundle_multiplier_irrigation;
        bundle = this.total_irrigation_production_value_previous_season_seed_bundle;
        total_value = (parseFloat(bundle) || 0) * (parseFloat(multiplier) || 0);
        this.total_irrigation_production_value_previous_season_seed_value = total_value;
        return total_value * (parseFloat(price) || 0);

    },
    getCuttingsTotal(value, price) {
        return (parseFloat(value) || 0) * (parseFloat(price) || 0);
    },
    getSubTotal() {
        return this.produceTotal + this.seedTotal + this.cuttingsTotal;
    },
    clearAll() {

        this.produceTotal = 0;
        this.seedTotal = 0;
        this.cuttingsTotal = 0;
        this.subTotal = 0;
        this.seedInputType2 = 'metric tonnes';
        this.total_irrigation_production_value_previous_season_seed_bundle = 0;
        this.total_irrigation_production_value_previous_season_seed_value = null;
        this.total_irrigation_production_value_previous_season_seed_prevailing_price = null;
        this.total_irrigation_production_value_previous_season_cuttings_value = null;
        this.total_irrigation_production_value_previous_season_cuttings_prevailing_price = null;
        this.total_irrigation_production_value_previous_season_produce_value = null;
        this.total_irrigation_production_value_previous_season_produce_prevailing_price = null;

    },
    calculateTotal() {


        this.produceTotal = this.getProduceTotal(this.total_irrigation_production_value_previous_season_produce_value, this.total_irrigation_production_value_previous_season_produce_prevailing_price);
        this.seedTotal = this.getSeedTotal(this.total_irrigation_production_value_previous_season_seed_value, this.total_irrigation_production_value_previous_season_seed_prevailing_price);
        this.cuttingsTotal = this.getCuttingsTotal(this.total_irrigation_production_value_previous_season_cuttings_value, this.total_irrigation_production_value_previous_season_cuttings_prevailing_price);
        this.subTotal = this.getSubTotal();
        this.total_irrigation_production_value_previous_season_value = this.subTotal;
        if (this.lastCalculatedTotal !== this.subTotal && this.dateOfFollowUp) {
            this.total_production_value_previous_season_total = null;
            this.total_production_value_previous_season_rate = 0;

        }
    },



    calculate() {
        // Only proceed if the total has changed since last calculation
        let dateOfFollowUp = $wire.date_of_followup;
        this.dateSet = dateOfFollowUp ? true : false;

        if (this.lastCalculatedTotal !== this.subTotal && dateOfFollowUp) {
            this.$wire.exchangeRateCalculateIrrigation();
            this.lastCalculatedTotal = this.subTotal; // Update the last calculated total
        }
    },

    init() {
        this.$watch('enterprise', (v) => {
            if (v === 'Cassava') {
                this.clearAll();
                return;
            }

            if (v === 'Potato') {
                this.seedInputType2 = 'metric tonnes';
            } else {
                this.seedInputType2 = 'bundles';
            }

        })

    }

}"
    x-effect="calculateTotal()" x-show="enterprise !== 'Cassava'">


    <label for="totalValueProduction" class="my-3 form-label">Total value of irrigation production
        (financial value-MWK)
        *</label> <br>




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
                <th>Type </th>
                <th>Value (Metric Tonnes) </th>
                <th>Prevailing Market Price per Kg/Bundle</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>

            <tr>
                <td>Produce (MT)</td>
                <td class="bundle">
                    <select class="mb-2 form-select" disabled>
                        <option value="">--Select Type--</option>
                        <option value="bundles">Bundles</option>
                        <option selected value="mt">MT</option>
                    </select>

                </td>
                <td>
                    <input type="number" min="0" step="any" class="form-control"
                        x-model="total_irrigation_production_value_previous_season_produce_value">
                </td>
                <td>
                    <input type="number" min="0" step="any" class="form-control"
                        x-model="total_irrigation_production_value_previous_season_produce_prevailing_price">
                </td>
                <td>
                    <input type="number" readonly class="form-control bg-subtle-warning"
                        :value="getProduceTotal(total_irrigation_production_value_previous_season_produce_value,
                            total_irrigation_production_value_previous_season_produce_prevailing_price)">
                </td>
            </tr>


            <tr>
                <td>Seed </td>
                <td class="bundle">
                    <select class="mb-2 form-select" disabled x-model="seedInputType2">

                        <option value="bundles">Bundles</option>
                        <option value="metric tonnes">MT</option>
                    </select>
                    <input type="number" min="0" step="any" class="form-control"
                        :readonly="seedInputType2 !== 'bundles'" x-show="seedInputType2 === 'bundles'"
                        x-model="total_irrigation_production_value_previous_season_seed_bundle">

                </td>
                <td>
                    <input type="number" min="0" step="any" class="form-control"
                        :readonly="seedInputType2 === 'bundles'"
                        x-model="total_irrigation_production_value_previous_season_seed_value">
                </td>
                <td>
                    <input type="number" min="0" step="any" class="form-control"
                        x-model="total_irrigation_production_value_previous_season_seed_prevailing_price">
                </td>
                <td>
                    <input type="number" readonly class="form-control bg-subtle-warning"
                        :value="getSeedTotal(total_irrigation_production_value_previous_season_seed_value,
                            total_irrigation_production_value_previous_season_seed_prevailing_price)">
                </td>
            </tr>


            <tr>
                <td>Cuttings (MT)</td>
                <td class="bundle">
                    <select class="mb-2 form-select" disabled>
                        <option value="">--Select Type--</option>
                        <option value="bundles">Bundles</option>
                        <option selected value="mt">MT</option>
                    </select>

                </td>
                <td>
                    <input type="number" min="0" step="any" class="form-control"
                        x-model="total_irrigation_production_value_previous_season_cuttings_value">
                </td>
                <td>
                    <input type="number" min="0" step="any" class="form-control"
                        x-model="total_irrigation_production_value_previous_season_cuttings_prevailing_price">
                </td>
                <td>
                    <input type="number" readonly class="form-control bg-subtle-warning"
                        :value="getCuttingsTotal(total_irrigation_production_value_previous_season_cuttings_value,
                            total_irrigation_production_value_previous_season_cuttings_prevailing_price)">
                </td>
            </tr>

        </tbody>

        <tfoot>

            <tr>

                <td colspan="2">
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
                        class="form-control @error('total_irrigation_production_value_previous_season_value') is-invalid @enderror bg-subtle-warning"
                        x-model="total_irrigation_production_value_previous_season_value" :value="subTotal">
                </td>
                <td class="fw-bold">
                    <label for="">USD Rate ($)</label>
                    <input type="number" min="0" step="any" readonly
                        class="form-control @error('total_irrigation_production_value_previous_season_rate') is-invalid @enderror bg-subtle-warning"
                        x-model='total_irrigation_production_value_previous_season_rate'>
                </td>

                <td>
                    <label for="">Financial Value (USD)</label>
                    <input type="number" min="0" step="any" readonly
                        class="form-control @error('total_irrigation_production_value_previous_season_total') is-invalid @enderror bg-subtle-warning"
                        x-model='total_irrigation_production_value_previous_season_total'>
                    @error('total_irrigation_production_value_previous_season_total')
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
            <input class="form-check-input" checked type="radio" id="sellToDomesticMarketsNo" value="0"
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
            <input class="form-check-input" checked type="radio" id="sellToInternationalMarketsNo" value="0"
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
            <input class="form-check-input" checked type="radio" id="sellThroughMarketInfoNo" value="0"
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
    total_vol_aggregation_center_sales: $wire.entangle('total_vol_aggregation_center_sales'),


}" x-init="$watch('sells_to_aggregation_centers', (v) => {

    if (v != 1) {

        $wire.resetValues('aggregation_center_sales');
        total_vol_aggregation_center_sales = 0
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
                <input class="form-check-input" checked type="radio" id="aggregationCenterResponseNo"
                    value="0" wire:model='sells_to_aggregation_centers'>
                <label class="form-check-label">No</label>
            </div>
        </div>

        @error('aggregation_centers')
            <x-error>{{ $message }}</x-error>
        @enderror
    </div>



    <!-- Total Volume of RTC Sold Through Aggregation Centers   (Metric Tonnes) -->
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


<div class="mb-3" x-data="{
    total_vol_aggregation_center_sales: $wire.entangle('total_vol_aggregation_center_sales'),
}">
    <label for="" class="form-label">Total Volume
        of RTC Sold Through Aggregation Centers (Metric
        Tonnes)</label>
    <input type="number" min="0" step="any"
        class="form-control @error('total_vol_aggregation_center_sales') is-invalid @enderror"
        x-model='total_vol_aggregation_center_sales' id="" aria-describedby="helpId" placeholder="" />
    @error('total_vol_aggregation_center_sales')
        <x-error>{{ $message }}</x-error>
    @enderror
</div>
