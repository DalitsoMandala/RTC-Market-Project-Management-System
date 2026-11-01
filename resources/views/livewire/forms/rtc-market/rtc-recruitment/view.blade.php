<div>
    @section('title')
        View Recruitment Data
    @endsection
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
         <x-breadcrumbs />
        </div>
        <!-- Tab panes -->

<div class="row">
    <div class="col-12">
        <x-table-card :title="'RTC Actor Recruitment'" :subtitle="'Recruitment Data'">
             <livewire:tables.rtc-market.recruitments-table />
        </x-table-card>

    </div>
</div>



    </div>
