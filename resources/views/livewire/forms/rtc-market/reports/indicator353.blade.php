<div x-data="{
    total: $wire.entangle('total'),


    annualValue: $wire.entangle('annual_value'),
    baselineValue: $wire.entangle('baseline'),
    totalPercentage: $wire.entangle('total_percentage'),
    updateFinancialValue() {
        this.financialValue =
            (isNaN(parseFloat(this.total)) ? 0 : parseFloat(this.total)) ;


        this.annualValue = this.financialValue;
        if (this.annualValue === 0) {
            this.totalPercentage = 0;

            return;

        }
        sub = (this.annualValue - this.baselineValue ?? 0) / this.annualValue;
        percentage = sub * 100;



        this.totalPercentage = Number(percentage.toFixed(2));

    }
}" x-init="() => {
    $watch('total', (v) => { updateFinancialValue() });

    $watch('baselineValue', (v) => { updateFinancialValue() });
}">
    <x-alerts />

    <x-required-notice />



    <form wire:submit.prevent="save">


        <div class="row">

            <div class="col d-none">
                <label for="projectYear" class="form-label">Project year</label>
                <input type="number" readonly id="project_year" wire:model="yearNumber"
                    class="form-control @error('project_year') is-invalid @enderror" min="0">
            </div>
            <div class="col">
                <div class="mb-3">
                    <label for="total_percentage" class="form-label">Total (% Percentage)</label>
                    <input type="number" readonly id="total_percentage" wire:model="total_percentage"
                        class="form-control @error('total_percentage') is-invalid @enderror">
                    @error('total_percentage')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="col">
                <div class="mb-3">
                    <label for="annual_value" class="form-label">Annual Value</label>
                    <input type="number" id="annual_value" x-model="annualValue" class="form-control" readonly>
                </div>
            </div>
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

        <div class="mb-3">
            <label for="total" class="form-label">Total</label>
            <input type="number" id="total" x-model="total"
                class="form-control @error('total') is-invalid @enderror" >
            @error('total')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="d-grid col-12 justify-content-center">
            <button class="btn btn-warning " @click="window.scrollTo({ top: 0, behavior: 'smooth' })" type="submit">
                Submit data
            </button>
        </div>
    </form>
</div>
