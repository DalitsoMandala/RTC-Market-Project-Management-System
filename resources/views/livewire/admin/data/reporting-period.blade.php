<div>
    @section('title')
        Reporting periods
    @endsection
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Reporting periods</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active">
                                Reporting Periods</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
<x-alpine-alerts />
        <!-- Nav tabs -->
        <div class="card">
            <div class="px-0 card-body">

                <ul class="mx-2 nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home"
                            type="button" role="tab" aria-controls="home" aria-selected="true">
                            Reporting period
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile"
                            type="button" role="tab" aria-controls="profile" aria-selected="false">
                            Reporting period Details
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#financial_year"
                            type="button" role="tab" aria-controls="profile" aria-selected="false">
                            Project Years
                        </button>
                    </li>


                </ul>


                <!-- Tab panes -->
                <div class="mt-2 tab-content">
                    <div class="tab-pane active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <livewire:admin.reporting-period-table />
                    </div>

                    <div class="tab-pane" id="financial_year" role="tabpanel" aria-labelledby="profile-tab">
                        <livewire:admin.financial-year-table />
                    </div>
                    <div class="tab-pane" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <livewire:admin.reporting-months-table />
                    </div>

                </div>


            </div>
        </div>


    </div>







    {{-- <div x-data x-init="$wire.on('showModal', (e) => {

            const myModal = new bootstrap.Modal(document.getElementById(e.name), {})
            myModal.show();
        })">


        <x-modal id="view-indicator-modal" title="edit">
            <form>
                <div class="mb-3">

                    <x-text-input placeholder="Name of indicator..." />
                </div>

                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-warning">Save changes</button>

                </div>
            </form>
        </x-modal>

    </div> --}}




</div>

</div>
