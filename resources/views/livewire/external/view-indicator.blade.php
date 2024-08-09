<div>
    @inject('indicatorService', 'App\Services\IndicatorService')
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">View Indicator</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active">Indicators</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <div class="card ">
                    <div class="card-header">
                        <h4>{{ $indicator_no }} - {{ $indicator_name }}</h4>
                    </div>
                    <div class="card-body">


                        @php
                            $component = $indicatorService->getComponent($indicator_no, $project_name);
                        @endphp

                        @if ($component)
                            @livewire($component, ['indicator_no' => $indicator_no, 'indicator_name' => $indicator_name, 'indicator_id' => $indicator_id, 'project_id' => $project_id])
                        @endif
                    </div>
                </div>
            </div>
        </div>




    </div>



</div>

</div>
