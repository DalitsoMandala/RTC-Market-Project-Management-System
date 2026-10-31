@section('title')
    Reporting periods
@endsection

<div>
    <div class="container-fluid">
        <div class="row">
            <x-breadcrumbs />

            <div class="col-12">
                <div>


                    <x-alpine-alerts />

                    {{-- ============================ Project Years ============================ --}}
                    <x-table-card title="Project Years" subtitle="Manage project years" :isResponsive="false">
                        <livewire:admin.financial-year-table />
                    </x-table-card>
                    {{-- ============================ Reporting Periods ============================ --}}
                    <x-table-card title="Reporting Periods" subtitle="Manage project reporting periods" :isResponsive="false">
                        <livewire:admin.reporting-period-table />
                    </x-table-card>


                </div>
            </div>
        </div>
    </div>
</div>
