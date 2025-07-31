<div>

    @section('title')
        Manage Gross Margins
    @endsection
    <div class="container-fluid">


        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Manage Gross Margins</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active">Manage Gross Margins</li>

                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <x-alerts />

        <ul class=" nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="batch-tab" data-bs-toggle="tab" data-bs-target="#normal"
                    type="button" role="tab" aria-controls="home" aria-selected="true">
                    VIEW GROSS MARGIN DATA
                </button>
            </li>




        </ul>
        <div class="card ">

            <div class="card-body">
                <livewire:tables.gross-margin-table />
            </div>
        </div>



    </div>

</div>
