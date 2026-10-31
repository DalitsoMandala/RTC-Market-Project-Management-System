<div>
    @section('title')
        View Farmers Data
    @endsection
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <x-table-card :title="'RTC Production Farmers Table'" :subtitle="'RTC Production Farmers Data'">
                    <livewire:tables.rtc-market.rtc-production-farmers-table />
                </x-table-card>
                <x-table-card :title="'Contractual Agreement Table'" :subtitle="'Contractual Agreement Data'">
                    <livewire:tables.rtc-market.rtc-production-farmers-conc-agreement />
                </x-table-card>
                <x-table-card :title="'Domestic Markets Table'" :subtitle="'Domestic Markets Data'">
                    <livewire:tables.rtc-market.rtc-production-farmers-dom-markets />
                </x-table-card>
                <x-table-card :title="'International Markets Table'" :subtitle="'International Markets Data'">
                    <livewire:tables.rtc-market.rtc-production-farmers-inter-markets />
                </x-table-card>
                <x-table-card :title="'Aggregation Centers Table'" :subtitle="'Aggregation Centers Data'">
                    <livewire:tables.rtc-market.rpm-farmer-agg-centers />
                </x-table-card>
                <x-table-card :title="'MIS Table'" :subtitle="'MIS Data'">
                    <livewire:tables.rtc-market.rpm-farmer-m-i-s />
                </x-table-card>
                <x-table-card :title="'Basic Information Table'" :subtitle="'Basic Information Data'">
                    <livewire:tables.rtc-market.rpm-farmer-basic />
                </x-table-card>
                <x-table-card :title="'Certified Table'" :subtitle="'Certified Data'">
                    <livewire:tables.rtc-market.rpm-farmer-certified />
                </x-table-card>
                <x-table-card :title="'Cultivation Table'" :subtitle="'Cultivation Data'">
                    <livewire:tables.rtc-market.rpm-farmer-cultivation />
                </x-table-card>
                <x-table-card :title="'Seed Registration Table'" :subtitle="'Seed Registration Data'">
                    <livewire:tables.farmer-seed-registration-table />
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
