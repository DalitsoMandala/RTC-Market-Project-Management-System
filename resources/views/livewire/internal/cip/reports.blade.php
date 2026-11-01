<div>
    @section('title')
        Reports
    @endsection

    <div class="container-fluid">

        {{-- PAGE TITLE --}}
        <div class="row">
            <div class="col-12">
                <x-breadcrumbs />
            </div>
        </div>

        {{-- ALERTS --}}
        <div class="row">
            <div class="col">
                <x-alerts />
                <x-alpine-alerts />
                <div x-data="{
                    show: false,
                    message: null,
                    call() {
                        $wire.call('reload');
                    }
                }"
                    @export-fail.window="message = $event.detail.message; show = true; $wire.call('reload')"
                    @report-completed.window="show = false" x-show="show">

                    <div class="alert alert-danger alert-border-left" x-ref="warningAlert">

                        <strong>Notice!</strong>
                        <span x-text="message"></span>
                    </div>

                </div>

                <x-table-card :title="'Reports'" :subtitle="'Manage your project progress with system reports'">
                    <div>


                        <x-slot:actions>
                            <div x-data="{
                                exporting: false
                            }">

                                {{-- REFRESH BUTTON --}}
                                @hasanyrole('admin')
                                    <button
                                        :class="{
                                            'disabled': exporting === true
                                        }"
                                        @exporting-reports.window="exporting=true"
                                        @finished-reports.window="exporting=false"
                                        class="btn btn-warning @if ($loading) disabled @endif"
                                        wire:loading.attr="disabled" wire:target="load()" wire:click="load"
                                        wire:loading.attr="disabled">

                                        <i class="bx bx-refresh"></i>
                                        Refresh Reports
                                    </button>
                                @endhasanyrole

                            </div>
                        </x-slot:actions>


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
                        <div class="@if ($loading) pe-none opacity-50 @endif ">

                            <livewire:tables.rtc-market.report-table />

                        </div>

                    </div>
                </x-table-card>
            </div>
        </div>

        {{-- WARNING MESSAGE --}}





        {{-- REPORT CARD --}}


    </div>
</div>
@script
    <script>
        setTimeout(() => {
            $wire.dispatch('report-updated');
        }, 5000);
    </script>
@endscript
