<div>

    @section('title')
        Manage Products Data
    @endsection
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Manage Products Data</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active">Manage Data</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <ul class=" nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="raw-import-tab" data-bs-toggle="tab"
                            data-bs-target="#raw-import" type="button" role="tab" aria-controls="home"
                            aria-selected="true">
                            RAW IMPORTS
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link " id="raw-export-tab" data-bs-toggle="tab" data-bs-target="#raw-export"
                            type="button" role="tab" aria-controls="home" aria-selected="true">
                            RAW EXPORTS
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link " id="processed-import-tab" data-bs-toggle="tab"
                            data-bs-target="#processed-import" type="button" role="tab" aria-controls="home"
                            aria-selected="true">
                            PROCESSED IMPORTS
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link " id="processed-export-tab" data-bs-toggle="tab"
                            data-bs-target="#processed-export" type="button" role="tab" aria-controls="home"
                            aria-selected="true">
                            PROCESSED EXPORTS
                        </button>
                    </li>



                </ul>
                <div class="card ">

                    <div class="card-body">
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="raw-import" role="tabpanel"
                                aria-labelledby="raw-import-tab">
                                <livewire:tables.root-tuber-processor-import-table />
                                <!-- Add your form or data table here -->
                            </div>
                            <div class="tab-pane fade" id="raw-export" role="tabpanel" aria-labelledby="raw-export-tab">
                                <p>Form fields for <strong>RAW EXPORTS</strong> go here.</p>
                                <!-- Add your form or data table here -->
                            </div>
                            <div class="tab-pane fade" id="processed-import" role="tabpanel"
                                aria-labelledby="processed-import-tab">
                                <p>Form fields for <strong>PROCESSED IMPORTS</strong> go here.</p>
                                <!-- Add your form or data table here -->
                            </div>
                            <div class="tab-pane fade" id="processed-export" role="tabpanel"
                                aria-labelledby="processed-export-tab">
                                <p>Form fields for <strong>PROCESSED EXPORTS</strong> go here.</p>
                                <!-- Add your form or data table here -->
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>



        {{--  <div x-data x-init="$wire.on('showModal', (e) => {

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
        })
        ">


            <x-modal id="view-indicator-modal" title="edit">
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

        </div> --}}




    </div>

</div>
