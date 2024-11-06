<div>
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">View Seed Beneficiaries</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active">View Seed Beneficiaries</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row" x-data="{
            showCard: false,
            toggleShow() {
                this.showCard = !this.showCard;
        
            }
        
        }" @close-form="showCard = false">

            <div class="col-12" x-show="showCard">
                <livewire:other-forms.seed-beneficiaries.add />
            </div>
            <div class="col-12">
                <div class="card ">
                    <div class="card-header align-items-center d-flex justify-content-between">
                        <h5 class="card-title">Seed Beneficiaries</h5>



                        <button class="btn btn-primary btn-sm" href="#" @click="toggleShow">Add Data +</button>

                    </div>
                    <div class="card-body">

                    </div>
                </div>
            </div>
        </div>



        {{-- <div x-data x-init="$wire.on('showModal', (e) => {

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
