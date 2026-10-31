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
                <x-table-card :title="'Contractual Agreement Table'" :subtitle="'Contractual Agreement Data'">

                    <livewire:tables.rtc-market.rtc-production-processors-conc-agreement />
                </x-table-card>
                <x-table-card :title="'Domestic Markets Table'" :subtitle="'Domestic Markets Data'">
                    <livewire:tables.rtc-market.rtc-production-processor-dom-markets />
                </x-table-card>
                <x-table-card :title="'International Markets Table'" :subtitle="'International Markets Data'">
                    <livewire:tables.rtc-market.rtc-production-processor-inter-markets />
                </x-table-card>
                <x-table-card :title="'Market Information Systems Table'" :subtitle="'Market Information Systems Data'">
                    <livewire:tables.rtc-market.rpm-processor-m-i-s />
                </x-table-card>
                <x-table-card :title="'Aggregation Centers Table'" :subtitle="'Aggregation Centers Data'">
                    <livewire:tables.rtc-market.rpm-processor-agg-centers />
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
