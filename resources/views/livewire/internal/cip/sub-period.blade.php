<div>
    @section('title')
        Submission period
    @endsection

    <div class="container-fluid">
        <x-alerts />
    </div>
    <div class="container-fluid " x-data="{
        showLoadingIndicator: true,
        init() {
    
    
            setTimeout(() => {
    
                this.showLoadingIndicator = false;
    
            }, 5000);
        }
    
    }" x-show="showLoadingIndicator">

        <div class="d-flex justify-content-center align-items-center vh-100">
            <div class="spinner-border text-warning spinner-border-lg" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

    </div>
    <div class="container-fluid" id="load-app" x-data="{
        showPage: false,
    
        init() {
    
            setTimeout(() => {
    
                this.showPage = true;
            }, 5000);
        }
    }" x-show="showPage">


        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Submission period</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active">Submission period</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="card">

                    <div class="card-header fw-bold ">
                        <h5 class="card-title"> Add submission period </h5>

                    </div>
                    <div class="card-body " x-data="{
                        is_open: true,
                    
                        selectAllIndicators: $wire.entangle('selectAllIndicators'),
                        skipTargets: $wire.entangle('skipTargets')
                    
                    }">







                        <form wire:submit.debounce.1000ms='save' id="form">


                            <div class="mb-3">

                                <label for="project-select" class="form-label">Choose Project</label>
                                <select id="project-select" class="form-select form-select-md"
                                    wire:model.live.debounce.1000ms="selectedProject">
                                    <option selected value="">Select one</option>
                                    @foreach ($projects as $project)
                                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                                    @endforeach
                                </select>
                                @error('selectedProject')
                                    <x-error>{{ $message }}</x-error>
                                @enderror
                            </div>

                            <div class="my-2 form-check">
                                <input class="form-check-input" disabled x-model='selectAllIndicators' type="checkbox"
                                    value="false" id="" />
                                <label class="form-check-label" for=""> Select All indicators </label>
                            </div>

                            <div class="indicators " x-show="!selectAllIndicators">

                                <div class="@if (!$selectedProject) pe-none opacity-25 @endif">


                                    <div class="mb-3 " wire:ignore x-init="() => {
                                    
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
                                                selectElement.append(newOption);
                                            });
                                            // Refresh Select2 to reflect changes
                                            selectElement.trigger('change');
                                    
                                    
                                            if (e.selected) {
                                                selectElement.val([e.selected]).trigger('change');
                                            }
                                    
                                    
                                    
                                            // setTimeout(() => {
                                            //     $wire.set('selectedIndicator', null);
                                            // }, 500)
                                    
                                    
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
                                    
                                    
                                    
                                        })
                                    }">
                                        <label for="" class="form-label">Select Indicator</label>
                                        <select x-ref="select" class="form-select "
                                            wire:model.debounce='selectedIndicator' id="select-indicators">
                                            <option selected value="">Select one</option>
                                            @foreach ($indicators as $indicator)
                                                <option value="{{ $indicator->id }}">
                                                    ({{ $indicator->indicator_no }})
                                                    {{ $indicator->indicator_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                @error('selectedIndicator')
                                    <x-error>{{ $message }}</x-error>
                                @enderror
                            </div>




                            <div class="mb-3 " x-show="!selectAllIndicators" wire:loading.class='opacity-25'
                                wire:target="selectedProject, selectedIndicator, selectedForm"
                                wire:loading.attr='disabled' x-data="{
                                    selectedForm: [],
                                    forms: [],
                                    setForms(forms) {
                                
                                        this.forms = forms;
                                        selected = $wire.selectedForm;
                                
                                        if (selected.length > 0 && selected != null) {
                                
                                            this.selectedForm = selected;
                                
                                        }
                                
                                
                                    },
                                
                                    selectForm() {
                                        $wire.selectedForm = this.selectedForm;
                                
                                
                                    }
                                
                                }" @change="selectForm()"
                                @changed-form.window="setForms($event.detail.forms)" x-init="">
                                <div class="@if (!$selectedIndicator) pe-none opacity-25 @endif">
                                    <label for="form-select" class="form-label">Choose Form</label>
                                    <select id="form-select"
                                        class="form-select form-select-md @if (!$selectedProject) pe-none opacity-25 @endif"
                                        multiple x-model="selectedForm">


                                        <template :key="form.id" x-for="form in forms">

                                            <option :value="form.id"> <span x-text="form.name"></span>
                                            </option>
                                        </template>

                                    </select>

                                </div>


                                @error('selectedForm')
                                    <x-error>{{ $message }}</x-error>
                                @enderror
                            </div>

                            <div class="@if (!$selectedProject) pe-none opacity-25 @endif">
                                <div class="mb-3" wire:loading.class='opacity-25'
                                    wire:target="selectedProject, selectedIndicator, selectedForm"
                                    wire:loading.attr='disabled'>
                                    <label for="month-select" class="form-label">Choose Reporting Period</label>
                                    <select id="month-select" class="form-select form-select-md"
                                        wire:model.live.debounce.700ms='selectedMonth'>
                                        <option value="">Select one</option>
                                        @foreach ($months as $index => $month)
                                            <option @if ($month->type === 'UNSPECIFIED') disabled @endif
                                                wire:key='{{ $month->id }}' value="{{ $month->id }}">
                                                {{ $month->start_month . '-' . $month->end_month }}
                                                ({{ $month->type }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('selectedMonth')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>
                            </div>

                            <div class="@if (!$selectedProject) pe-none opacity-25 @endif">
                                <div class="mb-3" wire:loading.class='opacity-25'
                                    wire:target="selectedProject, selectedIndicator, selectedForm"
                                    wire:loading.attr='disabled'>
                                    <label for="year-select" class="form-label">Choose Project Year</label>
                                    <select id="year-select" class="form-select form-select-md"
                                        wire:model.live.debounce.700ms='selectedFinancialYear'>
                                        <option value="">Select one</option>
                                        @foreach ($financialYears as $year)
                                            <option value="{{ $year->id }}">{{ $year->number }}</option>
                                        @endforeach
                                    </select>
                                    @error('selectedFinancialYear')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>
                            </div>
                            <div class="my-2 form-check">
                                <input class="form-check-input" disabled x-model='skipTargets' type="checkbox"
                                    value="false" id="" />
                                <label class="form-check-label" for=""> Skip Targets </label>
                            </div>


                            <!-- Dynamic Target Section -->
                            <div x-show="!selectAllIndicators " x-data="{
                                selectedProject: $wire.entangle('selectedProject'),
                                selectedIndicator: $wire.entangle('selectedIndicator'),
                                selectedForm: $wire.entangle('selectedForm'),
                                selectedMonth: $wire.entangle('selectedMonth'),
                                selectedFinancialYear: $wire.entangle('selectedFinancialYear'),
                                targets: $wire.entangle('targets'),
                                disaggregations: $wire.entangle('disaggregations'),
                                errors: @js($errors->toArray()),
                                isCipTargets: $wire.entangle('isCipTargets'),
                                checkValues() {
                                    if ((this.selectedIndicator && this.selectedFinancialYear)) {
                            
                                        $wire.getTargets();
                            
                                    }
                                },
                            
                            
                            
                                updateTargets() {
                                    $wire.getTargets();
                                }
                            
                            }" x-effect="checkValues();"
                                x-bind:class="{
                                    'opacity-25 pe-none': !(selectedIndicator && selectedFinancialYear)
                                }"
                                wire:loading.attr='disabled' @set-targets="updateTargets()">



                                <!-- Dynamically Adding Targets -->
                                <div class="mb-3">
                                    <h5>Define
                                        Targets</h5>

                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Target Name</th>
                                                <th>Target Value</th>
                                                <th>CIP Target Value (IF APPLICABLE)</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @foreach ($targets as $index => $target)
                                                <tr>
                                                    <td scope="row"> <select
                                                            class="form-select bg-light @error('targets.' . $index . '.name') is-invalid @enderror"
                                                            wire:model="targets.{{ $index }}.name"
                                                            wire:loading.attr='disabled'
                                                            wire:loading.class='opacity-25'>
                                                            <option value="">Select one</option>
                                                            @foreach ($disaggregations as $dsg)
                                                                <option
                                                                    @if ($dsg == $targets[$index]['name']) selected @endif
                                                                    value="{{ $dsg }}">{{ $dsg }}
                                                                </option>
                                                            @endforeach
                                                        </select>

                                                        @error('targets.' . $index . '.name')
                                                            <x-error>{{ $message }}</x-error>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input type="number"
                                                            class="form-control me-2 bg-light  @error('targets.' . $index . '.value') is-invalid @enderror"
                                                            placeholder="Target Value"
                                                            wire:model="targets.{{ $index }}.value" />
                                                        @error('targets.' . $index . '.value')
                                                            <x-error>{{ $message }}</x-error>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input type="number" :readonly="!isCipTargets"
                                                            class="form-control me-2   @error('cip_targets.' . $index . '.value') is-invalid @enderror"
                                                            placeholder="Target Value"
                                                            wire:model="cip_targets.{{ $index }}.value" />
                                                        @error('cip_targets.' . $index . '.value')
                                                            <x-error>{{ $message }}</x-error>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <!-- Button to add new target -->
                                                        <button class="btn btn-danger btn-sm"
                                                            wire:loading.attr='disabled'
                                                            wire:loading.class='opacity-25' type="button"
                                                            @click="$wire.removeTarget({{ $index }})">
                                                            Remove Target <span class="bx bx-trash"></span>
                                                        </button>

                                                    </td>
                                                </tr>
                                            @endforeach

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td>
                                                    <!-- Button to add new target -->
                                                    <button class="btn btn-warning btn-sm"
                                                        wire:loading.attr='disabled' wire:loading.class='opacity-25'
                                                        type="button" @click="$wire.addTarget()">
                                                        Add Target <span class="bx bx-plus"></span>
                                                    </button>
                                                </td>
                                            </tr>


                                        </tfoot>
                                    </table>




                                    <!-- General error for the targets array -->
                                    @error('targets')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>


                            </div>

                            <div class="mb-3">
                                <label for="start-period" class="form-label">Start of submissions</label>
                                <x-text-input :class="$errors->has('start_period') ? 'is-invalid' : ''" id="start-period"
                                    wire:model.debounce.700ms='start_period' type="date" />
                                @error('start_period')
                                    <x-error>{{ $message }}</x-error>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="end-period" class="form-label">End of submissions</label>
                                <x-text-input :class="$errors->has('end_period') ? 'is-invalid' : ''" id="end-period" wire:model.debounce.700ms='end_period'
                                    type="date" />
                                @error('end_period')
                                    <x-error>{{ $message }}</x-error>
                                @enderror
                            </div>

                            <div class="mb-3" x-data="{
                                selectedOrganisations: @entangle('selectedOrganisations'),
                                toggleSelection() {
                                    if (this.selectedOrganisations.includes('0')) {
                                        this.selectedOrganisations = ['0']; // Deselect others
                                    } else {
                                        this.selectedOrganisations = this.selectedOrganisations.filter(i => i !== '0'); // Remove '0' if others selected
                                    }
                                }
                            }" x-effect="toggleSelection()"
                                x-show="!selectAllIndicators">
                                <label for="" class="form-label">Organisations</label>
                                <select class="form-select form-select-md" x-model="selectedOrganisations" multiple>
                                    <option value="0">All</option>
                                    @foreach ($organisations as $organ)
                                        <option value="{{ $organ->id }}">{{ $organ->name }}</option>
                                    @endforeach
                                </select>

                            </div>


                            <div class="mb-3 " dir="ltr" x-data="{ switchOn: $wire.entangle('status'), row: $wire.entangle('rowId') }" x-show="row">

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

                            <button x-data class="btn btn-warning" type="submit" wire:loading.attr='disabled'
                                @click="  window.scrollTo({
                                            top: 0,
                                            behavior: 'smooth'
                                        })">Submit</button>
                            <button class="btn btn-outline-warning" type="button"
                                wire:click.debounce.1000ms='resetData' wire:loading.attr='disabled'>Reset</button>
                        </form>





                    </div>


                </div>
            </div>




        </div>

        <div class="row">

            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h5 class="card-title">Submission Period Table</h5>

                    </div>
                    <div class="px-0 card-body">
                        @php

                            $route = Route::current()->getPrefix();
                        @endphp
                        <livewire:tables.submission-period-table :currentRoutePrefix="$route">

                    </div>

                </div>


            </div>

        </div>
        @script
            <script>
                $('.goUp').on('click', () => {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    })
                });
            </script>
        @endscript
