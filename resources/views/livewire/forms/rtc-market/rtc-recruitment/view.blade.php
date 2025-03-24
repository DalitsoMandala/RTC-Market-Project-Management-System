<div>
    @section('title')
        View Farmers Data
    @endsection
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">


                    <h4 class="mb-0">View Data</h4>

                    <div class="page-title-right" wire:ignore>
                        @php
                            $routePrefix = \Illuminate\Support\Facades\Route::current()->getPrefix();
                        @endphp
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ $routePrefix }}/forms">Forms</a></li>
                            <li class="breadcrumb-item active">View Data</li>
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
                        <h4 class="text-center text-warning text-uppercase">RTC ACTOR RECRUITMENT Table
                            @if ($batch_no)
                                [Batch : {{ $batch_no }}]
                            @endif
                        </h4>
                    </div>
                    <div class="px-0 card-body" id="#datatable">
                        <livewire:tables.rtcmarket.recruitments-table />
                    </div>

                </div>
            </div>







        </div>

        @script
            <script>
                if (window.location.hash !== '') {
                    const button = document.querySelector(`button[data-bs-target='${window.location.hash}']`);
                    if (button) {
                        button.click();

                    }
                }
            </script>
        @endscript
    </div>
