<div>
    @section('title')
        View Reports
    @endsection
    <div class="container-fluid">
        <style>
            td {
                color: red;
            }
        </style>
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
            <x-table-card :title="'Submitted Reports Table'" :subtitle="'Reports Data'">
                <livewire:tables.submission-report-table />
            </x-table-card>
            </div>
        </div>



    </div>



</div>
