<div x-data="{
    /*
    |--------------------------------------------------------------------------
    | FARMERS / PROCESSORS / TRADERS
    |--------------------------------------------------------------------------
    */

    farmers: $wire.entangle('farmers'),
    processors: $wire.entangle('processors'),
    traders: $wire.entangle('traders'),

    /*
    |--------------------------------------------------------------------------
    | CROPS
    |--------------------------------------------------------------------------
    | These are manually entered values.
    | DO NOT calculate anything from them.
    */

    cassava: $wire.entangle('cassava'),
    potato: $wire.entangle('potato'),
    sweetPotato: $wire.entangle('sweet_potato'),

    /*
    |--------------------------------------------------------------------------
    | BASELINE
    |--------------------------------------------------------------------------
    */

    baselineFarmers: $wire.entangle('baseline_farmers'),
    baselineProcessors: $wire.entangle('baseline_processors'),
    baselineTraders: $wire.entangle('baseline_traders'),

    /*
    |--------------------------------------------------------------------------
    | CALCULATED VALUES
    |--------------------------------------------------------------------------
    */

    income: $wire.entangle('income'),
    rolledBaseline: $wire.entangle('rolled_baseline'),
    total: $wire.entangle('total'),

    number(value) {
        const parsed = parseFloat(value);

        return isNaN(parsed) ? 0 : parsed;
    },

    updateCalculations() {

        /*
        |--------------------------------------------------------------------------
        | Income
        |--------------------------------------------------------------------------
        */

        const beneficiaryIncome =
            this.number(this.farmers) +
            this.number(this.processors) +
            this.number(this.traders);

        /*
        |--------------------------------------------------------------------------
        | Only calculate Income when Farmers,
        | Processors or Traders are being used.
        |
        | Crops are NOT included here.
        |--------------------------------------------------------------------------
        */

        if (
            this.number(this.farmers) > 0 ||
            this.number(this.processors) > 0 ||
            this.number(this.traders) > 0
        ) {
            this.income = beneficiaryIncome;
        }

        /*
        |--------------------------------------------------------------------------
        | Rolled Baseline
        |--------------------------------------------------------------------------
        */

        this.rolledBaseline =
            this.number(this.baselineFarmers) +
            this.number(this.baselineProcessors) +
            this.number(this.baselineTraders);

        /*
        |--------------------------------------------------------------------------
        | Total
        |--------------------------------------------------------------------------
        */
        const incomeVal = this.number(this.income);
        const baselineVal = this.number(this.rolledBaseline);

        if (baselineVal > 0) {
            const percentageChange = ((incomeVal - baselineVal) / baselineVal) * 100;
            this.total = Number(percentageChange.toFixed(2));
        } else {
            this.total = 0;
        }

    }
}" x-init="$watch('farmers', () => updateCalculations());
$watch('processors', () => updateCalculations());
$watch('traders', () => updateCalculations());

$watch('baselineFarmers', () => updateCalculations());
$watch('baselineProcessors', () => updateCalculations());
$watch('baselineTraders', () => updateCalculations());

updateCalculations();">
    <x-alerts />

    <x-required-notice />



    <form wire:submit.prevent="save">

        <div class="row">

            <!-- TOTAL -->

            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">
                        Total(%)
                    </label>

                    <input type="number" x-model="total" readonly class="form-control">
                </div>
            </div>


            <!-- INCOME -->

            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">
                        Income ($)
                    </label>

                    <input type="number" x-model="income" readonly class="form-control">
                </div>
            </div>


            <!-- ROLLED BASELINE -->

            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">
                        Rolled Baseline
                    </label>

                    <input type="number" x-model="rolledBaseline" readonly class="form-control">
                </div>
            </div>

        </div>


        <hr>


        <h5>Income</h5>

        <div class="row">

            <!-- FARMERS -->

            <div class="col-md-4">
                <div class="mb-3">

                    <label class="form-label">
                        Farmers
                    </label>

                    <input type="number" x-model="farmers" class="form-control @error('farmers') is-invalid @enderror">

                    @error('farmers')
                        <span class="text-danger">
                            {{ $message }}
                        </span>
                    @enderror

                </div>
            </div>


            <!-- PROCESSORS -->

            <div class="col-md-4">
                <div class="mb-3">

                    <label class="form-label">
                        Processors
                    </label>

                    <input type="number" x-model="processors"
                        class="form-control @error('processors') is-invalid @enderror">

                    @error('processors')
                        <span class="text-danger">
                            {{ $message }}
                        </span>
                    @enderror

                </div>
            </div>


            <!-- TRADERS -->

            <div class="col-md-4">
                <div class="mb-3">

                    <label class="form-label">
                        Traders
                    </label>

                    <input type="number" x-model="traders" class="form-control @error('traders') is-invalid @enderror">

                    @error('traders')
                        <span class="text-danger">
                            {{ $message }}
                        </span>
                    @enderror

                </div>
            </div>

        </div>


        <hr>


        <h5>Baseline</h5>

        <div class="row">

            <!-- BASELINE FARMERS -->

            <div class="col-md-4">
                <div class="mb-3">

                    <label class="form-label">
                        Baseline Farmers
                    </label>

                    <input type="number" x-model="baselineFarmers"
                        class="form-control @error('baseline_farmers') is-invalid @enderror">

                    @error('baseline_farmers')
                        <span class="text-danger">
                            {{ $message }}
                        </span>
                    @enderror

                </div>
            </div>


            <!-- BASELINE PROCESSORS -->

            <div class="col-md-4">
                <div class="mb-3">

                    <label class="form-label">
                        Baseline Processors
                    </label>

                    <input type="number" x-model="baselineProcessors"
                        class="form-control @error('baseline_processors') is-invalid @enderror">

                    @error('baseline_processors')
                        <span class="text-danger">
                            {{ $message }}
                        </span>
                    @enderror

                </div>
            </div>


            <!-- BASELINE TRADERS -->

            <div class="col-md-4">
                <div class="mb-3">

                    <label class="form-label">
                        Baseline Traders
                    </label>

                    <input type="number" x-model="baselineTraders"
                        class="form-control @error('baseline_traders') is-invalid @enderror">

                    @error('baseline_traders')
                        <span class="text-danger">
                            {{ $message }}
                        </span>
                    @enderror

                </div>
            </div>

        </div>

        <h5>Crops</h5>

        <div class="row">

            <div class="col-md-4">
                <div class="mb-3">
                    <label for="cassava" class="form-label">
                        Cassava
                    </label>

                    <input type="number" id="cassava" x-model="cassava" class="form-control">
                </div>
            </div>

            <div class="col-md-4">
                <div class="mb-3">
                    <label for="potato" class="form-label">
                        Potato
                    </label>

                    <input type="number" id="potato" x-model="potato" class="form-control">
                </div>
            </div>

            <div class="col-md-4">
                <div class="mb-3">
                    <label for="sweet_potato" class="form-label">
                        Sweet Potato
                    </label>

                    <input type="number" id="sweet_potato" x-model="sweetPotato" class="form-control">
                </div>
            </div>

        </div>
        <div class="d-grid col-12 justify-content-center">

            <button class="btn btn-warning" type="submit"
                @click="window.scrollTo({
                top: 0,
                behavior: 'smooth'
            })">
                Submit Data
            </button>

        </div>

    </form>
</div>
</div>
