<div>

    @section('title')
        View Rtc Consumption Data
    @endsection
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
         <div class="col-12">
            <x-table-card :title="'RTC Consumption Table'" :subtitle="'RTC Consumption Data'">
                <livewire:tables.rtc-market.rtc-consumption-table />
            </x-table-card>
         </div>
        </div>






    </div>

</div>
