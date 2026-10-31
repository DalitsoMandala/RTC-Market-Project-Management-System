<div>
    @section('title')
        Dashboard
    @endsection
    <div class="container-fluid">
        @if ($openSubmissions > 0)
            <div class="row my-2">
                <div class="col">

                    <div class="alert alert-danger alert-border-left " role="alert" >
  <strong>Submission are open!</strong> Please submit your data/reports before the closing
                        dates. <a href="/external/submission-period" class="alert-link text-decoration-underline">Click
                            Here</a>

</div>


                </div>
            </div>
        @endif
        @include('layouts.dashboard-layout')


        <livewire:dashboard-charts />



    </div>


</div>
