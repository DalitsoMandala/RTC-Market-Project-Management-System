<div>
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Indicator Targets</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active">Manage indicator Targets</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">

                <x-alerts />
                <div class="card ">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title">Indicator Targets Table</h5>
                        <button class="btn btn-warning disabled" wire:click="$dispatch('add-form')">Add <i
                                class="bx bx-plus"></i></button>
                    </div>

                    <div class="card-header" x-data="{ showForm: false }" x-init="() => {

                        $wire.on('show-form', (e) => {
                            showForm = true;
                            window.scrollTo({
                                top: 0,
                                behavior: 'smooth'
                            })
                        })

                        $wire.on('add-form', (e) => {
                            showForm = true;
                            window.scrollTo({
                                top: 0,
                                behavior: 'smooth'
                            })
                        })
                    }" x-show="showForm">

                        <form wire:submit.debounce.500ms='save' wire:loading.class='opacity-25'
                            wire:target='save,addForm'>


                            <div class="indicators ">



                                <div class="mb-3 " wire:ignore x-init="$('#select-indicators').select2({
                                    width: '100%',
                                    theme: 'bootstrap-5',
                                    containerCssClass: 'select2--small',
                                    dropdownCssClass: 'select2--small',
                                });

                                $('#select-indicators').on('change', function() {
                                    data = $(this).val();

                                    $wire.selectedIndicator = data;

                                })
                                $wire.on('add-form', (e) => {
                                    $('#select-indicators').val(['']);
                                    $('#select-indicators').change();
                                })

                                $wire.on('show-form', (e) => {
                                    $('#select-indicators').val([e.indicator_id]);
                                    $('#select-indicators').change();
                                })">
                                    <label for="" class="form-label">Select Indicator</label>
                                    <select class="form-select " id="select-indicators">
                                        <option selected value="">Select one</option>
                                        @foreach ($indicators as $indicator)
                                            <option value="{{ $indicator->id }}">
                                                ({{ $indicator->indicator_no }})
                                                {{ $indicator->indicator_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>



                                @error('selectedIndicator')
                                    <x-error>{{ $message }}</x-error>
                                @enderror
                            </div>


                            <div class="mb-3">
                                <label for="financial_year_id" class="form-label">Financial Year</label>
                                <select wire:model="financial_year_id" disabled id="financial_year_id"
                                    class="form-select" required>
                                    <option value="">Select Project Year</option>
                                    @foreach ($financial_years as $year)
                                        <option value="{{ $year->id }}">{{ $year->number }}</option>
                                    @endforeach
                                </select>
                                @error('financial_year_id')
                                    <x-error>{{ $message }}</x-error>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="project_id" class="form-label">Project</label>
                                <select wire:model="project_id" disabled id="project_id" class="form-select" required>
                                    <option value="">Select Project</option>
                                    @foreach ($projects as $project)
                                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                    <x-error>{{ $message }}</x-error>
                                @enderror
                            </div>


                            <div class="mb-3">
                                <label for="target_value" class="form-label">Target Value</label>
                                <input type="number" wire:model="target_value" id="target_value"
                                    class="form-control   @if ($type === 'detail') bg-light @endif" @if ($type === 'detail') readonly @endif>
                                @error('target_value')
                                    <x-error>{{ $message }}</x-error>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="baseline_value" class="form-label">Baseline Value</label>
                                <input type="number" wire:model="baseline_value" id="baseline_value"
                                    class="form-control @if ($type === 'detail') bg-light @endif" @if ($type === 'detail')
                                    readonly @endif>
                                @error('baseline_value')
                                    <x-error>{{ $message }}</x-error>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="type" class="form-label">Type</label>
                                <select wire:model.live.debounce.1s="type" id="type" class="form-select">
                                    <option value="">Select Type</option>
                                    <option value="number">Number</option>
                                    <option value="percentage">Percentage</option>
                                    <option value="detail">Detail</option>
                                </select>
                                @error('type')
                                    <x-error>{{ $message }}</x-error>
                                @enderror
                            </div>



                            <!-- Conditionally Show Target Details Form -->
                            @if ($type === 'detail')
                                <h4>Target Details</h4>

                                @error('targetDetails')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                                @foreach ($targetDetails as $index => $detail)
                                    <div class="border p-3 mb-3" wire:key="target-detail-{{ $index }}">
                                        <div class="mb-3">
                                            <label for="targetDetails.{{ $index }}.name" class="form-label">Name</label>
                                            <input type="text" wire:model="targetDetails.{{ $index }}.name"
                                                id="targetDetails.{{ $index }}.name" class="form-control" required>
                                            @error('targetDetails.' . $index . '.name')
                                                <x-error>{{ $message }}</x-error>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="targetDetails.{{ $index }}.target_value" class="form-label">Target
                                                Value</label>
                                            <input type="number" wire:model="targetDetails.{{ $index }}.target_value"
                                                id="targetDetails.{{ $index }}.target_value" class="form-control" required>
                                            @error('targetDetails.' . $index . '.target_value')
                                                <x-error>{{ $message }}</x-error>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="targetDetails.{{ $index }}.type" class="form-label">Type</label>
                                            <select wire:model="targetDetails.{{ $index }}.type"
                                                id="targetDetails.{{ $index }}.type" class="form-select" required>
                                                <option value="number">Number</option>
                                                <option value="percentage">Percentage</option>
                                            </select>
                                            @error('targetDetails.' . $index . '.type')
                                                <x-error>{{ $message }}</x-error>
                                            @enderror
                                        </div>

                                        <button type="button" class="btn btn-danger"
                                            wire:click="removeTargetDetail({{ $index }})">Remove</button>
                                    </div>
                                @endforeach

                                <button type="button" class="btn btn-secondary mt-3" wire:click="addTargetDetail">Add
                                    Target
                                    Detail</button>
                            @endif

                            <div class="form-group my-2">
                                <button type="submit" x-data x-on:click="window.scrollTo({
        top: 0,
        behavior: 'smooth'
    })" class="btn btn-warning goUp">Save changes</button>
                            </div>




                        </form>
                    </div>
                    <div class="card-body">

                        <livewire:admin.indicator-target-table />
                    </div>
                </div>
            </div>
        </div>



        <div x-data x-init="$wire.on('showModal', (e) => {

            const myModal = new bootstrap.Modal(document.getElementById(e.name), {})
            myModal.show();
        })
        $wire.on('hideModal', (e) => {
            const modals = document.querySelectorAll('.modal.show');

            // Iterate over each modal and hide it using Bootstrap's modal hide method
            modals.forEach(modal => {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            });
        })">


            <x-modal id="view-modal" title="Edit record">

            </x-modal>

        </div>




    </div>

</div>