<div>
    @section('title')
        Targets
    @endsection

    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Indicator Targets</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active">Indicator Targets</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header ">
                        Targets Table
                    </div>
                    <div class="card-body">

                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="targets" data-bs-toggle="tab"
                                    data-bs-target="#main" type="button" role="tab" aria-controls="home-tab-pane"
                                    aria-selected="true">LOP
                                    Targets</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="assigned-targets" data-bs-toggle="tab"
                                    data-bs-target="#assigned" type="button" role="tab"
                                    aria-controls="profile-tab-pane" aria-selected="false">Assigned Targets</button>
                            </li>

                        </ul>

                        <div class="py-2 tab-content">
                            <div class="tab-pane fade show active" id="main" role="tabpanel"
                                aria-labelledby="home-tab" tabindex="0">

                                <livewire:tables.indicator-targets-table />
                            </div>
                            <div class="tab-pane fade" id="assigned" role="tabpanel" aria-labelledby="profile-tab"
                                tabindex="0">
                                <livewire:tables.assigned-target-table />
                            </div>

                        </div>


                    </div>
                </div>
            </div>
        </div>





    </div>

</div>
