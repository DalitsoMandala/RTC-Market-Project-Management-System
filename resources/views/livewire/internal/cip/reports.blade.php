<div>
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Reporting</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active">Reports</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <div class="card ">
                    <div class="card-body">

                        <form wire:submit='filter'>
                            <div class="row">


                                <div class="col-3">
                                    <div class="mb-1" wire:ignore x-data="{
                                        selected: $wire.entangle('selectedProject'),
                                        myInput(data) {
                                            this.selected = data;
                                        },
                                    }" x-init=" const input = $refs.selectElement;
                                     const selectInput = new Choices($refs.selectElement, {
                                         shouldSort: false,
                                         placeholder: true,
                                    
                                         choices: @js($projects->map(fn($option) => ['value' => $option->id, 'label' => $option->name])) // Adjust as per your model fields
                                     });
                                    
                                    
                                    
                                     input.addEventListener(
                                         'change',
                                         function(event) {
                                    
                                             myInput(event.detail.value);
                                    
                                    
                                    
                                         },
                                         false,
                                     );
                                     $wire.on('reset-filters', () => {
                                    
                                    
                                         selectInput.removeActiveItems(); // Clear the selected item
                                         selectInput.setChoiceByValue('');
                                    
                                     })">
                                        <label for="" class="form-label">Project</label>
                                        <select class="form-select form-select-sm" x-ref="selectElement">
                                            <option value="" disabled selected>Choose an option</option>
                                        </select>


                                    </div>
                                    @error('selectedProject')
                                        <x-error class="mb-1">{{ $message }}</x-error>
                                    @enderror
                                </div>
                                <div class="col-9">


                                    <div class="mb-1" wire:ignore x-data="{
                                        selected: $wire.entangle('selectedIndicators'),
                                        myInput(data) {
                                            this.selected = data;
                                        },
                                    
                                    }" x-init=" const input = $refs.selectElementIndicator;
                                     const selectInput = new Choices($refs.selectElementIndicator, {
                                         shouldSort: false,
                                         removeItemButton: true,
                                         placeholder: true,
                                         placeholderValue: 'Select indicators here...',
                                         choices: @js($indicators->map(fn($option) => ['value' => $option->id, 'label' => '(' . $option->indicator_no . ') ' . $option->indicator_name])) // Adjust as per your model fields
                                     });
                                    
                                     input.addEventListener(
                                         'change',
                                         function(event) {
                                    
                                    
                                             let selectedValues = selectInput.getValue(true);
                                    
                                    
                                             myInput(selectedValues);
                                    
                                    
                                         },
                                         false,
                                     );
                                     $wire.on('reset-filters', () => {
                                    
                                    
                                         selectInput.removeActiveItems(); // Clear the selected item
                                    
                                    
                                     })">
                                        <label for="" class="form-label">Indicator</label>
                                        <select class="form-select form-select-sm" multiple
                                            x-ref="selectElementIndicator">

                                        </select>


                                    </div>
                                    @error('selectedIndicators')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror

                                </div>
                                <div class="col-3" x-data="{ starting_period: $wire.entangle('starting_period') }">
                                    <div class="mb-1">
                                        <label for="" class="form-label">Starting Period</label>
                                        <x-flatpickr x-model="starting_period" />
                                    </div>
                                    @error('starting_period')
                                        <x-error class="mb-1">{{ $message }}</x-error>
                                    @enderror
                                </div>
                                <div class="col-3" x-data="{ ending_period: $wire.entangle('ending_period') }">
                                    <div class="mb-1">
                                        <label for="" class="form-label">Ending Period</label>
                                        <x-flatpickr x-model="ending_period" />
                                    </div>
                                    @error('ending_period')
                                        <x-error class="mb-1">{{ $message }}</x-error>
                                    @enderror
                                </div>
                                <div class="col-3 align-self-end">
                                    <div class="mb-1" x-data>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bx bx-filter"></i> Filter Data
                                        </button>
                                        <button class="btn btn-primary"
                                            @click="$wire.dispatch('reset-filters')">Reset</button>
                                    </div>

                                </div>
                            </div>

                        </form>
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <livewire:tables.reporting-table />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>







    </div>


</div>
