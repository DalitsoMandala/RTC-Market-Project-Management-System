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

                @if (session()->has('success'))
                    <x-success-alert>{!! session()->get('success') !!}</x-success-alert>
                @endif
                @if (session()->has('error'))
                    <x-error-alert>{!! session()->get('error') !!}</x-error-alert>
                @endif
                <div class="card " wire:ignore>
                    <div class="card-header fw-bold ">
                        Submissions Table
                    </div>
                    <div class="card-body">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link disabled" id="batch-tab" data-bs-toggle="tab"
                                    data-bs-target="#batch-submission" type="button" role="tab" aria-controls="home"
                                    aria-selected="true">
                                    Batch Submissions
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
                                <button class="nav-link active" id="people-tab" data-bs-toggle="tab"
                                    data-bs-target="#aggregate-submission" type="button" role="tab"
                                    aria-controls="profile" aria-selected="false">
                                    Aggregate Submission
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
                            <div wire:ignore class="mt-2 tab-pane  fade show" id="batch-submission" role="tabpanel"
                                aria-labelledby="home-tab">
                                <livewire:tables.submission-table :userId="auth()->user()->id" />
                            </div>
                            {{-- <div class="mt-2 tab-pane fade" id="manual-submission" role="tabpanel"
                                aria-labelledby="profile-tab">
                                <livewire:tables.submission-table :filter="'manual'" />
                            </div> --}}
                            <div wire:ignore class="mt-2 tab-pane active fade show" id="aggregate-submission"
                                role="tabpanel" aria-labelledby="profile-tab">
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


        $wire.on('showDataAggregate', (e) => {
            setTimeout(() => {
                $wire.dispatch('set', { id: e.id });
                //  $wire.setData(e.rowId);
                const myModal = new bootstrap.Modal(document.getElementById(e.name), {})
                myModal.show();
            }, 500);


        })">

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

            <x-modal id="view-submission-modal" title="update status">
                <form wire:submit='save'>

                    <div class="mb-3" x-data="{
                        status: $wire.entangle('status')
                    }">



                        <select class="form-select form-select-sm" x-model="status">
                            <option> Select one</option>
                            <option value="approved">Approved</option>
                            <option value="denied">Denied</option>

                        </select>
                        <small class="text-muted">You can approve/disapprove submissions here</small><br>
                        @error('status')
                            <span class="my-1 text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="">Comments</label>
                        <textarea wire:model='comment' id="" class="form-control"></textarea>
                        @error('comment')
                            <x-error>{{ $message }}</x-error>
                        @enderror
                    </div>

                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>

                    </div>
                </form>
            </x-modal>

        </div>



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