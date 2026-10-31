<div>
    @section('title')
        View Attendance Register Data
    @endsection
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
               <x-table-card :title="'Attendance Register Table'" :subtitle="'Attendance Register Data'">
                <livewire:tables.rtc-market.attendance-register-table />
               </x-table-card>
            </div>
        </div>




    </div>

</div>
