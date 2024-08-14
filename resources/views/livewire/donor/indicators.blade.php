<div>

    <style>

    </style>
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row ">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Indicators</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active">Indicators</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row animate__animated animate__fadeIn">
            <div class="col-12">
                <x-alerts />
                <div class="card">
                    <div class="card-header fw-bold">
                        Indicators Table
                    </div>
                    <div class="card-body">
                        <livewire:tables.indicatorTable :userId="auth()->user()->id" />
                    </div>
                </div>
            </div>
        </div>









    </div>

</div>
