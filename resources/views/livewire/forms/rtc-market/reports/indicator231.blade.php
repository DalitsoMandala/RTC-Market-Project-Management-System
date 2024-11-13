<div x-data="{
    cassava: $wire.entangle('cassava'),
    potato: $wire.entangle('potato'),
    sweetPotato: $wire.entangle('sweet_potato'),
    pos: $wire.entangle('pos'),
    smes: $wire.entangle('smes'),
    largeScaleCommercialFarmers: $wire.entangle('large_scale_commercial_farmers'),
    totalPercentage: $wire.entangle('total_percentage'),
    annualValue: $wire.entangle('annual_value'),
    baselineValue: $wire.entangle('baseline'),
    basic: $wire.entangle('basic'),
    certified: $wire.entangle('certified'),
    updateTotalPercentage() {
        let totalValue =
            (isNaN(parseFloat(this.basic)) ? 0 : parseFloat(this.basic)) +
            (isNaN(parseFloat(this.certified)) ? 0 : parseFloat(this.certified));

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
    $watch('pos', () => updateTotalPercentage());
    $watch('smes', () => updateTotalPercentage());
    $watch('largeScaleCommercialFarmers', () => updateTotalPercentage());
    $watch('basic', () => updateTotalPercentage());
    $watch('certified', () => updateTotalPercentage());
        $watch('baselineValue', (v) => { updateFinancialValue() });
}">

    <x-alerts />
    <x-required-notice />

    <form wire:submit.prevent="save">
        <div class="row">
            <!-- Total Percentage -->

            <div class="col">
                <label for="projectYear" class="form-label">Project year</label>
                <input type="number" readonly id="project_year" wire:model="yearNumber"
                    class="form-control @error('project_year') is-invalid @enderror" min="0">
            </div>
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
                    <label for="baseline" class="form-label">Previous Value</label>
                    <input type="number" id="baseline" x-model="baselineValue"
                        class="form-control         @error('baseline') is-invalid @enderror">
                    @error('baseline')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

            </div>
        </div>
        <h5>Crop Type</h5>
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


        <h5>Type</h5>
        <!-- POs -->
        <div class="mb-3">
            <label for="pos" class="form-label">POs (Producer Organizations)</label>
            <input type="number" id="pos" x-model="pos" class="form-control @error('pos') is-invalid @enderror">
            @error('pos')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <!-- SMEs -->
        <div class="mb-3">
            <label for="smes" class="form-label">SMEs</label>
            <input type="number" id="smes" x-model="smes" class="form-control @error('smes') is-invalid @enderror">
            @error('smes')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <!-- Large Scale Commercial Farmers -->
        <div class="mb-3">
            <label for="large_scale_commercial_farmers" class="form-label">Large Scale Commercial Farmers</label>
            <input type="number" id="large_scale_commercial_farmers" x-model="largeScaleCommercialFarmers"
                class="form-control @error('large_scale_commercial_farmers') is-invalid @enderror">
            @error('large_scale_commercial_farmers')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <h5>Type of Seed</h5>
        <!-- Basic -->
        <div class="mb-3">
            <label for="basic" class="form-label">Basic</label>
            <input type="number" id="basic" x-model="basic" class="form-control @error('basic') is-invalid @enderror">
            @error('basic')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <!-- Certified -->
        <div class="mb-3">
            <label for="certified" class="form-label">Certified</label>
            <input type="number" id="certified" x-model="certified"
                class="form-control @error('certified') is-invalid @enderror">
            @error('certified')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="d-grid col-12 justify-content-center">
            <button class="btn btn-warning " @click="window.scrollTo({ top: 0, behavior: 'smooth' })" type="submit">
                Submit
            </button>
        </div>
    </form>
</div>