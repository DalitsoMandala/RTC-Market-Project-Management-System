<div>
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Dashboard</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active">Manage Page Name</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">

                <x-alerts />
                <div class="card ">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title">Assigned Targets Table</h5>
                        <button class="btn btn-warning " wire:click="$dispatch('add-form')">Add <i
                                class="bx bx-plus"></i>
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


                        <form wire:submit.prevent="save" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="indicator_id" class="form-label">Indicator Target</label>
                                <div x-data="{
                                    rowId: $wire.entangle('rowId'),
                                }" x-init="$('#select-indicators').select2({
                                    width: '100%',
                                    theme: 'bootstrap-5',
                                    containerCssClass: 'select2--small',
                                    dropdownCssClass: 'select2--small',
                                });

                                $('#select-indicators').on('change', function() {
                                    data = $(this).val();

                                    $wire.indicator_id = data;

                                })
                                $wire.on('add-form', (e) => {
                                    $('#select-indicators').val(['']);
                                    $('#select-indicators').change();
                                })

                                $wire.on('show-form', (e) => {
                                    $('#select-indicators').val([e.indicator_id]);
                                    $('#select-indicators').change();
                                })" wire:ignore>
                                    <select id="select-indicators" class="form-select" required
                                        :disabled="rowId != null">
                                        <option value="">Select Indicator Target</option>
                                        @foreach ($indicatorTargets as $indicatorTarget)
                                            <option value="{{ $indicatorTarget->id }}">
                                                {{ $indicatorTarget->indicator->indicator_name }}</option>
                                        @endforeach
                                    </select>

                                </div>

                                @error('indicator_id')
                                    <x-error>{{ $message }}</x-error>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="financial_year_id" class="form-label">Project Year</label>
                                <select wire:model.live.debounce.600ms="selectedFinancialYear" id="financial_year_id"
                                    wire:loading.class='pe-none bg-light opacity-25'
                                    class="form-select @if ($rowId) pe-none bg-light @endif"
                                    required>
                                    <option value="">Select Financial Year</option>
                                    @foreach ($financial_years as $year)
                                        <option value="{{ $year->id }}">{{ $year->number }}</option>
                                    @endforeach
                                </select>
                                @error('selectedFinancialYear')
                                    <x-error>{{ $message }}</x-error>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="project_id" class="form-label">Project</label>
                                <select wire:model.live.debounce.600ms="selectedProject" id="project_id"
                                    wire:loading.class='pe-none bg-light opacity-25'
                                    class="form-select @if ($rowId) pe-none bg-light @endif"
                                    required>
                                    <option value="">Select Project</option>
                                    @foreach ($projects as $project)
                                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                                    @endforeach
                                </select>
                                @error('selectedProject')
                                    <x-error>{{ $message }}</x-error>
                                @enderror
                            </div>

                            <div class=" mb-3">
                                <div class="row">
                                    <div class="col">

                                        <label for="target_value" class="form-label">LOP Target Value</label>
                                        <p class="fw-bold p-2 border bg-light rounded-1 ">
                                            @if ($lop_type != 'detail')
                                                {{ $lop_target_value }}{{ $lop_type == 'percentage' ? '%' : '' }}
                                            @else
                                                {!! $lop_details !!}
                                            @endif
                                        </p>

                                    </div>


                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="organisation_id" class="form-label">Organisation</label>
                                <select wire:model.live.debounce.600ms="organisation_id" id="organisation_id"
                                    class="form-select" wire:loading.class='pe-none bg-light opacity-25' required>
                                    <option value="">Select Organisation</option>
                                    @foreach ($organisations as $organisation)
                                        <option value="{{ $organisation->id }}">{{ $organisation->name }}</option>
                                    @endforeach
                                </select>
                                @error('organisation_id')
                                    <x-error>{{ $message }}</x-error>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="target_value" class="form-label">Target Value</label>
                                <input type="number" wire:model="target_value" id="target_value"
                                    class="form-control @if ($type == 'detail') bg-light @endif"
                                    @if ($type == 'detail') readonly @endif required>
                                @error('target_value')
                                    <x-error>{{ $message }}</x-error>
                                @enderror
                            </div>

                            <div class="mb-3 d-none">
                                <label for="current_value" class="form-label">Current Value</label>
                                <input type="number" readonly wire:model="current_value" id="current_value"
                                    class="form-control">
                                @error('current_value')
                                    <x-error>{{ $message }}</x-error>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="type" class="form-label">Type</label>
                                <select wire:model.live.debounce.600ms="type" id="type" class="form-select"
                                    required>
                                    <option value="">Select Type</option>
                                    <option value="number">Number</option>
                                    <option value="percentage">Percentage</option>
                                    <option value="detail">Detail</option>
                                </select>
                                @error('type')
                                    <x-error>{{ $message }}</x-error>
                                @enderror
                            </div>

                            <!-- Conditionally Show Detail Form -->
                            @if ($type === 'detail')
                                <hr>
                                <h4>Details</h4>
                                @foreach ($detail as $index => $item)
                                    <div class="border p-3 mb-3" wire:key="detail-{{ $index }}">
                                        <div class="mb-3">
                                            <label for="detail.{{ $index }}.name"
                                                class="form-label">Name</label>
                                            <input type="text" wire:model="detail.{{ $index }}.name"
                                                id="detail.{{ $index }}.name" class="form-control" required>
                                            @error('detail.' . $index . '.name')
                                                <x-error>{{ $message }}</x-error>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="detail.{{ $index }}.target_value"
                                                class="form-label">Value</label>
                                            <input type="number"
                                                wire:model="detail.{{ $index }}.target_value"
                                                id="detail.{{ $index }}.target_value" class="form-control"
                                                required>
                                            @error('detail.' . $index . '.target_value')
                                                <x-error>{{ $message }}</x-error>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="detail.{{ $index }}.type"
                                                class="form-label">Type</label>
                                            <select wire:model="detail.{{ $index }}.type"
                                                id="detail.{{ $index }}.type" class="form-select" required>
                                                <option value="number">Number</option>
                                                <option value="percentage">Percentage</option>
                                            </select>
                                            @error('detail.' . $index . '.type')
                                                <x-error>{{ $message }}</x-error>
                                            @enderror
                                        </div>

                                        <button type="button" class="btn btn-theme-red"
                                            wire:click="removeDetail({{ $index }})"
                                            @if (count($detail) <= 1) disabled @endif>Remove</button>
                                    </div>
                                @endforeach



                                <button type="button" class="btn btn-secondary mt-3" wire:click="addDetail">Add
                                    Detail</button>
                            @endif

                            <!-- Base Switchs -->
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch"
                                    wire:model.live.debounce.600ms='showDelete' id="flexSwitchCheckDefault">
                                <label class="form-check-label text-danger fs-6" for="flexSwitchCheckDefault">Confirm
                                    delete this assigned data. This is remove all assigned target from all
                                    partners/organisations (Warning: This action can not be undone!)
                                </label>
                            </div>



                            <button type="button" wire:click="resetForm"
                                onclick="$('#select-indicators').val(['']);
                                    $('#select-indicators').change();"
                                class="btn btn-secondary mt-3">Reset &
                                Add</button>
                            <button type="button" class="btn btn-theme-red mt-3" wire:click='deleteData'>Delete
                                Assign
                                Target</button>
                            <button type="submit" class="btn btn-warning mt-3 goUp">Submit</button>
                        </form>

                    </div>
                    <div class="card-body">
                        <livewire:admin.assigned-targets-table />
                    </div>
                </div>
            </div>
        </div>


        <div x-data x-init="$wire.on('showModal', (e) => {

            const myModal = new bootstrap.Modal(document.getElementById(e.name), {})
            myModal.show();
        })
        $wire.on('refresh', (e) => {
            const modals = document.querySelectorAll('.modal.show');

            // Iterate over each modal and hide it using Bootstrap's modal hide method
            modals.forEach(modal => {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            });
        })">


            <x-modal id="view-indicator-modal" title="edit">
                <form>
                    <div class="mb-3">

                        <x-text-input placeholder="Name of indicator..." />
                    </div>

                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-warning">Save changes</button>

                    </div>
                </form>
            </x-modal>

        </div>




    </div>

</div>
