<div x-data="{
    total: $wire.entangle('total'),
    baselineValue: $wire.entangle('baseline'),
    totalPercentage: $wire.entangle('total_percentage'),
    annualValue: $wire.entangle('annual_value'),
    updateTotalPercentage() {
        let totalValue =
            (isNaN(parseFloat(this.total)) ? 0 : parseFloat(this.total));

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
    $watch('total', () => updateTotalPercentage());
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
        <h5>Total Value</h5>
        <!-- Cassava -->
        <div class="mb-3">
            <label for="total" class="form-label">Total</label>
            <input type="number" id="cassava" x-model="total"
                class="form-control @error('total') is-invalid @enderror">
            @error('total')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>


        <!-- Submit Button -->
        <div class="d-grid col-12 justify-content-center">
            <button class="btn btn-warning " @click="window.scrollTo({ top: 0, behavior: 'smooth' })" type="submit">
                Submit data
            </button>
        </div>
    </form>
</div>
