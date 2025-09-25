<div>
    @section('title')
        Dashboard
    @endsection
    <div class="container-fluid">

        @include('layouts.dashboard-layout')
        @if ($openSubmissions > 0)
            <div class="row">
                <div class="col">

                    <div class="alert alert-danger alert-dismissible fade show" role="alert">

                        <strong>Submission are open!</strong> Please submit your data/reports before the closing
                        dates. <a href="/external/submission-period" class="alert-link text-decoration-underline">Click
                            Here</a>
                    </div>



                </div>
            </div>
        @endif

        <livewire:dashboard-charts />



    </div>


</div>
