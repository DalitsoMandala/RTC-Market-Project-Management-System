<div>

    @section('title')
        Add Gross Margin Data
    @endsection
    <div x-data class="mt-4 container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Manage Gross Margins</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active">Manage Gross Margins</li>

                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <x-alerts />

        <ul class=" nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="batch-tab" data-bs-toggle="tab" data-bs-target="#normal"
                    type="button" role="tab" aria-controls="home" aria-selected="true">
                    ADD GROSS MARGIN
                </button>
            </li>




        </ul>
        <div class="card">
            <div class="card-body">
                <form wire:submit.prevent="save">
                    {{-- Gross Margin Title --}}
<div class="row">
                    <div class="mb-3 col-md-12">
                        <label class="form-label">Name of Producer</label>
                        <input type="text" class="form-control @error('name_of_producer') is-invalid @enderror"
                            wire:model="name_of_producer">
                        @error('name_of_producer')
                            <x-error>{{ $message }}</x-error>
                        @enderror
                    </div>

                        <div class="mb-3 col-md-4">
                            <label class="form-label d-block">Sex</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('sex') is-invalid @enderror" type="radio"
                                    id="male" value="Male" wire:model="sex">
                                <label class="form-check-label" for="sex">Male</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('sex') is-invalid @enderror" type="radio"
                                    id="female" value="Female" wire:model="sex">
                                <label class="form-check-label" for="sex">Female</label>
                            </div> <br>
                            @error('sex')
                                <x-error>{{ $message }}</x-error>
                            @enderror
                        </div>
                        <div class="mb-3 col-md-4">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control @error('phone_number') is-invalid @enderror"
                                wire:model="phone_number" oninput="this.value = this.value.toUpperCase()"
                                wire:model="phone_number">
                            @error('phone_number')
                                <x-error>{{ $message }}</x-error>
                            @enderror
                        </div>


                        <div class="mb-3 col-md-4">
                            <label for="" class="form-label ">ENTERPRISE</label>
                            <div class="form-group">

                                <select
                                    class="form-select @error('enterprise')
            is-invalid
        @enderror"
                                    wire:model='enterprise'>
                                    <option value="">Select one</option>
                                    <option value="Cassava">Cassava</option>
                                    <option value="Potato">Potato</option>
                                    <option value="Sweet potato">Sweet potato</option>
                                </select>
                            </div>


                            @error('enterprise')
                                <x-error>{{ $message }}</x-error>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-4">
                            <label for="date" class="form-label"> Date</label>
                            <input type="date" wire:model="date"
                                class="form-control @error('date') is-invalid @enderror" id="date">
                            @error('date')
                                <x-error>{{ $message }}</x-error>
                            @enderror
                        </div>
                        <div class="mb-3 col-md-4">
                            <label class="form-label">EPA</label>
                            <input type="text" class="form-control @error('epa') is-invalid @enderror"
                                wire:model="epa">
                            @error('epa')
                                <x-error>{{ $message }}</x-error>
                            @enderror
                        </div>
                        <div class="mb-3 col-md-4">
                            <label class="form-label">Section</label>
                            <input type="text" class="form-control @error('section') is-invalid @enderror"
                                wire:model="section">
                            @error('section')
                                <x-error>{{ $message }}</x-error>
                            @enderror
                        </div>


                        <div class="mb-3 col-md-4">
                            <label class="form-label">TA</label>
                            <input type="text" class="form-control @error('ta') is-invalid @enderror"
                                wire:model="ta">
                            @error('ta')
                                <x-error>{{ $message }}</x-error>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-4">
                            <label class="form-label">Village</label>
                            <input type="text" class="form-control @error('village') is-invalid @enderror"
                                wire:model="village">
                            @error('section')
                                <x-error>{{ $message }}</x-error>
                            @enderror
                        </div>



                        <div class="mb-3 col-md-4">
                            <label class="mb-3 form-label d-block">Season</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('season') is-invalid @enderror" type="radio"
                                    id="Rainfed" value="Rainfed" wire:model="season">
                                <label class="form-check-label" for="season">Rainfed</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('season') is-invalid @enderror" type="radio"
                                    id="Irrigated" value="Irrigated" wire:model="season">
                                <label class="form-check-label" for="season">Irrigated</label>
                            </div> <br>
                            @error('season')
                                <x-error>{{ $message }}</x-error>
                            @enderror
                        </div>


                        <div class="mb-3 col-md-4">
                            <label for="district" class="form-label">District</label>
                            <select class="form-select @error('district') is-invalid @enderror"
                                wire:model='district'>
                                @include('layouts.district-options')
                            </select>
                            @error('district')
                                <x-error>{{ $message }}</x-error>
                            @enderror
                        </div>
                        <div class="mb-3 col-md-4">
                            <label for="gender" class="form-label">gender</label>
                            <select class="form-select @error('gender') is-invalid @enderror" wire:model="gender"
                                id="gender">
                                <option value="">-- Select One --</option>
                                <option selected value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="mb-3 col-md-4">
                            <label class="form-label">GPS SOUTHINGS</label>
                            <input type="number" step="any"
                                class="form-control @error('gps_s') is-invalid @enderror" wire:model="gps_s">
                            @error('gps_s')
                                <x-error>{{ $message }}</x-error>
                            @enderror
                        </div>
                        <div class="mb-3 col-md-4">
                            <label class="form-label">GPS EASTINGS</label>
                            <input type="number" step="any"
                                class="form-control @error('gps_e') is-invalid @enderror" wire:model="gps_e">
                            @error('gps_e')
                                <x-error>{{ $message }}</x-error>
                            @enderror
                        </div>
                        <div class="mb-3 col-md-4">
                            <label class="form-label">Elevation</label>
                            <input type="number" step="any"
                                class="form-control @error('elevation') is-invalid @enderror" wire:model="elevation">
                            @error('elevation')
                                <x-error>{{ $message }}</x-error>
                            @enderror
                        </div>
                        <div class="mb-3 col-md-4">
                            <label class="mb-3 form-label d-block">Type of Produce</label>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('type_of_produce') is-invalid @enderror"
                                    type="radio" id="typeSeed" value="Seed" wire:model="type_of_produce">
                                <label class="form-check-label" for="typeSeed">Seed</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('type_of_produce') is-invalid @enderror"
                                    type="radio" id="typeWare" value="Ware" wire:model="type_of_produce">
                                <label class="form-check-label" for="typeWare">Ware</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('type_of_produce') is-invalid @enderror"
                                    type="radio" id="typeCuttings" value="Cuttings" wire:model="type_of_produce">
                                <label class="form-check-label" for="typeCuttings">Cuttings</label>
                            </div>


                            <br>
                            @error('type_of_produce')
                                <x-error>{{ $message }}</x-error>
                            @enderror
                        </div>


