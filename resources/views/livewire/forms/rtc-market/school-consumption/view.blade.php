<div>
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Dashboard</h4>

                    <div class="page-title-right" wire:ignore>
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="/cip/forms">Forms</a></li>
                            <li class="breadcrumb-item active">View</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->


        <div class="row">


            <div class="col-12">


                <x-alerts />

                <div class="card">
                    <div class="card-header">
                        <h4 class="text-center text-warning text-uppercase">School consumption data @if ($batch_no)
                                [Batch : {{ $batch_no }}]
                            @endif
                        </h4>
                    </div>

                    <div class="card-body" id="#datatable">
                        <livewire:tables.rtc-market.school-consumption-table />

                    </div>
                </div>

            </div>
        </div>






    </div>

</div>
