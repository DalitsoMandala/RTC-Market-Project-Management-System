<div>
    @section('title')
        Reports
    @endsection

    <div class="container-fluid">

        {{-- PAGE TITLE --}}
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <div class="page-title-left col-12">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="/">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">
                                Reporting
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- ALERTS --}}
        <div class="row">
            <div class="col">
                <x-alerts />
            </div>
        </div>

        {{-- WARNING MESSAGE --}}
        <div class="row">
            <div class="col">
                <div class="alert alert-success alert-border-left" x-ref="warningAlert" x-data x-init="() => {
                    let object = $($refs.warningAlert);
                    object.fadeTo(30000, 0).slideUp(500);
                }">

                    <strong>Notice!</strong>
                    You can now filter the report using filters inside the table.
                </div>
            </div>
        </div>

        {{-- REPORT CARD --}}
        <div class="card">

            <x-card-header>Reports</x-card-header>

            <div class="card-body">

                {{-- REFRESH BUTTON --}}
                @hasanyrole('admin')
                    <button class="btn btn-warning" wire:click="load" wire:loading.attr="disabled">

                        <i class="bx bx-refresh"></i>
                        Refresh Reports
                    </button>
                @endhasanyrole

            </div>

            <hr>

            {{-- PROGRESS AREA --}}
            <div wire:poll.3s="checkProgress">

                @if ($loading)
                    <div class="p-3 text-center">

                        <div class="fw-bold mb-2">
                            Updating reports... {{ $progress }}%
                        </div>

                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                                style="width: {{ $progress }}%">
                            </div>
                        </div>

                    </div>
                @endif

            </div>

            {{-- REPORT TABLE --}}
            <div class="@if ($loading) pe-none opacity-50 @endif">

                <livewire:tables.rtc-market.report-table />

            </div>

        </div>

    </div>
</div>
