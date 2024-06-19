<div class="row">
    <div class="card">
        <div class="card-body">
            <div class=" col-12">


                <div class="mb-3 d-none">

                    <label for="" class="form-label">Choose Project</label>
                    <select class="form-select form-select-md" wire:model="selectedProject" disabled>
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
                    wire:loading.attr='disabled'>

                    <label for="" class="form-label">Choose Reporting Period</label>



                    <select class="form-select form-select-md " wire:model='selectedMonth'>

                        <option value="">Select one</option>

                        <div x-data="{ selectedProject: $wire.entangle('selectedProject') }" x-show="selectedProject">
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
                    wire:loading.attr='disabled'>

                    <label for="" class="form-label">Choose Financial Year</label>
                    <select class="form-select form-select-md "wire:model='selectedFinancialYear'>

                        <option value="">Select one</option>
                        <div x-data="{ selectedProject: $wire.entangle('selectedProject') }" x-show="selectedProject">
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
