<div>
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Reporting</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active">Reports</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <div class="card ">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="" class="form-label">Project</label>
                                    <select class="form-select form-select-sm" name="" id="">
                                        <option selected>Select one</option>
                                        <option value="">New Delhi</option>
                                        <option value="">Istanbul</option>
                                        <option value="">Jakarta</option>
                                    </select>
                                </div>

                            </div>
                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="" class="form-label">Indicator</label>
                                    <select class="form-select form-select-sm" multiple name="" id="">
                                        <option selected>Select one</option>
                                        <option value="">New Delhi</option>
                                        <option value="">Istanbul</option>
                                        <option value="">Jakarta</option>
                                    </select>
                                </div>

                            </div>
                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="" class="form-label">Starting Period</label>
                                    <input class="form-control form-control-sm" />
                                </div>

                            </div>
                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="" class="form-label">Ending Period</label>
                                    <input class="form-control form-control-sm" />
                                </div>

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <livewire:tables.reporting-table />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>








    </div>

</div>
