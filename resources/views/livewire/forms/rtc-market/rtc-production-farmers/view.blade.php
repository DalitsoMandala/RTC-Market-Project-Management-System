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
