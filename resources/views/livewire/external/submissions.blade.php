<div>
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Dashboard</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active">Page Name</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="home-tab" data-bs-toggle="tab"
                                    data-bs-target="#batch" type="button" role="tab" aria-controls="home"
                                    aria-selected="true">
                                    Batch Submissions
                                </button>
                            </li>
                            {{-- <li class="nav-item" role="presentation">
                                <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#sub"
                                    type="button" role="tab" aria-controls="profile" aria-selected="false">
                                    Individual Submissions
                                </button>
                            </li> --}}

                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div class="mt-2 tab-pane active" id="batch" role="tabpanel" aria-labelledby="home-tab">

                            </div>
                            {{-- <div class="tab-pane" id="sub" role="tabpanel" aria-labelledby="profile-tab">
                                <livewire:external.row-submission-table />
                            </div> --}}

                        </div>

                    </div>
                </div>
            </div>
        </div>







    </div>

</div>
