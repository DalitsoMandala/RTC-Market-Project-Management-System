<div>
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Dashboard</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active">Page Name</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header" x-data="{ is_open: false }" x-init="$wire.on('editData', () => {
                        is_open = true;
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    
                    })">
                        <button class="btn btn-primary" @click="is_open = !is_open">Add Submission Period+</button>

                        <div class="mt-2 border shadow-none row card card-body" x-show="is_open">
                            <div class="col-12 col-md-6" id="form">
                                <form wire:submit='save'>

                                    <x-alerts />


                                    <div class="mb-3">

                                        <label for="" class="form-label">Choose Project</label>
                                        <select class="form-select form-select-md"
                                            wire:model.live.debounce.500ms="selectedProject">
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



                                    <div class="mb-3" wire:loading.class='opacity-25' wire:target="selectedProject"
                                        wire:loading.attr='disabled'>

                                        <label for="" class="form-label">Choose Form</label>
                                        <select class="form-select form-select-md "
                                            wire:model.live.debounce.500ms="selectedForm">
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
                                        wire:loading.attr='disabled'>

                                        <label for="" class="form-label">Choose Reporting Period</label>



                                        <select class="form-select form-select-md "
                                            wire:model.live.debounce.500ms='selectedMonth'>

                                            <option value="">Select one</option>

                                            <div x-data="{ selectedProject: $wire.entangle('selectedProject') }" x-show="selectedProject">
                                                @foreach ($months as $month)
                                                    <option wire:key='{{ $month->id }}' value="{{ $month->id }}">
                                                        {{ $month->start_month . '-' . $month->end_month }} </option>
                                                @endforeach
                                            </div>


                                        </select>


                                        @error('selectedMonth')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>

                                    <div class="mb-3" wire:loading.class='opacity-25' wire:target="selectedProject"
                                        wire:loading.attr='disabled'>

                                        <label for="" class="form-label">Choose Financial Year</label>
                                        <select
                                            class="form-select form-select-md "wire:model.live.debounce.500ms='selectedFinancialYear'>

                                            <option value="">Select one</option>
                                            <div x-data="{ selectedProject: $wire.entangle('selectedProject') }" x-show="selectedProject">
                                                @foreach ($financialYears as $year)
                                                    <option value="{{ $year->id }}">{{ $year->number }} </option>
                                                @endforeach

                                            </div>

                                        </select>

                                        @error('selectedFinancialYear')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>

                                    <div class="indicators">


                                        <div class="mb-3" wire:ignore x-data="{
                                            selected: $wire.entangle('selectedIndicator'),
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

                                    <div class="mb-3">
                                        <label for="" class="form-label">Start of submissions</label>
                                        <x-text-input wire:model='start_period' type="date" />
                                        @error('start_period')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="" class="form-label">End of submissions</label>
                                        <x-text-input wire:model='end_period' type="date" />
                                        @error('end_period')
                                            <x-error>{{ $message }}</x-error>
                                        @enderror
                                    </div>

                                    <div class="mb-3 form-check form-switch form-switch-lg " dir="ltr"
                                        x-data="{ switchOn: $wire.entangle('status'), row: $wire.entangle('rowId') }" x-show="row">
                                        <input type="checkbox" x-model="switchOn" class="form-check-input"
                                            id="customSwitchsizelg">
                                        <label class="form-check-label" for="customSwitchsizelg">Submission
                                            Status</label>


                                    </div>




                                    <div class="mb-3 form-check form-switch form-switch-lg " dir="ltr"
                                        x-data="{ expired: $wire.entangle('expired'), row: $wire.entangle('rowId') }" x-show="row !== null">
                                        <input type="checkbox" x-model="expired" class="form-check-input"
                                            id="customSwitchsizelg">
                                        <label class="form-check-label" for="customSwitchsizelg">Cancel/Set to Expire
                                        </label>
                                        <br>
                                        <small class="text-danger fs-6 ">Warning: This will make the submission
                                            period
                                            inaccessible for updates</small>

                                    </div>

                                    <button class="btn btn-primary" type="submit" wire:loading.attr='disabled'>
                                        Submit
                                    </button>

                                    <button class="btn btn-outline-primary" type="button" wire:click='resetData'
                                        wire:loading.attr='disabled'>
                                        Reset
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                    <div class="card-body">
                        <livewire:tables.submission-period-table>
                    </div>
                </div>
            </div>
        </div>



        <div x-data x-init="$wire.on('showModal', (e) => {
        
            const myModal = new bootstrap.Modal(document.getElementById(e.name), {})
            myModal.show();
        })">


            <x-modal id="view-submission-period-modal" title="edit">
                <form>
                    <div class="mb-3">

                        <x-text-input placeholder="Name of indicator..." />
                    </div>

                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>

                    </div>
                </form>
            </x-modal>

        </div>




    </div>

</div>
