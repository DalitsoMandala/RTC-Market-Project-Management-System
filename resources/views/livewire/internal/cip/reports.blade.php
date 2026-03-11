<div>
    @section('title')
        Reports
    @endsection
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">


                    <div class="page-title-left col-12">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active">Reporting</li>
                        </ol>
                    </div>

                </div>
            </div>

        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col">
                <x-alerts />
                <div class="alert alert-success alert-border-left" x-ref="warningAlert" x-data x-init="() => {

                    let object = $($refs.warningAlert);
                    object.fadeTo(30000, 0).slideUp(500);
                }">
                    <strong>Notice!</strong>
                    Please note that we have updated this section of the system. You can now filter the report using the
                    filters inside the table.
                </div>
            </div>
        </div>


            <div class="row" x-data="{
            message: null
            }" @export-fail.window="message = $event.detail.message" x-show="message">
                <div class="col">
                    <div class="alert alert-warning alert-border-left">
                        <strong>Notice!</strong>
                        <span x-text="message"></span>
                    </div>
                </div>
            </div>



        <div class="card ">
            <x-card-header>Reports</x-card-header>

            <div class=" card-body">


                <hr>

                @if ($loadingData)
                    <div class="p-2 my-2 row">
                        <div class="col-12 d-flex justify-content-center align-items-center">
                            <span class="fw-bold me-2">Updating data... Please wait</span>
                            <div wire:poll.5000ms='readCache'
                                class="d-flex justify-content-center align-items-center">
                                <div class="spinner-border text-warning spinner-border-sm" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif




                <div x-data="{ show: $wire.entangle('loadingData') }" :class="{ 'pe-none opacity-25': show === true }">
                    <livewire:tables.rtc-market.report-table />
                </div>








            </div>
        </div>

    </div>











</div>

