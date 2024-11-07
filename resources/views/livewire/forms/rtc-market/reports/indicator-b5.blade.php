<div x-data="{
    cassava: $wire.entangle('cassava'),
    potato: $wire.entangle('potato'),
    sweetPotato: $wire.entangle('sweet_potato'),
    certifiedSeedProduce: $wire.entangle('certified_seed_produce'),
    valueAddedRTCProducts: $wire.entangle('value_added_rtc_products'),
    totalPercentage: $wire.entangle('total_percentage'),
    annualValue: $wire.entangle('annual_value'),
    baselineValue: $wire.entangle('baseline'),

    updateTotalPercentage() {
        let totalValue =
            (isNaN(parseFloat(this.cassava)) ? 0 : parseFloat(this.cassava)) +
            (isNaN(parseFloat(this.potato)) ? 0 : parseFloat(this.potato)) +
            (isNaN(parseFloat(this.sweetPotato)) ? 0 : parseFloat(this.sweetPotato)) +
            (isNaN(parseFloat(this.certifiedSeedProduce)) ? 0 : parseFloat(this.certifiedSeedProduce)) +
            (isNaN(parseFloat(this.valueAddedRTCProducts)) ? 0 : parseFloat(this.valueAddedRTCProducts));

        this.annualValue = totalValue; // Set annual value as sum of inputs

        if (this.annualValue === 0) {
            this.totalPercentage = 0;
            return;
        }

        // Calculate total percentage based on annual and baseline values
             const sub = (this.annualValue - this.baselineValue ?? 0) / this.annualValue;
        let percentage = sub * 100;

        this.totalPercentage = Number(percentage.toFixed(2)); // Rounded to 2 decimal places
    }
}" x-init="() => {
    $watch('cassava', () => updateTotalPercentage());
    $watch('potato', () => updateTotalPercentage());
    $watch('sweetPotato', () => updateTotalPercentage());
    $watch('certifiedSeedProduce', () => updateTotalPercentage());
    $watch('valueAddedRTCProducts', () => updateTotalPercentage());
        $watch('baselineValue', (v) => { updateFinancialValue() });
}">

    <x-alerts />
    <x-required-notice />

    <form wire:submit.prevent="save">
        <div class="row">
            <div class="col">
                <label for="total_percentage" class="form-label">Total (% Percentage)</label>
                <input type="number" readonly id="total_percentage" wire:model="total_percentage"
                    class="form-control @error('total_percentage') is-invalid @enderror" min="0">
                @error('total_percentage')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col">
                <div class="mb-3">
                    <label for="annual_value" class="form-label">Annual Value</label>
                    <input type="number" readonly id="annual_value" x-model="annualValue" class="form-control" readonly>
                </div>
            </div>
            <div class="col">
                <div class="mb-3">
                    <label for="baseline" class="form-label">Baseline</label>
                    <input type="number" readonly id="baseline" x-model="baselineValue" class="form-control" readonly>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="cassava" class="form-label">Cassava</label>
            <input type="number" id="cassava" x-model="cassava"
                class="form-control @error('cassava') is-invalid @enderror">
            @error('cassava')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="potato" class="form-label">Potato</label>
            <input type="number" id="potato" x-model="potato"
                class="form-control @error('potato') is-invalid @enderror">
            @error('potato')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="sweet_potato" class="form-label">Sweet Potato</label>
            <input type="number" id="sweet_potato" x-model="sweetPotato"
                class="form-control @error('sweet_potato') is-invalid @enderror">
            @error('sweet_potato')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="certified_seed_produce" class="form-label">Certified Seed Produce</label>
            <input type="number" id="certified_seed_produce" x-model="certifiedSeedProduce"
                class="form-control @error('certified_seed_produce') is-invalid @enderror">
            @error('certified_seed_produce')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="value_added_rtc_products" class="form-label">Value Added RTC Products</label>
            <input type="number" id="value_added_rtc_products" x-model="valueAddedRTCProducts"
                class="form-control @error('value_added_rtc_products') is-invalid @enderror">
            @error('value_added_rtc_products')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="d-grid col-12 justify-content-center">
            <button class="btn btn-primary " @click="window.scrollTo({ top: 0, behavior: 'smooth' })" type="submit">
                Submit
            </button>
        </div>
    </form>
</div>
