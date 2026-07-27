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
                <x-alpine-alerts />
            </div>
        </div>

        {{-- WARNING MESSAGE --}}

        <div class="row" x-data="{
            show: false,
            message: null,
            call() {
                $wire.call('reload');
            }
        }"
            @export-fail.window="message = $event.detail.message; show = true; $wire.call('reload')"
            @report-completed.window="show = false" x-show="show">
            <div class="col">
                <div class="alert alert-danger alert-border-left" x-ref="warningAlert">

                    <strong>Notice!</strong>
                    <span x-text="message"></span>
                </div>
            </div>
        </div>



        {{-- REPORT CARD --}}
        <div class="card">

            <x-card-header>Reports</x-card-header>

            <div class="card-body" x-data="{
                exporting: false
            }">

                {{-- REFRESH BUTTON --}}
                @hasanyrole('admin')
                    <button :class="{
                        'disabled': exporting === true
                    }"
                        @exporting-reports.window="exporting=true" @finished-reports.window="exporting=false"
                        class="btn btn-warning @if ($loading) disabled @endif"
                        wire:loading.attr="disabled" wire:target="load()" wire:click="load" wire:loading.attr="disabled">

                        <i class="bx bx-refresh"></i>
                        Refresh Reports
                    </button>
                @endhasanyrole

            </div>

            <hr>

            {{-- PROGRESS AREA --}}


            @if ($loading)
                <div wire:poll.3s="checkProgress">
                    <div class="p-3 text-center">

                        <div class="mb-2 fw-bold">
                            Updating reports... {{ $progress }}%
                        </div>

                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                                role="progressbar" style="width: {{ $progress }}%">
                            </div>
                        </div>

                    </div>

                </div>
            @endif


            {{-- REPORT TABLE --}}
            <div class="@if ($loading) pe-none opacity-50 @endif p-5">

                <livewire:tables.rtc-market.report-table />

            </div>

        </div>

    </div>
</div>
@script
    <script>
        setTimeout(() => {
            $wire.dispatch('report-updated');
        }, 5000);
    </script>
@endscript
