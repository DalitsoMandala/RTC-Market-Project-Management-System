<div>
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-end">


                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="../../">Forms</a></li>
                            <li class="breadcrumb-item active">View</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                @if (session()->has('success'))
                    <x-success-alert>{!! session()->get('success') !!}</x-success-alert>
                @endif
                @if (session()->has('error'))
                    <x-error-alert>{!! session()->get('error') !!}</x-error-alert>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h4 class="text-center text-primary text-uppercase">RTC PRODUCTION AND MARKETING FORM DATA FOR
                            FARMERS @if ($batch_no)
                                [Batch : {{ $batch_no }}]
                            @endif
                        </h4>
                    </div>
                    <div class="card-body" id="#datatable">

                        <livewire:tables.rtc-market.rtc-production-farmers-table :userId="auth()->user()->id" />
                    </div>
                </div>

            </div>
        </div>







    </div>

</div>
