<div>
    @section('title')
        Submissions
    @endsection
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <x-breadcrumbs />
        </div>

        @php
            $routePrefix = Route::current()->getPrefix();
        @endphp

        <div class="row">
            <div class="col-12">
                <x-alerts />
                <x-table-card :title="'Batch Submissions'" :subtitle="'View all submissions'">
                    <livewire:tables.submission-table :routePrefix="$routePrefix" :tableName="'SubmissionTable'" :userId="auth()->user()->id" />
                </x-table-card>
                <x-table-card :title="'Aggregate Submissions'" :subtitle="'View aggregate submissions'">
                    <livewire:tables.aggregate-submission-table :routePrefix="$routePrefix" :tableName="'AggregateSubmissionTable'" :userId="auth()->user()->id" />
                </x-table-card>
                @hasanyrole('admin|manager|staff|enumerator')
                    <x-table-card :title="'Manual Submissions'" :subtitle="'View manual submissions'">
                        <livewire:tables.manual-submission-table :tableName="'ManualSubmissionTable'" :routePrefix="$routePrefix" :userId="auth()->user()->id" />
                    </x-table-card>
                    <x-table-card :title="'Market Data Submissions'" :subtitle="'View market data submissions'">
                        <livewire:tables.market-data-submission-table :routePrefix="$routePrefix" :userId="auth()->user()->id" />
                    </x-table-card>
                @endhasanyrole
                <x-table-card :title="'Job Progress'" :subtitle="'View job progress'">
                    {{-- <livewire:tables.job-progress-table :userId="auth()->user()->id" /> --}}
                </x-table-card>


            </div>
        </div>


        <div x-data x-init="$wire.on('showModal', (e) => {
            setTimeout(() => {
                $wire.dispatch('set', { id: e.id });
                //  $wire.setData(e.rowId);
                const myModal = new bootstrap.Modal(document.getElementById(e.name), {})
                myModal.show();
            }, 500);


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
        })

        $wire.on('showAggregate', (e) => {
            setTimeout(() => {
                $wire.dispatch('set', { id: e.id });
                //  $wire.setData(e.rowId);
                const myModal = new bootstrap.Modal(document.getElementById(e.name), {})
                myModal.show();
            }, 500);


        })

        $wire.on('showDataAggregate', (e) => {
                setTimeout(() => {
                    $wire.dispatch('set', { id: e.id });
                    //  $wire.setData(e.rowId);
                    const myModal = new bootstrap.Modal(document.getElementById(e.name), {})
                    myModal.show();
                }, 500);


            }),

            $wire.on('deleteAggregate', (e) => {
                setTimeout(() => {
                    $wire.dispatch('set', { id: e.id });
                    //  $wire.setData(e.rowId);
                    const myModal = new bootstrap.Modal(document.getElementById(e.name), {})
                    myModal.show();
                }, 500);


            })



        $wire.on('showMarket', (e) => {

            console.log(e);
            setTimeout(() => {
                $wire.dispatch('setMarket', { id: e.id });
                //  $wire.setData(e.rowId);
                const myModal = new bootstrap.Modal(document.getElementById(e.name), {})
                myModal.show();
            }, 500);


        })
        $wire.on('deleteBatch', (e) => {
            setTimeout(() => {
                $wire.dispatch('set', { id: e.id });
                //  $wire.setData(e.rowId);
                const myModal = new bootstrap.Modal(document.getElementById(e.name), {})
                myModal.show();
            }, 500);


        })


        $wire.on('deleteMarketBatch', (e) => {
            setTimeout(() => {
                $wire.dispatch('set', { id: e.id });
                //  $wire.setData(e.rowId);
                const myModal = new bootstrap.Modal(document.getElementById(e.name), {})
                myModal.show();
            }, 500);


        })

        $wire.on('deleteProgress', (e) => {
            setTimeout(() => {
                $wire.dispatch('set', { id: e.id });
                //  $wire.setData(e.rowId);
                const myModal = new bootstrap.Modal(document.getElementById(e.name), {})
                myModal.show();
            }, 500);


        })">




            <x-modal id="view-aggregate-modal" title="Update Submission">

                <x-alerts />

                <h4 class="text-center h4">Please confirm whether you would like to approve/disapprove this aggregate?
                </h4>

                <div>


                    <div x-data="{
                        data: $wire.entangle('inputs'),
                        isManager: $wire.entangle('isManager'),
                        disableInputs: false,
                        init() {


                            if (this.isManager) {
                                this.disableInputs = false;
                            } else {
                                this.disableInputs = true;

                            }

                        }

                    }">



                        <template x-for="(value, name) in data" :key="index">

                            <div class="mb-3">
                                <label for="" class="form-label" x-text="name"></label>
                                <input type="text" required class="form-control " placeholder="Enter value"
                                    :readonly="disableInputs" aria-describedby="helpId" :value="value" />
                                <div class="invalid-feedback">
                                    This field requires a value.
                                </div>

                            </div>


                        </template>



                    </div>



                    <input type="hidden" wire:model="status">
                    <div class="mt-4 mb-3">
                        <label for="">Comment</label>
                        <input wire:model='comment' class="  form-control @error('comment') is-invalid @enderror"
                            placeholder="Enter comment..." />
                        <small class="my-1 text-muted">Enter NA if no comment.</small><br />
                        @error('comment')
                            <x-error>{{ $message }}</x-error>
                        @enderror
                    </div>

                    <div class="mb-3 " dir="ltr" x-data="{ notify: $wire.entangle('notify'), }">

                        <label for="">Notify User</label>
                        <div class="square-switch d-flex align-items-baseline">
                            <input type="checkbox" x-model="notify" :checked="notify" id="square-switch4"
                                switch="none">
                            <label for="square-switch4" data-on-label="Yes" data-off-label="No"></label>

                        </div>


                    </div>


                    <div class="d-flex border-top-0 justify-content-center">
                        <form wire:submit="DisapproveAggregateSubmission">
                            <button type="submit" wire:loading.attr="disabled" class="btn btn-theme-red me-2"> <i
                                    class="bx bx-x-circle"></i> Disapprove</button>
                        </form>
                        <form wire:submit="ApproveAggregateSubmission">
                            <button type="submit" wire:loading.attr="disabled" class="btn btn-success me-2"> <i
                                    class="bx bx-check-double"></i> Approve</button>
                        </form>

                    </div>
                </div>
            </x-modal>
            <x-modal id="view-data-agg-modal" title="View Aggregates">

                <div x-data="{
                    data: $wire.entangle('inputs'),



                }">



                    <template x-for="(value, name) in data" :key="index">

                        <div class="mb-3">
                            <label for="" class="form-label" x-text="name"></label>
                            <input readonly type="text" required class="form-control bg-light "
                                placeholder="Enter value" aria-describedby="helpId" :value="value" />
                            <div class="invalid-feedback">
                                This field requires a value.
                            </div>

                        </div>


                    </template>



                </div>


            </x-modal>

            <x-modal id="view-submission-modal" title="Approve Batch Submission">
                <x-alerts />
                <h4 class="text-center h4">Please confirm whether you would like to approve/disapprove this record?
                </h4>


                <div class="mt-4 mb-3">
                    <label for="">Comment</label>

                    <input wire:model='comment' class="  form-control @error('comment') is-invalid @enderror"
                        placeholder="Enter comment..." />
                    <small class="my-1 text-muted">Enter NA if no comment.</small><br /> @error('comment')
                        <x-error>{{ $message }}</x-error>
                    @enderror
                </div>

                <!-- Hidden input to store the status -->
                <input type="hidden" wire:model="status">

                <div class="mb-3 " dir="ltr" x-data="{ notify: $wire.entangle('notify'), }">

                    <label for="">Notify User</label>
                    <div class="square-switch d-flex align-items-baseline">
                        <input type="checkbox" x-model="notify" :checked="notify" id="square-switch4"
                            switch="none">
                        <label for="square-switch4" data-on-label="Yes" data-off-label="No"></label>

                    </div>


                </div>
                <div class="d-flex border-top-0 justify-content-center">
                    <form wire:submit.debounce.1000ms="disapproveBatchSubmission">

                        <button type="submit" wire:loading.attr="disabled" class="btn btn-theme-red me-2"> <i
                                class="bx bx-x-circle"></i> Disapprove</button>

                    </form>


                    <form wire:submit.debounce.1000ms="approveBatchSubmission">

                        <button type="submit" wire:loading.attr="disabled" class="btn btn-success me-2"> <i
                                class="bx bx-check-double"></i> Approve</button>

                    </form>

                </div>


            </x-modal>


            <x-modal id="view-market-modal" title="Approve Market Data Submission">
                <x-alerts />
                <h4 class="text-center h4">Please confirm whether you would like to approve/disapprove this record?
                </h4>


                <input type="hidden" wire:model="status">
                <div class="mt-4 mb-3">
                    <label for="">Comment</label>
                    <input wire:model='comment' class="  form-control @error('comment') is-invalid @enderror"
                        placeholder="Enter comment..." />
                    <small class="my-1 text-muted">Enter NA if no comment.</small><br />
                    @error('comment')
                        <x-error>{{ $message }}</x-error>
                    @enderror
                </div>
                <div class="mb-3 " dir="ltr" x-data="{ notify: $wire.entangle('notify'), }">

                    <label for="">Notify User</label>
                    <div class="square-switch d-flex align-items-baseline">
                        <input type="checkbox" x-model="notify" :checked="notify" id="square-switch4"
                            switch="none">
                        <label for="square-switch4" data-on-label="Yes" data-off-label="No"></label>

                    </div>


                </div>

                <div class="mt-5 d-flex border-top-0 justify-content-center">
                    <form wire:submit.debounce.1000ms="DisapproveMarketSubmission">

                        <button type="submit" wire:loading.attr="disabled" class="btn btn-theme-red me-2"> <i
                                class="bx bx-x-circle"></i> Disapprove</button>

                    </form>


                    <form wire:submit.debounce.1000ms="ApproveMarketSubmission">

                        <button type="submit" wire:loading.attr="disabled" class="btn btn-success me-2"> <i
                                class="bx bx-check-double"></i> Approve</button>

                    </form>

                </div>

            </x-modal>


            <x-modal id="delete-aggregate-modal" title="Delete Submission">
                <x-alerts />
                <h4 class="text-center h4">Please confirm whether you would like to delete this record?

                </h4>


                <form wire:submit='deleteAGG'>

                    <div class="mb-3 " dir="ltr" x-data="{ notify: $wire.entangle('notify'), }">

                        <label for="">Notify User</label>
                        <div class="square-switch d-flex align-items-baseline">
                            <input type="checkbox" x-model="notify" :checked="notify" id="square-switch4"
                                switch="none">
                            <label for="square-switch4" data-on-label="Yes" data-off-label="No"></label>

                        </div>


                    </div>

                    <div class="mt-5 d-flex border-top-0 justify-content-center">
                        <button type="button" wire:loading.attr="disabled" class="btn btn-secondary me-2"
                            data-bs-dismiss="modal">No, cancel</button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="deleteAGG"
                            class="btn btn-theme-red">Yes, I'm sure</button>
                    </div>
                </form>
            </x-modal>

            <x-modal id="delete-batch-modal" title="Delete Submission">
                <x-alerts />
                <h4 class="text-center h4">Please confirm whether you would like to delete this record?

                </h4>


                <form wire:submit='deleteBatch'>

                    <div class="mb-3 " dir="ltr" x-data="{ notify: $wire.entangle('notify'), }">

                        <label for="">Notify User</label>
                        <div class="square-switch d-flex align-items-baseline">
                            <input type="checkbox" x-model="notify" :checked="notify" id="square-switch4"
                                switch="none">
                            <label for="square-switch4" data-on-label="Yes" data-off-label="No"></label>

                        </div>


                    </div>

                    <div class="mt-5 d-flex border-top-0 justify-content-center">
                        <button type="button" wire:loading.attr="disabled" class="btn btn-secondary me-2"
                            data-bs-dismiss="modal">No, cancel</button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="deleteBatch"
                            class="btn btn-theme-red">Yes, I'm sure</button>
                    </div>
                </form>
            </x-modal>

            <x-modal id="delete-market-modal" title="Delete Market Data Submission">
                <x-alerts />
                <h4 class="text-center h4">Please confirm whether you would like to delete this record?

                </h4>


                <form wire:submit='deleteMarket'>

                    <div class="mb-3 " dir="ltr" x-data="{ notify: $wire.entangle('notify'), }">

                        <label for="">Notify User</label>
                        <div class="square-switch d-flex align-items-baseline">
                            <input type="checkbox" x-model="notify" :checked="notify" id="square-switch4"
                                switch="none">
                            <label for="square-switch4" data-on-label="Yes" data-off-label="No"></label>

                        </div>


                    </div>

                    <div class="mt-5 d-flex border-top-0 justify-content-center">
                        <button type="button" wire:loading.attr="disabled" class="btn btn-secondary me-2"
                            data-bs-dismiss="modal">No, cancel</button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="deleteMarketBatch"
                            class="btn btn-theme-red">Yes, I'm sure</button>
                    </div>
                </form>
            </x-modal>
            <x-modal id="delete-progress-modal" title="Delete Submission">
                <x-alerts />
                <h4 class="text-center h4">Please confirm whether you would like to delete this record?

                </h4>

                <form wire:submit='deleteProgress'>
                    <label for="">Please type "delete" to confirm</label>
                    <input type="text" wire:model='confirmDeleteProgress'
                        class="form-control @error('confirmDeleteProgress') is-invalid @enderror" />
                    <small class=" text-danger">This action cannot be undone</small>


                    @error('confirmDeleteProgress')
                        <x-error>{{ $message }}</x-error>
                    @enderror


                    <div class="mb-3 " dir="ltr" x-data="{ notify: $wire.entangle('notify'), }">

                        <label for="">Notify User</label>
                        <div class="square-switch d-flex align-items-baseline">
                            <input type="checkbox" x-model="notify" :checked="notify" id="square-switch4"
                                switch="none">
                            <label for="square-switch4" data-on-label="Yes" data-off-label="No"></label>

                        </div>


                    </div>
                    <div class="mt-5 d-flex border-top-0 justify-content-center">
                        <button type="button" wire:loading.attr="disabled" class="btn btn-secondary me-2"
                            data-bs-dismiss="modal">No, cancel</button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="deleteProgress"
                            class="btn btn-theme-red">Yes, I'm sure</button>
                    </div>
                </form>
            </x-modal>



        </div>



    </div>

    @script
        <script>
            if (window.location.hash !== '') {
                const button = document.querySelector(`button[data-bs-target='${window.location.hash}']`);
                if (button) {
                    button.click();

                }
            }

            const getUserRole = @json(auth()->user()->getRoleNames()->first());
            if (getUserRole === 'enumerator') {
                const button = document.querySelector(`button[data-bs-target='#market-submission']`);

                if (button) {
                    setTimeout(() => {
                        button.click();
                    })


                }
            }
        </script>
    @endscript

</div>
