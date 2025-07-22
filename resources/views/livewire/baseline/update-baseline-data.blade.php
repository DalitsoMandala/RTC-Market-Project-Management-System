<div>
    @section('title')
        Update Baseline Values
    @endsection

    <div class="container-fluid">
        <!-- start page title -->
        <div class="my-2 row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Update Baseline Values</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active">Update Baseline Values</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <x-alerts />
        <!-- end page title -->

        <div class=" row">
            <div class="col-12">
                <ul class=" nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="batch-tab" data-bs-toggle="tab" data-bs-target="#normal"
                            type="button" role="tab" aria-controls="home" aria-selected="true">
                            BASELINE DATA
                        </button>
                    </li>




                </ul>
                <div class="card">

                    <div class=" card-body">


                        <livewire:tables.baseline-table />
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
        })">




            <x-modal id="view-baseline-modal" title="Update Submission">

                <x-alerts />

                <h4 class="text-center h4">Please confirm whether you would like to update this baseline?
                </h4>


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

            </x-modal>

        </div>
    </div>
    @script
        <script>
            $wire.on('submit-form', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        </script>
    @endscript
