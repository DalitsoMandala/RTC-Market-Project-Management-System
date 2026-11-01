<div>

    @section('title')
        View Production Marketing Log Data
    @endsection
    <div class="my-2 container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
              <x-table-card :title="'Production Marketing Log Table'" :subtitle="'Production marketing log data'">
                <livewire:tables.rtc-market.production-marketing-log-table />
              </x-table-card>
            </div>
        </div>




    </div>

</div>
