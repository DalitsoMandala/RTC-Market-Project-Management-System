<div>

    @section('title')
        Gross Margin Categories
    @endsection
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Gross Margin Categories</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active">Manage Gross Margin Categories/Items</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <x-tab-component>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="category-tab" data-bs-toggle="tab"
                            data-bs-target="#category" type="button" role="tab" aria-controls="home"
                            aria-selected="true">
                            Categories
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link " id="item-tab" data-bs-toggle="tab" data-bs-target="#item"
                            type="button" role="tab" aria-controls="home" aria-selected="true">
                            Items
                        </button>
                    </li>
                </x-tab-component>
                <div class="card ">

                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="category" role="tabpanel" aria-labelledby="category-tab">
                                <livewire:tables.gross-margin-category-table />
                            </div>

                            <div class="tab-pane" id="item" role="tabpanel" aria-labelledby="item-tab">
                                <livewire:tables.gross-margin-cat-item-table />
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
