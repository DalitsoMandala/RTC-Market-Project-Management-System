<!-- end page title -->
<div class="row">
    <div class="col-12">
        <h3 class="mb-5 text-center text-warning">@yield('form-title')</h3>

        <x-alerts />

        @if (!$targetSet)
            <livewire:forms.rtc-market.set-targets-form :submissionTargetIds="$targetIds" />
        @endif

        @if ($openSubmission === false)
            <div class="alert alert-danger" role="alert">
                You can not submit a form right now
                because submissions are closed for the moment!
            </div>
        @endif



        <div class="mb-1 row justify-content-center  @if ($openSubmission === false) opacity-25  pe-none @endif"
            x-data="{
                selectedFinancialYear: $wire.entangle('selectedFinancialYear').live,
                selectedMonth: $wire.entangle('selectedMonth').live,
                selectedIndicator: $wire.entangle('selectedIndicator').live,
            }">



            <div class="col-12 col-md-8">
                <form wire:submit='save'>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                Add Data
                            </h3>
                        </div>
                        <div class="card-body">
                            @yield('form-content')
                        </div>
                    </div>

                </form>
            </div>




        </div>

    </div>
</div>
