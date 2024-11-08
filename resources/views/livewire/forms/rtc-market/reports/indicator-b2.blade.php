<div x-data="{
    formalCassava: $wire.entangle('formal_exports.cassava'),
    formalPotato: $wire.entangle('formal_exports.potato'),
    formalSweetPotato: $wire.entangle('formal_exports.sweet_potato'),
    informalCassava: $wire.entangle('informal_exports.cassava'),
    informalPotato: $wire.entangle('informal_exports.potato'),
    informalSweetPotato: $wire.entangle('informal_exports.sweet_potato'),
    financialValue: $wire.entangle('financial_value'),
    annualValue: $wire.entangle('annual_value'),
    baselineValue: $wire.entangle('baseline'),
    totalPercentage: $wire.entangle('total_percentage'),
    updateFinancialValue() {
        this.financialValue =
            (isNaN(parseFloat(this.formalCassava)) ? 0 : parseFloat(this.formalCassava)) +
            (isNaN(parseFloat(this.formalPotato)) ? 0 : parseFloat(this.formalPotato)) +
            (isNaN(parseFloat(this.formalSweetPotato)) ? 0 : parseFloat(this.formalSweetPotato)) +
            (isNaN(parseFloat(this.informalCassava)) ? 0 : parseFloat(this.informalCassava)) +
            (isNaN(parseFloat(this.informalPotato)) ? 0 : parseFloat(this.informalPotato)) +
            (isNaN(parseFloat(this.informalSweetPotato)) ? 0 : parseFloat(this.informalSweetPotato));
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
    $watch('formalCassava', (v) => { updateFinancialValue() });
    $watch('formalPotato', (v) => { updateFinancialValue() });
    $watch('formalSweetPotato', (v) => { updateFinancialValue() });
    $watch('informalCassava', (v) => { updateFinancialValue() });
    $watch('informalPotato', (v) => { updateFinancialValue() });
    $watch('informalSweetPotato', (v) => { updateFinancialValue() });
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
        </div>

        <div class="mb-3">
            <label for="financial_value" class="form-label">Financial Value ($)</label>
            <input type="number" id="financial_value" x-model="financialValue"
                class="form-control @error('financial_value') is-invalid @enderror" readonly>
            @error('financial_value')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label for="volume" class="form-label">Volume (Metric Tonnes)</label>
            <input type="number" id="volume" wire:model="volume"
                class="form-control @error('volume') is-invalid @enderror">
            @error('volume')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <h5>Formal Exports</h5>
        <div class="mb-3">
            <label for="formal_cassava" class="form-label">Cassava</label>
            <input type="number" id="formal_cassava" x-model="formalCassava"
                class="form-control @error('formal_exports.cassava') is-invalid @enderror">
            @error('formal_exports.cassava')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="formal_potato" class="form-label">Potato</label>
            <input type="number" id="formal_potato" x-model="formalPotato"
                class="form-control @error('formal_exports.potato') is-invalid @enderror">
            @error('formal_exports.potato')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="formal_sweet_potato" class="form-label">Sweet Potato</label>
            <input type="number" id="formal_sweet_potato" x-model="formalSweetPotato"
                class="form-control @error('formal_exports.sweet_potato') is-invalid @enderror">
            @error('formal_exports.sweet_potato')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <h5>Informal Exports</h5>
        <div class="mb-3">
            <label for="informal_cassava" class="form-label">Cassava</label>
            <input type="number" id="informal_cassava" x-model="informalCassava"
                class="form-control @error('informal_exports.cassava') is-invalid @enderror">
            @error('informal_exports.cassava')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="informal_potato" class="form-label">Potato</label>
            <input type="number" id="informal_potato" x-model="informalPotato"
                class="form-control @error('informal_exports.potato') is-invalid @enderror">
            @error('informal_exports.potato')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="informal_sweet_potato" class="form-label">Sweet Potato</label>
            <input type="number" id="informal_sweet_potato" x-model="informalSweetPotato"
                class="form-control @error('informal_exports.sweet_potato') is-invalid @enderror">
            @error('informal_exports.sweet_potato')
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