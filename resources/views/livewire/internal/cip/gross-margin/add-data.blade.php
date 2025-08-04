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

                    <div class="mb-3" x-data="{
                        selectedTitle: @entangle('selectedTitle'),
                        newTitle: @entangle('newTitle'),
                        selectedTitles: [],
                        initTitles: @entangle('existingTitles'),
                        setData(titles, title) {
                            this.selectedTitles = titles;
                            this.selectedTitles.filter(item => item.title == title).length > 0 ? this.selectedTitle = title : this.selectedTitle = 'Other';

                        }
                    }" x-init="() => {
                        selectedTitles = initTitles;

                    }"
                        @set-new-title.window="setData($event.detail.titles,$event.detail.title)">

                        <label class="form-label">Gross Margin Title</label>

                        <select class="form-select" x-model="selectedTitle" wire:model="selectedTitle"
                            wire:loading.attr="disabled">
                            <option value="">-- Choose One here --</option>
                            <option value="Other">-- New Title --</option>
                            <template x-for="titles in selectedTitles">
                                <option :value="titles.title" x-text="titles.title"></option>
                            </template>
                        </select>
                        @error('selectedTitle')
                            <x-error>{{ $message }}</x-error>
                        @enderror

                        <template x-if="selectedTitle === 'Other'">
                            <input type="text" class="mt-2 form-control" placeholder="Enter new title"
                                x-model="newTitle" wire:model="newTitle">
                        </template>
                        @error('newTitle')
                            <x-error>{{ $message }}</x-error>
                        @enderror

                    </div>



                    {{-- Farmer Metadata Section --}}
                    <div class="row">
                        <div class="mb-3 col-md-12">
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
                        <div class="mb-3 col-md-12">
                            <label class="form-label">Name of Producer</label>
                            <input type="text" class="form-control @error('name_of_producer') is-invalid @enderror"
                                wire:model="name_of_producer">
                            @error('name_of_producer')
                                <x-error>{{ $message }}</x-error>
                            @enderror
                        </div>
                        <div class="mb-3 col-md-12">
                            <label class="form-label d-block">Season</label>
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
                            <select class="form-select @error('district') is-invalid @enderror" wire:model='district'>
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
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control @error('phone_number') is-invalid @enderror"
                                wire:model="phone_number" oninput="this.value = this.value.toUpperCase()"
                                wire:model="phone_number">
                            @error('phone_number')
                                <x-error>{{ $message }}</x-error>
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
                        <div class="mb-3 col-md-12">
                            <label class="form-label d-block">Type of Produce</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('type_of_produce') is-invalid @enderror"
                                    type="radio" id="typeSeed" value="Seed" wire:model="type_of_produce">
                                <label class="form-check-label" for="typeSeed">Seed</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input @error('type_of_produce') is-invalid @enderror"
                                    type="radio" id="typeTable" value="Table" wire:model="type_of_produce">
                                <label class="form-check-label" for="typeTable">Table</label>
                            </div> <br>
                            @error('type_of_produce')
                                <x-error>{{ $message }}</x-error>
                            @enderror
                        </div>

                    </div>



                    {{-- Item Table --}}
                    <div class="table-responsive">
                        <table class="table align-middle table-hover table-bordered table-sm" x-data="{
                            items: @entangle('items'),
                            get grossProfit() {
                                return this.items.reduce((sum, item) => {
                                    let qty = parseFloat(item.qty) || 0;
                                    let unit = parseFloat(item.unit_price) || 0;
                                    return sum + (qty * unit);
                                }, 0);
                            }
                        }">

                            <thead class="table-secondary">
                                <tr>
                                    <th colspan="6" class="text-start"> Total valuable costs</th>
                                </tr>
                            </thead>
                            <thead class="">
                                <tr>
                                    <th>Item</th>
                                    <th>Description</th>
                                    <th>Qty</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach ($items as $index => $item)
                                    <tr>
                                        {{-- Item --}}
                                        <td>
                                            <div x-data="{ showInput: @js($item['item'] === 'Other') }" x-init="$watch(() => items[{{ $index }}].item, val => showInput = val === 'Other')">
                                                <select
                                                    class="form-select @error('items.' . $index . '.item') is-invalid @enderror"
                                                    wire:model="items.{{ $index }}.item">
                                                    <option value="">-- Select Item --</option>
                                                    @foreach ($itemOptions as $option)
                                                        <option value="{{ $option }}">{{ $option }}
                                                        </option>
                                                    @endforeach
                                                    <option value="Other">Other</option>
                                                </select>
                                                @error('items.' . $index . '.item')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror

                                                <div x-show="showInput" class="mt-2">
                                                    <input type="text"
                                                        class="form-control @error('items.' . $index . '.custom_item') is-invalid @enderror"
                                                        placeholder="New item name"
                                                        wire:model="items.{{ $index }}.custom_item">
                                                    @error('items.' . $index . '.custom_item')
                                                        <x-error>{{ $message }}</x-error>
                                                    @enderror
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Description --}}
                                        <td>
                                            <input type="text"
                                                class="form-control @error('items.' . $index . '.description') is-invalid @enderror"
                                                wire:model="items.{{ $index }}.description">


                                            @error('items.' . $index . '.description')
                                                <x-error>{{ $message }}</x-error>
                                            @enderror
                                        </td>

                                        {{-- Qty --}}
                                        <td>
                                            <input type="number" step="any"
                                                class="form-control  @error('items.' . $index . '.qty') is-invalid @enderror"
                                                wire:model="items.{{ $index }}.qty">


                                            @error('items.' . $index . '.qty')
                                                <x-error>{{ $message }}</x-error>
                                            @enderror
                                        </td>

                                        {{-- Unit Price --}}
                                        <td>
                                            <input type="number" step="any"
                                                class="form-control  @error('items.' . $index . '.unit_price') is-invalid @enderror"
                                                wire:model="items.{{ $index }}.unit_price">


                                            @error('items.' . $index . '.unit_price')
                                                <x-error>{{ $message }}</x-error>
                                            @enderror
                                        </td>

                                        {{-- Row Total --}}
                                        <td>
                                            <input readonly class="form-control"
                                                :value="(parseFloat(items[{{ $index }}].qty || 0) * parseFloat(items[
                                                    {{ $index }}].unit_price || 0)).toFixed(2)" />
                                        </td>

                                        {{-- Remove --}}
                                        <td>
                                            <button class="btn btn-danger btn-sm" wire:loading.attr="disabled"
                                                wire:click.prevent="removeItem({{ $index }})">
                                                Remove
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="6" class="text-start">
                                        <button class="btn btn-warning btn-sm" wire:click="addItem"
                                            wire:loading.attr="disabled">
                                            Add Row +
                                        </button>
                                    </td>
                                </tr>

                            </tbody>
                            <tbody>
                                <tr class="table-secondary">
                                    <th colspan="6">Other Items</th>
                                </tr>

                                <tr>
                                    <th>Item</th>
                                    <th>Description</th>
                                    <th>Qty</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>

                                </tr>
                                <tr>
                                    <td>


                                        <select class="form-select " disabled wire:model='yield'>

                                            <option selected value="Yield">Yield (Production)</option>

                                        </select>
                                        @error('yield')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror

                                    </td>

                                    <td>


                                        <input type="text" step="any"
                                            class="form-control @error('yield_description') is-invalid @enderror"
                                            wire:model="yield_description">
                                        @error('yield_description')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror


                                    </td>

                                    <td>

                                        <input type="number" step="any"
                                            class="form-control @error('yield_qty') is-invalid @enderror"
                                            wire:model="yield_qty">
                                        @error('yield_qty')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </td>

                                    <td>

                                        <input type="number" step="any"
                                            class="form-control @error('yield_unit_price') is-invalid @enderror"
                                            wire:model="yield_unit_price">
                                        @error('yield_unit_price')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </td>

                                    <td>

                                        <input type="number" step="any"
                                            class="form-control @error('yield_total') is-invalid @enderror"
                                            wire:model="yield_total">
                                        @error('yield_total')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </td>
                                </tr>
                                <tr>
                                    <td>


                                        <select class="form-select " disabled wire:model='price'>

                                            <option selected value="Price">Price/KG (Income)</option>

                                        </select>
                                        @error('income_price')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror

                                    </td>

                                    <td>


                                        <input type="text" step="any"
                                            class="form-control @error('income_price_description') is-invalid @enderror"
                                            wire:model="income_price_description">
                                        @error('income_price_description')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror


                                    </td>

                                    <td>

                                        <input type="number" step="any"
                                            class="form-control @error('income_price_qty') is-invalid @enderror"
                                            wire:model="income_price_qty">
                                        @error('income_price_qty')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </td>

                                    <td>

                                        <input type="number" step="any"
                                            class="form-control @error('income_price_unit_price') is-invalid @enderror"
                                            wire:model="income_price_unit_price">
                                        @error('income_price_unit_price')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </td>

                                    <td>

                                        <input type="number" step="any"
                                            class="form-control @error('income_price_total') is-invalid @enderror"
                                            wire:model="income_price_total">
                                        @error('income_price_total')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </td>
                                </tr>

                            </tbody>
                            <tfoot>

                                <tr class="fs-4">
                                    <td colspan="5" class="text-end">
                                        <strong>Gross Profit (MWK): </strong>
                                    </td>
                                    <td><strong x-text="grossProfit.toFixed(2)"></strong></td>
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
