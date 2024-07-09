<div>

    <style>
        .table-sticky-col {
            position: sticky;
            left: 0;
            background-color: #fff;
            /* Ensure background matches table to avoid overlap issues */
            z-index: 1;
            /* Ensure it stays above other table content */
        }
    </style>
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Add submission period</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active">Add submission period</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-header">
                        <h3 class="card-title">Add submission period</h3>
                    </div>
                    <div class="card-header" x-data="{ is_open: true }">



                        <div class="col-12 col-md-6 col-md-sm-8" id="form">



                            <form wire:submit.debounce.1000ms='save'>
                                <x-alerts />

                                <div class="mb-3">

                                    <label for="project-select" class="form-label">Choose Project</label>
                                    <select id="project-select" class="form-select form-select-md"
                                        wire:model.live.debounce.500ms="selectedProject">
                                        <option selected value="">Select one</option>
                                        @foreach ($projects as $project)
                                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('selectedProject')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>


                                <div class="indicators">


                                    <div class="mb-3" wire:ignore x-init="() => {
                                    
                                        $('#select-indicators').select2({
                                            width: '100%',
                                            theme: 'bootstrap-5',
                                            containerCssClass: 'select2--small',
                                            dropdownCssClass: 'select2--small',
                                        });
                                    
                                    
                                        $('#select-indicators').on('select2:select', function(e) {
                                            let data = e.params.data;
                                            setTimeout(() => {
                                                $wire.set('selectedIndicator', data.id);
                                            }, 500)
                                    
                                    
                                        });
                                    
                                    
                                        $wire.on('update-indicator', (e) => {
                                    
                                    
                                    
                                    
                                            const selectElement = $('#select-indicators');
                                            const arrayOfObjects = e.data;
                                    
                                            selectElement.empty();
                                    
                                    
                                            selectElement.append('<option selected value=\'\'>Select one</option>');
                                            arrayOfObjects.forEach(data => {
                                    
                                                let newOption = new Option(`(${data.indicator_no}) ` + data.indicator_name, data.id, false, false);
                                                selectElement.append(newOption).trigger('change');
                                            });
                                    
                                            selectElement.val('').trigger('change');
                                    
                                            selectElement.select2
                                            setTimeout(() => {
                                                $wire.set('selectedIndicator', null);
                                            }, 500)
                                    
                                    
                                        });
                                    
                                        $wire.on('select-indicator', (e) => {
                                            const selectElement = $('#select-indicators');
                                            const arrayOfObjects = e.data;
                                    
                                            selectElement.empty();
                                    
                                    
                                            selectElement.append('<option selected value=\'\'>Select one</option>');
                                            arrayOfObjects.forEach(data => {
                                    
                                                let newOption = new Option(`(${data.indicator_no}) ` + data.indicator_name, data.id, false, false);
                                                selectElement.append(newOption).trigger('change');
                                            });
                                    
                                            selectElement.val(e.selected).trigger('change');
                                    
                                        })
                                    }">
                                        <label for="" class="form-label">Select Indicator</label>
                                        <select x-ref="select" class="form-select "
                                            wire:model.debounce='selectedIndicator' id="select-indicators">
                                            <option selected value="">Select one</option>
                                            @foreach ($indicators as $indicator)
                                                <option value="{{ $indicator->id }}">
                                                    ({{ $indicator->indicator_no }})
                                                    {{ $indicator->indicator_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>



                                    @error('selectedIndicator')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>



                                <div class="mb-3" wire:loading.class='opacity-25'
                                    wire:target="selectedProject, selectedIndicator, selectedForm"
                                    wire:loading.attr='disabled' x-data="{
                                        selectedForm: [],
                                        forms: [],
                                        setForms(data, forms) {
                                    
                                            this.forms = forms;
                                    
                                            let newData = data.map(num => num.toString());
                                    
                                    
                                        },
                                    
                                        selectForm() {
                                            $wire.selectedForm = this.selectedForm;
                                        }
                                    
                                    }" @change="selectForm()"
                                    @changed-form.window="setForms($event.detail.data,$event.detail.forms)"
                                    x-init="">

                                    <label for="form-select" class="form-label">Choose Form</label>
                                    <select id="form-select" class="form-select form-select-md" multiple
                                        x-model="selectedForm">


                                        <template :key="form.id" x-for="form in forms">

                                            <option :value="form.id"> <span x-text="form.name"></span>
                                            </option>
                                        </template>

                                    </select>


                                    @error('selectedForm')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>


                                <div class="mb-3" wire:loading.class='opacity-25'
                                    wire:target="selectedProject, selectedIndicator, selectedForm"
                                    wire:loading.attr='disabled'>
                                    <label for="month-select" class="form-label">Choose Reporting Period</label>
                                    <select id="month-select" class="form-select form-select-md"
                                        wire:model.debounce.500ms='selectedMonth'>
                                        <option value="">Select one</option>
                                        @foreach ($months as $month)
                                            <option wire:key='{{ $month->id }}' value="{{ $month->id }}">
                                                {{ $month->start_month . '-' . $month->end_month }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('selectedMonth')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>

                                <div class="mb-3" wire:loading.class='opacity-25'
                                    wire:target="selectedProject, selectedIndicator, selectedForm"
                                    wire:loading.attr='disabled'>
                                    <label for="year-select" class="form-label">Choose Financial Year</label>
                                    <select id="year-select" class="form-select form-select-md"
                                        wire:model.debounce.500ms='selectedFinancialYear'>
                                        <option value="">Select one</option>
                                        @foreach ($financialYears as $year)
                                            <option value="{{ $year->id }}">{{ $year->number }}</option>
                                        @endforeach
                                    </select>
                                    @error('selectedFinancialYear')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="start-period" class="form-label">Start of submissions</label>
                                    <x-text-input id="start-period" wire:model.debounce.500ms='start_period'
                                        type="date" />
                                    @error('start_period')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="end-period" class="form-label">End of submissions</label>
                                    <x-text-input id="end-period" wire:model.debounce.500ms='end_period'
                                        type="date" />
                                    @error('end_period')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>

                                <div class="mb-3 " dir="ltr" x-data="{ switchOn: $wire.entangle('status'), row: $wire.entangle('rowId') }" x-show="row">
                                    {{-- <input type="checkbox" x-model="switchOn" class="form-check-input"
                                            id="status-switch"> --}}

                                    {{--
                                        <label class="form-check-label" for="status-switch">(Submission Status <span
                                                class="badge " style="font-size: 10px"
                                                :class="{ 'bg-success': switchOn === true, 'bg-danger': switchOn === false }">
                                                <span x-show="switchOn === false">closed</span>
                                                <span x-show="switchOn === true">open</span></span>)</label> --}}
                                    <label for="">Open for submissions ?</label>
                                    <div class="square-switch d-flex align-items-baseline">
                                        <input type="checkbox" x-model="switchOn" id="square-switch1" switch="none"
                                            checked="">
                                        <label for="square-switch1" data-on-label="Yes" data-off-label="No"></label>

                                    </div>


                                </div>

                                <div class="mb-3 form-check form-switch form-switch-lg d-none" dir="ltr"
                                    x-data="{ expired: $wire.entangle('expired'), row: $wire.entangle('rowId') }" x-show="row !== null">
                                    <input type="checkbox" x-model="expired" class="form-check-input"
                                        id="expire-switch">
                                    <label class="form-check-label" for="expire-switch">Cancel/Set to
                                        Expire</label>
                                    <br>
                                    <small class="text-danger fs-6">Warning: This will make the submission
                                        period
                                        inaccessible for updates</small>
                                </div>

                                <button x-data class="btn btn-primary" type="submit" wire:loading.attr='disabled'
                                    @click="  window.scrollTo({
                                            top: 0,
                                            behavior: 'smooth'
                                        })">Submit</button>
                                <button class="btn btn-outline-primary" type="button"
                                    wire:click.debounce.1000ms='resetData' wire:loading.attr='disabled'>Reset</button>
                            </form>



                        </div>
                        <hr>

                        <div class="card-body">
                            @php

                                $route = Route::current()->getPrefix();
                            @endphp
                            <livewire:tables.submission-period-table :currentRoutePrefix="$route">
                        </div>
                    </div>


                </div>
            </div>




        </div>




    </div>


    @script
        <script>
            const tooltipTriggerList = document.querySelectorAll('button[title]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

            $wire.on('reload-tooltips', () => {

                setTimeout(() => {
                    const tooltipTriggerList = document.querySelectorAll('button[title]');
                    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(
                        tooltipTriggerEl))

                }, 1000);


            })
        </script>
    @endscript
</div>
