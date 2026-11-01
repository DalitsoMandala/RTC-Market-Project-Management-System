<div>
    @section('title')
        Targets
    @endsection
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <x-breadcrumbs />
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">

                <x-table-card :title="'Targets'" :subtitle="'View all targets'" :isResponsive="false">
                    <livewire:targets.target-table />
                </x-table-card>

            </div>
        </div>
    </div>
