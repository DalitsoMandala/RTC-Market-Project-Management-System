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


                                    <div class="mb-3" x-data="{ selected: $wire.entangle('Selected'), forms: @js($forms) }" wire:ignore>

                                        <label for="" class="form-label">Choose Form</label>
                                        <select class="form-select form-select-md" x-model="selected">
                                            <option selected>Select one</option>

                                            <template x-for="form in forms">
                                                <option :value="form.id" :key="form.id"> <span
                                                        x-text="form.name"></span>
                                                </option>
                                            </template>
                                        </select>

                                        @error('formSelected')
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
                                        x-data="{ switchOn: $wire.entangle('status') }">
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
                                </form>
                            </div>

                        </div>
                    </div>
                    <div class="card-body">
                        <livewire:submission-period-table>
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
