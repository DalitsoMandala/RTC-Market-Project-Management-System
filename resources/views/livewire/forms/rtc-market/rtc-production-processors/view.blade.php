<div>
    @section('title')
        View Processors Data
    @endsection
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <x-table-card :title="'RTC Production Processors Table'" :subtitle="'RTC Processors Data'">
                    <livewire:tables.rtc-market.rtc-production-processors-table :key="'rpm1'" :userId="auth()->user()->id"
                        :routePrefix="Route::current()->getPrefix()" />

                </x-table-card>
             
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
