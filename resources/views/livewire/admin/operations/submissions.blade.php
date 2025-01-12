<div>
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Submissions</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active">Submissions</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">

                <div>
                    @if (session()->has('success'))
                        <x-success-alert>{!! session()->get('success') !!}</x-success-alert>
                    @endif
                    @if (session()->has('error'))
                        <x-error-alert>{!! session()->get('error') !!}</x-error-alert>
                    @endif

                </div>

                <div class="card " wire:ignore>
                    <div class="card-header fw-bold ">
                        Submissions Table
                    </div>
                    <div class="card-body">
                        <!-- Nav tabs -->
                        @php
                            $batch = \App\Models\Submission::where('batch_type', 'batch')
                                ->where('status', 'pending')
                                ->count();
                            $manual = \App\Models\Submission::where('batch_type', 'manual')
                                ->where('status', 'pending')
                                ->count();
                            $aggregate = \App\Models\Submission::where('batch_type', 'aggregate')
                                ->where('status', 'pending')
                                ->count();

                        @endphp
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="batch-tab" data-bs-toggle="tab"
                                    data-bs-target="#batch-submission" type="button" role="tab"
                                    aria-controls="home" aria-selected="true">
                                    Batch Submissions <span
                                        class="badge bg-warning @if ($batch == 0) d-none @endif">{{ $batch }}</span>
                                </button>
                            </li>
                            {{-- <li class="nav-item" role="presentation">
                                <button class="nav-link" id="manual-tab" data-bs-toggle="tab"
                                    data-bs-target="#manual-submission" type="button" role="tab" aria-controls="profile"
                                    aria-selected="false">
                                    Manual Submissions
                                </button>
                            </li> --}}
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="people-tab" data-bs-toggle="tab"
                                    data-bs-target="#aggregate-submission" type="button" role="tab"
                                    aria-controls="profile" aria-selected="false">
                                    Aggregate Submission <span
                                        class="badge bg-warning @if ($aggregate == 0) d-none @endif">{{ $aggregate }}</span>
                                </button>
                            </li>

                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="progress-tab" data-bs-toggle="tab"
                                    data-bs-target="#job-progress" type="button" role="tab" aria-controls="profile"
                                    aria-selected="false">
                                    Pending Submissions
                                </button>
                            </li>

                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div wire:ignore class="mt-2 tab-pane active fade show" id="batch-submission"
                                role="tabpanel" aria-labelledby="home-tab">
                                <livewire:tables.submission-table :userId="auth()->user()->id" />
                            </div>
                            {{-- <div class="mt-2 tab-pane fade" id="manual-submission" role="tabpanel"
                                aria-labelledby="profile-tab">
                                <livewire:tables.submission-table :filter="'manual'" />
                            </div> --}}
                            <div wire:ignore class="mt-2 tab-pane  fade show" id="aggregate-submission" role="tabpanel"
                                aria-labelledby="profile-tab">
                                <livewire:tables.aggregate-submission-table :userId="auth()->user()->id" />
                            </div>

                            <div wire:ignore class="mt-2 tab-pane fade show" id="job-progress" role="tabpanel"
                                aria-labelledby="profile-tab">
                                <livewire:tables.job-progress-table :userId="auth()->user()->id" />
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>


        <div x-data x-init="$wire.on('showModal', (e) => {
            setTimeout(() => {
                $wire.dispatch('set', { id: e.rowId });
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


        $wire.on('deleteBatch', (e) => {
            setTimeout(() => {
                $wire.dispatch('set', { id: e.id });
                //  $wire.setData(e.rowId);
                const myModal = new bootstrap.Modal(document.getElementById(e.name), {})
                myModal.show();
            }, 500);


        })">




            <x-modal id="view-aggregate-modal" title="Update Submission">
                <div>


                    <div x-data="{
                        data: $wire.entangle('inputs'),



                    }">



                        <template x-for="(value, name) in data" :key="index">

                            <div class="mb-3">
                                <label for="" class="form-label" x-text="name"></label>
                                <input type="text" required class="form-control  " placeholder="Enter value"
                                    aria-describedby="helpId" :value="value" />
                                <div class="invalid-feedback">
                                    This field requires a value.
                                </div>

                            </div>


                        </template>



                    </div>

                    <div class="mt-4 mb-3">
                        <label for="">Comment</label>
                        <textarea wire:model="comment" rows="5" class=" form-control @error('comment') is-invalid @enderror"></textarea>

                        @error('comment')
                            <x-error>{{ $message }}</x-error>
                        @enderror
                    </div>


                    <div class="d-flex border-top-0 justify-content-center">
                        <form wire:submit="DisapproveAggregateSubmission">
                            <button type="submit" wire:loading.attr="disabled"
                                wire:target="DisapproveAggregateSubmission"
                                class="btn btn-theme-red me-2">Disapprove</button>
                        </form>
                        <form wire:submit="ApproveAggregateSubmission">
                            <button type="submit" wire:loading.attr="disabled" wire:target="ApproveAggregateSubmission"
                                class="btn btn-warning me-2">Approve</button>
                        </form>

                    </div>
                </div>
            </x-modal>
            <x-modal id="view-data-agg-modal" title="Approve Submission">

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

            <x-modal id="view-submission-modal" title="Approve Submission">
                <x-alerts />
                <h4 class="text-center h4">Please confirm whether you would like to approve/disapprove this record?
                </h4>

                <form wire:submit.debounce.1000ms="submit">
                    <div class="mt-4 mb-3">
                        <label for="">Comment</label>
                        <input wire:model="comment" class="form-control @error('comment') is-invalid @enderror" />
                        <small class="text-muted">Type <b>N/A</b> if no comment is available</small> <br>
                        @error('comment')
                            <x-error>{{ $message }}</x-error>
                        @enderror
                    </div>

                    <!-- Hidden input to store the status -->
                    <input type="hidden" wire:model="status">

                    <div class="d-flex border-top-0 justify-content-center">
                        <button type="button" wire:loading.attr="disabled" wire:target="save"
                            class="btn btn-theme-red me-2" wire:click="setStatus('denied')">Disapprove</button>
                        <button type="button" wire:loading.attr="disabled" wire:target="save"
                            class="btn btn-warning" wire:click="setStatus('approved')">Approve</button>
                    </div>
                </form>
            </x-modal>


            <x-modal id="delete-aggregate-modal" title="Delete Submission">
                <x-alerts />
                <h4 class="text-center h4">Please confirm whether you would like to delete this record?

                </h4>


                <form wire:submit='deleteAGG'>



                    <div class="d-flex border-top-0 justify-content-center mt-5">
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



                    <div class="d-flex border-top-0 justify-content-center mt-5">
                        <button type="button" wire:loading.attr="disabled" class="btn btn-secondary me-2"
                            data-bs-dismiss="modal">No, cancel</button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="deleteBatch"
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
        </script>
    @endscript

</div>
