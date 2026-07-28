<div x-data x-init="$wire.on('show-indicator-modal', () => {
    const myModal = new bootstrap.Modal(document.getElementById('indicator-crud-modal'), {});
    myModal.show();
});
$wire.on('show-delete-indicator-modal', (e) => {
    const myModal = new bootstrap.Modal(document.getElementById('indicator-delete-modal'), {});
    myModal.show();

})

$wire.on('hideModal', (e) => {
    const modalEl = document.getElementById(e.name ?? 'indicator-crud-modal');
    const modalInstance = bootstrap.Modal.getInstance(modalEl);
    if (modalInstance) {
        modalInstance.hide();
    }
});">

    <x-modal id="indicator-crud-modal" :title="$indicatorId ? 'Edit Indicator' : 'Add Indicator'">
        <form wire:submit='save'>

            <div class="mb-3">
                <label for="indicator_no" class="form-label">Indicator #</label>
                <x-text-input id="indicator_no" placeholder="e.g. B1" wire:model="indicator_no" />
                @error('indicator_no')
                    <span class="my-1 text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label for="indicator_name" class="form-label">Indicator Name</label>
                <x-text-input id="indicator_name" placeholder="Name of indicator..." wire:model="indicator_name" />
                @error('indicator_name')
                    <span class="my-1 text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label for="project-select" class="form-label">Project</label>
                <select id="project-select" class="form-select form-select-md" wire:model="project_id">
                    <option value="">Select one</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
                @error('project_id')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>



            <div class="mb-1" wire:ignore x-data="{ selectedOrder: [] }" x-init="const $el = $('#formDisaggregations');
            
            $el.select2({
                width: '100%',
                theme: 'bootstrap-5',
                containerCssClass: 'select2--small',
                dropdownCssClass: 'select2--small',
                tags: true,
                tokenSeparators: [','],
                createTag: function(params) {
                    var term = $.trim(params.term);
                    if (term === '') return null;
                    return { id: term, text: term, newTag: true };
                },
            });
            
            // Helper: reorder the <option> elements in the DOM to match a given order.
            // Select2 renders chips in DOM order, so this is what actually controls
            // what the user sees AND what gets read back into selectedOrder later.
            function reorderOptions(order) {
                order.forEach(function(id) {
                    let option = $el.find('option').filter(function() {
                        return $(this).val() === id;
                    });
            
                    if (option.length === 0) {
                        // Value doesn't exist yet as an <option> (e.g. a tag pushed
                        // from the backend that isn't in $disaggregationOptions) — create it.
                        option = $(new Option(id, id, false, false));
                        $el.append(option);
                    }
            
                    // Moving it re-appends (not duplicates) — pushes it to the end,
                    // so options end up in exactly the order of `order`.
                    $el.append(option);
                });
            }
            
            // 1. Capture selection chronologically, reorder DOM to match, sync to Livewire
            $el.on('select2:select', function(e) {
                const id = e.params.data.id;
                if (!selectedOrder.includes(id)) {
                    selectedOrder.push(id);
                }
                reorderOptions(selectedOrder);
                $el.trigger('change.select2'); // re-render chips without re-firing select2:select
                $wire.selectedDisaggregations = selectedOrder;
            });
            
            // 2. Remove items seamlessly on unselect
            $el.on('select2:unselect', function(e) {
                const id = e.params.data.id;
                selectedOrder = selectedOrder.filter(item => item !== id);
                $wire.selectedDisaggregations = selectedOrder;
            });
            
            // 3. Keep backend data events working correctly, in the given order
            $wire.on('select-partners', (e) => {
                selectedOrder = e.data3 || [];
                reorderOptions(selectedOrder);
                $el.val(selectedOrder).trigger('change');
            });">
                <label class="form-label">Disaggregations</label>
                <small class="mb-1 d-block text-muted">
                    Pick from the existing list, or type a new one and press enter to create it for this indicator.
                </small>
                <select class="form-select form-select-md" multiple id="formDisaggregations">
                    @foreach ($disaggregationOptions as $disagg)
                        <option value="{{ $disagg->name }}">{{ $disagg->name }}</option>
                    @endforeach
                </select>
            </div>
            @error('selectedDisaggregations')
                <x-error>{{ $message }}</x-error>
            @enderror


            <div>

                <button type="button" wire:loading.attr="disabled"
                    class="mt-2 btn btn-warning btn-sm @if (!$indicatorId) d-none @endif"
                    wire:click="restoreDisaggregations">
                    Restore default disaggregations
                </button>

            </div>
            @if ($fileLocation)
                <div class="mt-3 mb-0 alert alert-light">
                    <strong>File location:</strong> {{ $fileLocation }}
                    <br>
                    <span
                        class="badge @if ($file_exists) bg-success-subtle text-success @else
                   bg-danger-subtle text-danger @endif
                    ">
                        @if ($file_exists)
                            File exists
                        @else
                            File does not exist
                        @endif
                    </span>
                </div>
            @endif

            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-warning">
                    {{ $indicatorId ? 'Save changes' : 'Create indicator' }}
                </button>
            </div>
        </form>
    </x-modal>
    <x-modal id="indicator-delete-modal" title="Delete Indicator">
        <x-alerts />
        <h4 class="text-center h4">Please confirm whether you would like to delete this Indicator
            ({{ $indicator_name }})?</h4>
        <p class="text-center">This action cannot be undone, and all associated data will be permanently removed.</p>


        <form>

            <div class="mb-3">
                <p>Type "DELETE" to confirm</p>
                <input type="text" class="form-control @error('deleteConfirm') is-invalid @enderror"
                    wire:model="deleteConfirm" aria-describedby="helpId" />
                @error('deleteConfirm')
                    <x-error>{{ $message }}</x-error>
                @enderror
            </div>


            <div class="mt-5 d-flex border-top-0 justify-content-center">
                <button type="button" wire:loading.attr="disabled" class="btn btn-secondary me-2"
                    data-bs-dismiss="modal">No, cancel</button>
                <button type="button" wire:click="delete({{ $indicatorId }})" wire:loading.attr="disabled"
                    wire:target="delete" class="btn btn-theme-red">Yes, I'm sure</button>
            </div>
        </form>
    </x-modal>
</div>
