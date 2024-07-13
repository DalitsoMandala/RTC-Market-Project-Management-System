<div>
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Submissions</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
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
                    <div class="card-body">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="batch-tab" data-bs-toggle="tab"
                                    data-bs-target="#batch-submission" type="button" role="tab"
                                    aria-controls="home" aria-selected="true">
                                    Batch Submissions
                                </button>
                            </li>
                            {{-- <li class="nav-item" role="presentation">
                                <button class="nav-link" id="manual-tab" data-bs-toggle="tab"
                                    data-bs-target="#manual-submission" type="button" role="tab"
                                    aria-controls="profile" aria-selected="false">
                                    Manual Submissions
                                </button>
                            </li> --}}
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="people-tab" data-bs-toggle="tab"
                                    data-bs-target="#aggregate-submission" type="button" role="tab"
                                    aria-controls="profile" aria-selected="false">
                                    Aggregate Submission
                                </button>
                            </li>

                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div wire:ignore class="mt-2 tab-pane active fade show" id="batch-submission"
                                role="tabpanel" aria-labelledby="home-tab">
                                <livewire:tables.submission-table :filter="'batch'" />
                            </div>
                            {{-- <div class="mt-2 tab-pane fade" id="manual-submission" role="tabpanel"
                                aria-labelledby="profile-tab">
                                <livewire:tables.submission-table :filter="'manual'" />
                            </div> --}}
                            <div wire:ignore class="mt-2 tab-pane fade-show" id="aggregate-submission" role="tabpanel"
                                aria-labelledby="profile-tab">
                                <livewire:tables.submission-table :filter="'aggregate'" />
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
        
        
        })">




            <x-modal id="view-aggregate-modal" title="Approve Submission">
                <form wire:submit.debounce.1000ms='save'>
                    <h3 class="text-center h4">Please confirm wether you would like to approve/disapprove this
                        record?
                    </h3>

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
                    <div class="mt-4 mb-3">
                        <label for="">Comment</label>
                        <input wire:model='comment' class="form-control @error('comment')is-invalid @enderror" />
                        <small class="text-muted">type <b>N/A</b> if no comment is available</small> <br>
                        @error('comment')
                            <x-error>{{ $message }}</x-error>
                        @enderror
                    </div>

                    <div class="modal-footer border-top-0 justify-content-center" x-data="{
                        statusGet(status) {
                            $wire.status = status;
                            $wire.saveAGG();
                        }
                    
                    }">
                        <hr>


                        <button type="button" @click="statusGet('denied')" class="btn btn-danger">Disapprove</button>
                        <button type="button" @click="statusGet('approved')"
                            class="pr-4 btn btn-primary">Approve</button>

                    </div>
                </form>
            </x-modal>


            <x-modal id="view-submission-modal" title="Approve Submission">
                <form wire:submit.debounce.1000ms='save'>
                    <h3 class="text-center h4">Please confirm wether you would like to approve/disapprove this
                        record?
                    </h3>

                    <div class="mt-4 mb-3">
                        <label for="">Comment</label>
                        <input wire:model='comment' class="form-control @error('comment')is-invalid @enderror" />
                        <small class="text-muted">type <b>N/A</b> if no comment is available</small> <br>
                        @error('comment')
                            <x-error>{{ $message }}</x-error>
                        @enderror
                    </div>

                    <div class="modal-footer border-top-0 justify-content-center" x-data="{
                        statusGet(status) {
                            $wire.status = status;
                            $wire.save();
                        }
                    
                    }">
                        <button type="button" @click="statusGet('denied')" class="btn btn-danger">Disapprove</button>
                        <button type="button" @click="statusGet('approved')"
                            class="pr-4 btn btn-primary">Approve</button>

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
