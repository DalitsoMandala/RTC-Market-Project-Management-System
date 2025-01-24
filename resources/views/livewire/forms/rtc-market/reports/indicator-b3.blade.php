<div x-data="{
    formalCassava: $wire.entangle('formal_cassava'),
    formalPotato: $wire.entangle('formal_potato'),
    formalSweetPotato: $wire.entangle('formal_sweet_potato'),
    formalImports: $wire.entangle('formal_imports'),
    financialValue: $wire.entangle('financial_value'),
    annualValue: $wire.entangle('annual_value'),
    baselineValue: $wire.entangle('baseline'),
    totalPercentage: $wire.entangle('total_percentage'),

    updateFinancialValue() {
        this.financialValue =
            (isNaN(parseFloat(this.formalCassava)) ? 0 : parseFloat(this.formalCassava)) +
            (isNaN(parseFloat(this.formalPotato)) ? 0 : parseFloat(this.formalPotato)) +
            (isNaN(parseFloat(this.formalSweetPotato)) ? 0 : parseFloat(this.formalSweetPotato));


        this.annualValue = this.financialValue;

        if (this.annualValue === 0) {
            this.totalPercentage = 0;
            return;
        }

        const sub = (this.annualValue - this.baselineValue ?? 0) / this.annualValue;
        const percentage = sub * 100;

        this.totalPercentage = Number(percentage.toFixed(2));
    }
}" x-init="() => {
    $watch('formalCassava', () => updateFinancialValue());
    $watch('formalPotato', () => updateFinancialValue());
    $watch('formalSweetPotato', () => updateFinancialValue());
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

            <div class="col">
                <div class="mb-3">
                    <label for="financial_value" class="form-label">Financial Value ($)</label>
                    <input type="number" id="financial_value" x-model="financialValue"
                        class="form-control @error('financial_value') is-invalid @enderror" readonly>
                    @error('financial_value')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>



        <div class="mb-3">
            <label for="volume" class="form-label">Volume (Metric Tonnes)</label>
            <input type="number" id="volume" wire:model="volume"
                class="form-control @error('volume') is-invalid @enderror">
            @error('volume')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <h5>Formal Data</h5>
        <div class="mb-3">
            <label for="formal_cassava" class="form-label">Cassava</label>
            <input type="number" id="formal_cassava" x-model="formalCassava"
                class="form-control @error('formal_cassava') is-invalid @enderror">
            @error('formal_cassava')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="formal_potato" class="form-label">Potato</label>
            <input type="number" id="formal_potato" x-model="formalPotato"
                class="form-control @error('formal_potato') is-invalid @enderror">
            @error('formal_potato')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="formal_sweet_potato" class="form-label">Sweet Potato</label>
            <input type="number" id="formal_sweet_potato" x-model="formalSweetPotato"
                class="form-control @error('formal_sweet_potato') is-invalid @enderror">
            @error('formal_sweet_potato')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="formal_sweet_potato" class="form-label">Total Formal Exports</label>
            <input type="number" readonly
                x-model=" (isNaN(parseFloat(formalCassava)) ? 0 : parseFloat(formalCassava)) +
            (isNaN(parseFloat(formalPotato)) ? 0 : parseFloat(formalPotato)) +
            (isNaN(parseFloat(formalSweetPotato)) ? 0 : parseFloat(formalSweetPotato))"
                class="form-control">

        </div>

        {{-- <div class="mb-3">
            <label for="formal_imports" class="form-label">Formal Imports</label>
            <input type="number" id="formal_imports" x-model="formalImports"
                class="form-control @error('formal_imports') is-invalid @enderror">
            @error('formal_imports')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div> --}}

        <div class="d-grid col-12 justify-content-center">
            <button class="btn btn-warning " @click="window.scrollTo({ top: 0, behavior: 'smooth' })" type="submit">
                Submit Data
            </button>
        </div>
    </form>
</div>
