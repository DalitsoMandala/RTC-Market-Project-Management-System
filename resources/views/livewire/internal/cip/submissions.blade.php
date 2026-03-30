<div>
    @section('title')
        Submissions
    @endsection
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">


                    <div class="page-title-left col-12">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active">Submissions</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>


        <div>
            <x-alerts />
        </div>

        <div class="card">
            <x-card-header>Submissions</x-card-header>


            <div class="card-body">

                <!-- Tabs -->
                <ul class="nav nav-tabs" id="submissionTabs" role="tablist">

                    <li class="nav-item" role="presentation">
                        <button class="nav-link active @hasanyrole('enumerator') disabled @endhasanyrole" id="batch-tab"
                            data-bs-toggle="tab" data-bs-target="#batch-submission" type="button" role="tab"
                            aria-controls="batch-submission" aria-selected="true">
                            Batch Submissions
                            <span class="badge bg-theme-red @if ($batch == 0) d-none @endif">
                                {{ $batch }}
                            </span>
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link @hasanyrole('enumerator') disabled @endhasanyrole" id="aggregate-tab"
                            data-bs-toggle="tab" data-bs-target="#aggregate-submission" type="button" role="tab"
                            aria-controls="aggregate-submission" aria-selected="false">
                            Aggregate Submission (Reports)
                            <span class="badge bg-theme-red @if ($aggregate == 0) d-none @endif">
                                {{ $aggregate }}
                            </span>
                        </button>
                    </li>

                    @hasanyrole('admin|manager|staff|enumerator')
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="manual-tab" data-bs-toggle="tab"
                                data-bs-target="#manual-submission" type="button" role="tab"
                                aria-controls="manual-submission">
                                Manual Submission
                            </button>
                        </li>

                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="market-tab" data-bs-toggle="tab"
                                data-bs-target="#market-submission" type="button" role="tab"
                                aria-controls="market-submission">
                                Market Data Submission
                                <span class="badge bg-theme-red @if ($market == 0) d-none @endif">
                                    {{ $market }}
                                </span>
                            </button>
                        </li>

                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="additional-report-tab" data-bs-toggle="tab"
                                data-bs-target="#additional-report" type="button" role="tab"
                                aria-controls="additional-report">
                                Additional Report Submission
                            </button>
                        </li>
                    @endhasanyrole

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="progress-tab" data-bs-toggle="tab"
                            data-bs-target="#submission-progress" type="button" role="tab"
                            aria-controls="submission-progress">
                            Pending Submissions
                            <span class="badge bg-theme-red @if ($pendingJob == 0) d-none @endif">
                                {{ $pendingJob }}
                            </span>
                        </button>
                    </li>

                </ul>

                <!-- Tab content -->
                <div class="mt-3 tab-content">
                    @php

                        $route = Route::current()->getPrefix();
                    @endphp
                    <div class="tab-pane fade show active" id="batch-submission" role="tabpanel"
                        aria-labelledby="batch-tab" wire:ignore>
                        <livewire:tables.submission-table :tableName="'SubmissionTable'" :routePrefix="$route" :userId="auth()->user()->id" />
                    </div>

                    <div class="tab-pane fade" id="aggregate-submission" role="tabpanel" aria-labelledby="aggregate-tab"
                        wire:ignore>
                        <livewire:tables.aggregate-submission-table :routePrefix="$route" :tableName="'AggregateSubmissionTable'" :userId="auth()->user()->id" />
                    </div>

                    <div class="tab-pane fade" id="manual-submission" role="tabpanel" aria-labelledby="manual-tab"
                        wire:ignore>
                        <livewire:tables.manual-submission-table :tableName="'ManualSubmissionTable'" :routePrefix="$route"
                            :userId="auth()->user()->id" />
                    </div>

                    <div class="tab-pane fade" id="market-submission" role="tabpanel" aria-labelledby="market-tab"
                        wire:ignore>
                        <livewire:tables.market-data-submission-table :routePrefix="$route" :userId="auth()->user()->id" />
                    </div>

                    <div class="tab-pane fade" id="submission-progress" role="tabpanel" aria-labelledby="progress-tab"
                        wire:ignore>
                        <livewire:tables.job-progress-table :userId="auth()->user()->id" />
                    </div>

                    <div class="tab-pane fade" id="additional-report" role="tabpanel"
                        aria-labelledby="additional-report-tab" wire:ignore x-data="{ show: false }">

                        <div class="gap-2 mb-3 d-flex">
                            <button class="btn btn-warning" :disabled="show" @click="show=true">
                                Import Report
                            </button>

                            <button class="btn btn-secondary" x-show="show" @click="show=false">
                                Close
                            </button>
                        </div>

                        <div x-show="show" class="mb-3">
                            <livewire:imports.import-data />
                            <hr>
                        </div>

                        <livewire:tables.additional-report-table />
                    </div>

                </div>

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