</div>

                    {{-- Item Table --}}
                    <div class="rounded table-responsive">
                        <table class="table align-middle table-hover table-bordered table-sm "
                            x-data="{
                                items: @entangle('items'),
                                varietyOptions: @entangle('varietyOptions'),
                                sellingPrice: @entangle('sellingPrice'),
                                sellingPriceQty: @entangle('sellingPriceQty'),
                                sellingPriceDesc: @entangle('sellingPriceDesc'),
                                sellingPriceUnit: @entangle('sellingPriceUnit'),
                                yield: @entangle('yield'),
                                yieldQty: @entangle('yieldQty'),
                                yieldDesc: @entangle('yieldDesc'),
                                yieldUnit: @entangle('yieldUnit'),
                                income: @entangle('income'),
                                incomeQty: @entangle('incomeQty'),
                                incomeDesc: @entangle('incomeDesc'),
                                incomeUnit: @entangle('incomeUnit'),
                                breakEvenYield: @entangle('breakEvenYield'),
                                breakEvenPrice: @entangle('breakEvenPrice'),
                                grossMargin: @entangle('grossMargin'),
                                totalValuableCost: @entangle('totalValuableCost'),
                                totalHarvestQty: @entangle('totalHarvestQty'),
                                totalHarvest: @entangle('totalHarvest'),
                                totalHarvestDesc: @entangle('totalHarvestDesc'),
                                get totalHarvest() {

                                    return parseFloat(this.totalHarvestQty) || 0;
                                },
                                get totalValuableCost() {
                                    return this.items.reduce((sum, item) => {
                                        let qty = parseFloat(item.qty) || 0;
                                        let unit = parseFloat(item.unit_price) || 0;
                                        return sum + (qty * unit);
                                    }, 0);
                                },

                                get sellingPrice() {
                                    return (parseFloat(this.sellingPriceQty) || 0) * (parseFloat(this.sellingPriceUnit) || 0);
                                },

                                get income() {
                                    return (parseFloat(this.incomeQty) || 0) * (parseFloat(this.incomeUnit) || 0);
                                },

                                get yield() {
                                    return (this.income > 0) ? ((this.totalValuableCost / this.income) * 100) : 0;
                                },

                                get breakEvenYield() {
                                    return (this.sellingPrice > 0) ? (this.totalValuableCost / this.sellingPrice) : 0;
                                },

                                get breakEvenPrice() {
                                    return (this.yield > 0) ? (this.totalValuableCost / this.yield) : 0;
                                },

                                get grossMargin() {
                                    return this.income - this.totalValuableCost;
                                },

                                init() {
                                    this.$watch('sellingPrice', (val) => $wire.sellingPrice = val);
                                    this.$watch('income', (val) => $wire.income = val);
                                    this.$watch('yield', (val) => $wire.yield = val);
                                    this.$watch('breakEvenYield', (val) => $wire.breakEvenYield = val);
                                    this.$watch('breakEvenPrice', (val) => $wire.breakEvenPrice = val);
                                    this.$watch('grossMargin', (val) => $wire.grossMargin = val);
                                    this.$watch('totalValuableCost', (val) => $wire.totalValuableCost = val);
                                    this.$watch('totalHarvest', (val) => $wire.totalHarvest = val);
                                }
                            }">

                            <!-- === HEADER === -->
                            <thead class=" table-secondary">
                                <tr class="">
                                    <th colspan="6" class="p-2 text-center">Total Valuable Costs</th>
                                </tr>
                                <tr>
                                    <th>Item</th>
                                    <th>Unit</th>
                                    <th>Qty</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <!-- === BODY === -->
                            <tbody>

                                <tr class="">
                                    <th colspan="6" class="p-2 ">Seed (Variety)</th>
                                </tr>
                                @foreach ($varietyOptions as $index => $variety)
                                    <tr>
                                        <!-- Item -->
                                        <td>


                                            <input type="text"
                                                class="form-control @error('varietyOptions.' . $index . '.variety') is-invalid @enderror"
                                                wire:model='varietyOptions.{{ $index }}.variety' />
                                            @error('varietyOptions.' . $index . '.variety')
                                                <x-error>{{ $message }}</x-error>
                                            @enderror
                                        </td>

                                        <!-- Description -->
                                        <td>
                                            <input type="hidden"
                                                class="form-control @error('varietyOptions.' . $index . '.unit') is-invalid @enderror"
                                                wire:model="varietyOptions.{{ $index }}.unit">
                                            {{ $varietyOptions[$index]['unit'] }}
                                        </td>

                                        <!-- Qty -->
                                        <td>
                                            <input type="number" step="any"
                                                class="form-control @error('varietyOptions.' . $index . '.qty') is-invalid @enderror"
                                                wire:model="varietyOptions.{{ $index }}.qty">
                                            @error('varietyOptions.' . $index . '.qty')
                                                <x-error>{{ $message }}</x-error>
                                            @enderror
                                        </td>

                                        <!-- Unit Price -->
                                        <td>
                                            <input type="number" step="any"
                                                class="form-control @error('varietyOptions.' . $index . '.unit_price') is-invalid @enderror"
                                                wire:model="varietyOptions.{{ $index }}.unit_price">
                                            @error('varietyOptions.' . $index . '.unit_price')
                                                <x-error>{{ $message }}</x-error>
                                            @enderror
                                        </td>

                                        <!-- Total -->
                                        <td>
                                            <input readonly class="form-control"
                                                :value="'MWK ' + (parseFloat(varietyOptions[{{ $index }}].qty || 0) *
                                                    parseFloat(
                                                        varietyOptions[{{ $index }}].unit_price || 0)).toFixed(
                                                    2)">
                                        </td>

                                        <!-- Remove -->
                                        <td>
                                            <button class="btn btn-danger btn-sm" wire:loading.attr="disabled"
                                                wire:click.prevent="removeVariety({{ $index }})">Remove</button>
                                        </td>
                                    </tr>
                                @endforeach

                                <!-- Add Row -->
                                <tr>
                                    <td colspan="6">
                                        <button class="btn btn-warning btn-sm" wire:click="addVariety"
                                            wire:loading.attr="disabled">
                                            Add Row +
                                        </button>
                                    </td>
                                </tr>
                                @foreach ($categoryOptions as $key => $category)
                                    <tr class="">

                                        <th colspan="6" class="p-2 ">{{ $category }}</th>


                                    </tr>

                                    @foreach ($items as $index => $item)
                                        @if ($item['category'] == $category)
                                            <tr>
                                                <!-- Item -->
                                                <td>

                                                    <input type="hidden" class="form-control"
                                                        value="{{ $item['category'] }}"
                                                        wire:model='items.{{ $index }}.category' />
                                                    <input type="hidden" class="form-control"
                                                        value="{{ $item['item'] }}"
                                                        wire:model='items.{{ $index }}.item' />
                                                    {{ $item['item'] }}
                                                </td>

                                                <!-- Description -->
                                                <td>
                                                    <input type="hidden"
                                                        class="form-control @error('items.' . $index . '.description') is-invalid @enderror"
                                                        wire:model="items.{{ $index }}.description">
                                                    {{ $item['unit'] }}
                                                </td>

                                                <!-- Qty -->
                                                <td>
                                                    <input type="number" step="any"
                                                        class="form-control @error('items.' . $index . '.qty') is-invalid @enderror"
                                                        wire:model="items.{{ $index }}.qty">
                                                    @error('items.' . $index . '.qty')
                                                        <x-error>{{ $message }}</x-error>
                                                    @enderror
                                                </td>

                                                <!-- Unit Price -->
                                                <td>
                                                    <input type="number" step="any"
                                                        class="form-control @error('items.' . $index . '.unit_price') is-invalid @enderror"
                                                        wire:model="items.{{ $index }}.unit_price">
                                                    @error('items.' . $index . '.unit_price')
                                                        <x-error>{{ $message }}</x-error>
                                                    @enderror
                                                </td>

                                                <!-- Total -->
                                                <td>
                                                    <input readonly class="form-control"
                                                        :value="'MWK ' + (parseFloat(items[{{ $index }}].qty || 0) *
                                                            parseFloat(
                                                                items[{{ $index }}].unit_price || 0)).toFixed(2)">
                                                </td>


                                            </tr>
                                        @endif
                                    @endforeach
                                @endforeach



                            </tbody>

                            <thead class=" table-secondary">
                                <tr class="">
                                    <th colspan="6" class="p-2 text-center">Calculations</th>
                                </tr>
                                <tr>
                                    <th>Item</th>
                                    <th>Unit</th>
                                    <th>Qty</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                    <th>Action</th>

                                </tr>
                            </thead>

                            <tfoot>

                                <tr>
                                    <td>
                                        <strong>Total Harvest:</strong>
                                    </td>

                                    <!-- Description -->
                                    <td>
                                        {{ $totalHarvestDesc }}

                                    </td>

                                    <!-- Qty -->
                                    <td>
                                        <input type="number" step="any"
                                            class="form-control @error('totalHarvestQty') is-invalid @enderror "
                                            wire:model="totalHarvestQty">
                                        @error('totalHarvestQty')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </td>

                                    <!-- Unit Price -->
                                    <td>
                                        <input type="number" step="any" class="form-control " readonly>

                                    </td>
                                    <td>
                                        <input type="number" readonly step="any" class="form-control"
                                            x-bind:value="totalHarvest.toFixed(2)">
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <strong>Prevailing Selling Price (MWK):</strong>
                                    </td>

                                    <!-- Description -->
                                    <td>
                                        <input type="hidden"
                                            class="form-control @error('sellingPriceDesc') is-invalid @enderror"
                                            wire:model="sellingPriceDesc">
                                        {{ $sellingPriceDesc }}
                                    </td>

                                    <!-- Qty -->
                                    <td>
                                        <input type="number" step="any"
                                            class="form-control @error('sellingPriceQty') is-invalid @enderror"
                                            wire:model="sellingPriceQty">
                                        @error('sellingPriceQty')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </td>

                                    <!-- Unit Price -->
                                    <td>
                                        <input type="number" step="any"
                                            class="form-control @error('sellingPriceUnit') is-invalid @enderror"
                                            wire:model="sellingPriceUnit">
                                        @error('sellingPriceUnit')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </td>
                                    <td>
                                        <input type="number" readonly step="any" class="form-control"
                                            x-bind:value="sellingPrice.toFixed(2)">
                                    </td>
                                </tr>

                                <tr>
                                    <td><strong>Income (MWK):</strong></td>
                                    <!-- Description -->
                                    <td>
                                        <input type="hidden"
                                            class="form-control @error('incomeDesc') is-invalid @enderror"
                                            wire:model="incomeDesc">
                                        {{ $incomeDesc }}
                                    </td>

                                    <!-- Qty -->
                                    <td>
                                        <input type="number" step="any"
                                            class="form-control @error('incomeQty') is-invalid @enderror"
                                            wire:model="incomeQty">
                                        @error('incomeQty')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </td>

                                    <!-- Unit Price -->
                                    <td>
                                        <input type="number" step="any"
                                            class="form-control @error('incomeUnit') is-invalid @enderror"
                                            wire:model="incomeUnit">
                                        @error('incomeUnit')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" readonly
                                            x-bind:value="income.toFixed(2)">
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Total Valuable Costs (MWK):</strong></td>
                                    <td>
                                        <input type="hidden" class="form-control" readonly />
                                    </td>
                                    <td> <input type="text" class="form-control" readonly /></td>
                                    <td> <input type="text" class="form-control" readonly /></td>
                                    <td>
                                        <input type="text" class="form-control" readonly
                                            x-bind:value="totalValuableCost.toFixed(2)">
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Yield (kg/unit):</strong></td>
                                    <!-- Description -->
                                    <td>
                                        <input type="hidden" class="form-control" readonly />
                                    </td>
                                    <td> <input type="text" class="form-control" readonly /></td>
                                    <td> <input type="text" class="form-control" readonly /></td>
                                    <td>
                                        <input type="number" step="any" class="form-control" readonly
                                            x-bind:value="yield.toFixed(2)">
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Break-even Yield:</strong></td>
                                    <td>
                                        <input type="hidden" class="form-control" readonly />
                                    </td>
                                    <td> <input type="text" class="form-control" readonly /></td>
                                    <td> <input type="text" class="form-control" readonly /></td>
                                    <td>
                                        <input type="text" class="form-control" readonly
                                            x-bind:value="breakEvenYield.toFixed(2)">
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Break-even Price (MWK):</strong></td>
                                    <!-- Description -->
                                    <td>
                                        <input type="hidden" class="form-control" readonly />
                                    </td>
                                    <td> <input type="text" class="form-control" readonly /></td>
                                    <td> <input type="text" class="form-control" readonly /></td>
                                    <td>
                                        <input type="text" class="form-control" readonly
                                            x-bind:value="breakEvenPrice.toFixed(2)">
                                    </td>
                                </tr>
                                <tr class="table-secondary">
                                    <td colspan="4" class="text-end"><strong class="fs-5">Gross Margin
                                            (Profit):</strong></td>
                                    <td>
                                        <input type="text" class="form-control fs-5 fw-bold" readonly
                                            x-bind:value="grossMargin.toFixed(2)">
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                    </div>
                    <div class="mt-5 d-flex col-12 justify-content-center" x-data>

                        <button class="mx-1 btn btn-secondary" id="resetForm"
                            @click="window.scrollTo({
                                                    top: 0,
                                                    behavior: 'smooth'
                                                })
                                                $wire.clear();
                                                ">Reset
                            Form</button>
                        <button class="px-5 btn btn-warning"
                            @click="window.scrollTo({
                                                top: 0,
                                                behavior: 'smooth'
                                            })"
                            type="submit">Submit Data</button>
                    </div>

                </form>
            </div>
        </div>

    </div>


</div>

{{-- @script
    <script>
        // window.addEventListener('set-new-title', (e) => {
        //     $wire.set('selectedTitle', e.detail.title);
        //     setTimeout(() => {

        //         console.log(e.detail.title);
        //     }, 2000);
        // });
    </script>
@endscript --}}
