<div x-data="{
    improvedRTCVariety: $wire.entangle('improved_rtc_variety'),
    seedProduction: $wire.entangle('seed_production'),
    storage: $wire.entangle('storage'),
    agronomicProduction: $wire.entangle('agronomic_production'),
    postHarvestProcessing: $wire.entangle('post_harvest_processing'),
    cassava: $wire.entangle('cassava'),
    potato: $wire.entangle('potato'),
    sweetPotato: $wire.entangle('sweet_potato'),
    totalPercentage: $wire.entangle('total_percentage'),
    annualValue: $wire.entangle('annual_value'),
    baselineValue: $wire.entangle('baseline'),

    updateTotalPercentage() {
        let totalValue =
            (isNaN(parseFloat(this.improvedRTCVariety)) ? 0 : parseFloat(this.improvedRTCVariety)) +
            (isNaN(parseFloat(this.seedProduction)) ? 0 : parseFloat(this.seedProduction)) +
            (isNaN(parseFloat(this.storage)) ? 0 : parseFloat(this.storage)) +
            (isNaN(parseFloat(this.agronomicProduction)) ? 0 : parseFloat(this.agronomicProduction)) +
            (isNaN(parseFloat(this.postHarvestProcessing)) ? 0 : parseFloat(this.postHarvestProcessing))
            // (isNaN(parseFloat(this.cassava)) ? 0 : parseFloat(this.cassava)) +
            // (isNaN(parseFloat(this.potato)) ? 0 : parseFloat(this.potato)) +
            // (isNaN(parseFloat(this.sweetPotato)) ? 0 : parseFloat(this.sweetPotato)
            ;

        this.annualValue = totalValue;

        if (this.annualValue === 0) {
            this.totalPercentage = 0;
            return;
        }

        // Calculate total percentage based on annual and baseline values
             const sub = (this.annualValue - this.baselineValue ?? 0) / this.annualValue;
        let percentage = sub * 100;

        this.totalPercentage = Number(percentage.toFixed(2));
    }
}" x-init="() => {
    $watch('improvedRTCVariety', () => updateTotalPercentage());
    $watch('seedProduction', () => updateTotalPercentage());
    $watch('storage', () => updateTotalPercentage());
    $watch('agronomicProduction', () => updateTotalPercentage());
    $watch('postHarvestProcessing', () => updateTotalPercentage());
    $watch('cassava', () => updateTotalPercentage());
    $watch('potato', () => updateTotalPercentage());
    $watch('sweetPotato', () => updateTotalPercentage());
        $watch('baselineValue', (v) => { updateFinancialValue() });
}">

    <x-alerts />
    <x-required-notice />

    <form wire:submit.prevent="save">
        <div class="row">

            <div class="col">
                <label for="projectYear" class="form-label">Project year</label>
                <input type="number" readonly id="project_year" wire:model="yearNumber"
                    class="form-control @error('project_year') is-invalid @enderror" min="0">
            </div>
            <!-- Total Percentage -->
            <div class="col">
                <label for="total_percentage" class="form-label">Total (% Percentage)</label>
                <input type="number" readonly id="total_percentage" wire:model="total_percentage"
                    class="form-control @error('total_percentage') is-invalid @enderror" min="0">
                @error('total_percentage')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <!-- Annual Value -->
            <div class="col">
                <div class="mb-3">
                    <label for="annual_value" class="form-label">Annual Value</label>
                    <input type="number" readonly id="annual_value" x-model="annualValue" class="form-control">
                </div>
            </div>
            <!-- Baseline Value -->
            <div class="col">
                <div class="mb-3">
                    <label for="baseline" class="form-label">Baseline</label>
                    <input type="number" readonly id="baseline" x-model="baselineValue" class="form-control">
                </div>
            </div>


        </div>

        <h5>Type of technology</h5>
        <!-- Improved RTC Variety -->
        <div class="mb-3">
            <label for="improved_rtc_variety" class="form-label">Improved RTC Variety</label>
            <input type="number" id="improved_rtc_variety" x-model="improvedRTCVariety"
                class="form-control @error('improved_rtc_variety') is-invalid @enderror">
            @error('improved_rtc_variety')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <!-- Seed Production -->
        <div class="mb-3">
            <label for="seed_production" class="form-label">Seed Production</label>
            <input type="number" id="seed_production" x-model="seedProduction"
                class="form-control @error('seed_production') is-invalid @enderror">
            @error('seed_production')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <!-- Storage -->
        <div class="mb-3">
            <label for="storage" class="form-label">Storage</label>
            <input type="number" id="storage" x-model="storage"
                class="form-control @error('storage') is-invalid @enderror">
            @error('storage')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <!-- Agronomic Production -->
        <div class="mb-3">
            <label for="agronomic_production" class="form-label">Agronomic Production</label>
            <input type="number" id="agronomic_production" x-model="agronomicProduction"
                class="form-control @error('agronomic_production') is-invalid @enderror">
            @error('agronomic_production')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <!-- Post-Harvest Processing -->
        <div class="mb-3">
            <label for="post_harvest_processing" class="form-label">Post-Harvest Processing</label>
            <input type="number" id="post_harvest_processing" x-model="postHarvestProcessing"
                class="form-control @error('post_harvest_processing') is-invalid @enderror">
            @error('post_harvest_processing')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <h5>Type of Crop</h5>
        <!-- Cassava -->
        <div class="mb-3">
            <label for="cassava" class="form-label">Cassava</label>
            <input type="number" id="cassava" x-model="cassava"
                class="form-control @error('cassava') is-invalid @enderror">
            @error('cassava')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <!-- Potato -->
        <div class="mb-3">
            <label for="potato" class="form-label">Potato</label>
            <input type="number" id="potato" x-model="potato"
                class="form-control @error('potato') is-invalid @enderror">
            @error('potato')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <!-- Sweet Potato -->
        <div class="mb-3">
            <label for="sweet_potato" class="form-label">Sweet Potato</label>
            <input type="number" id="sweet_potato" x-model="sweetPotato"
                class="form-control @error('sweet_potato') is-invalid @enderror">
            @error('sweet_potato')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="d-grid col-12 justify-content-center">
            <button class="btn btn-primary " @click="window.scrollTo({ top: 0, behavior: 'smooth' })" type="submit">
                Submit
            </button>
        </div>
    </form>
</div>