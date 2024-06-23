<div class="row">
    <div class="card">
        <div class="card-body">
            <div class=" col-12">


                <div class="indicators">


                    <div class="mb-3" wire:ignore x-data="{
                        selected: $wire.entangle('selectedIndicator').live,
                        myInput(data) {
                            this.selected = data;
                        },
                    
                    }" x-init=" const input = $refs.selectElementIndicator;
                     const selectInput = new Choices($refs.selectElementIndicator, {
                         shouldSort: false,
                         removeItemButton: true,
                         placeholder: true,
                         placeholderValue: 'Select indicators here...',
                         choices: [
                             { value: '', label: 'Select indicator here...', selected: true, disabled: true }, // Add this empty option
                             ...@js($indicators->map(fn($option) => ['value' => $option->id, 'label' => '(' . $option->indicator_no . ') ' . $option->indicator_name]))
                         ]
                     });
                    
                     input.addEventListener(
                         'change',
                         function(event) {
                    
                    
                             let selectedValues = selectInput.getValue(true);
                             if (selectedValues != undefined) {
                                 myInput(selectedValues);
                             } else {
                                 selectInput.setChoiceByValue('');
                                 myInput(null);
                             }
                    
                    
                    
                    
                         },
                         false,
                     );
                    
                    
                     $wire.on('update-indicator', (value) => {
                         selectInput.setChoiceByValue(value);
                    
                     })">
                        <label for="" class="form-label">Indicator</label>
                        <select class="form-select form-select-sm" x-ref="selectElementIndicator">

                        </select>


                    </div>
                    @error('selectedIndicator')
                        <x-error>{{ $message }}</x-error>
                    @enderror

                </div>

                <div class="mb-3 d-none">

                    <label for="" class="form-label">Choose Project</label>
                    <select class="form-select form-select-md " wire:model="selectedProject" disabled>
                        <option selected value="">Select one</option>


                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}

                            </option>
                        @endforeach
                    </select>

                    @error('selectedProject')
                        <x-error>{{ $message }}</x-error>
                    @enderror
                </div>



                <div class="mb-3 d-none" wire:loading.class='opacity-25' wire:target="selectedProject"
                    wire:loading.attr='disabled'>

                    <label for="" class="form-label">Choose Form</label>
                    <select class="form-select form-select-md " wire:model="selectedForm" disabled>
                        <option selected value="">Select one</option>
                        <div x-data="{ selectedProject: $wire.entangle('selectedProject') }" x-show="selectedProject">
                            @foreach ($forms as $form)
                                <option value="{{ $form->id }}">{{ $form->name }}

                                </option>
                            @endforeach
                        </div>




                    </select>

                    @error('selectedForm')
                        <x-error>{{ $message }}</x-error>
                    @enderror
                </div>


                <div class="mb-3" wire:loading.class='opacity-25' wire:target="selectedProject"
                    wire:loading.attr='disabled' x-data="{ selectedProject: $wire.entangle('selectedProject').live, selectedMonth: $wire.entangle('selectedMonth').live }">

                    <label for="" class="form-label">Choose Reporting Period</label>



                    <select class="form-select form-select-md" x-model.debounce.500ms='selectedMonth'>

                        <option value="">Select one</option>

                        <div x-show="selectedProject">
                            @foreach ($months as $month)
                                <option wire:key='{{ $month->id }}' value="{{ $month->id }}">
                                    {{ $month->start_month . '-' . $month->end_month }}
                                </option>
                            @endforeach
                        </div>


                    </select>


                    @error('selectedMonth')
                        <x-error>{{ $message }}</x-error>
                    @enderror
                </div>

                <div class="mb-3" wire:loading.class='opacity-25' wire:target="selectedProject"
                    wire:loading.attr='disabled' x-data="{
                        selectedProject: $wire.entangle('selectedProject').live,
                        selectedFinancialYear: $wire.entangle('selectedFinancialYear').live
                    }">

                    <label for="" class="form-label">Choose Financial Year</label>
                    <select class="form-select form-select-md " x-model.debounce.500ms='selectedFinancialYear'>

                        <option value="">Select one</option>
                        <div x-show="selectedProject">
                            @foreach ($financialYears as $year)
                                <option value="{{ $year->id }}">{{ $year->number }}
                                </option>
                            @endforeach

                        </div>

                    </select>

                    @error('selectedFinancialYear')
                        <x-error>{{ $message }}</x-error>
                    @enderror
                </div>
            </div>
        </div>
    </div>

</div>
