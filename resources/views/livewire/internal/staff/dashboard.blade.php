<div>
    @section('title')
        Dashboard
    @endsection


    <div class="container-fluid" x-data="{
        dashboardOneShow: true,
        dashboardTwoShow: false,
        toggleDashboard(value) {
            if (value == 1) {
                this.dashboardOneShow = true;
                this.dashboardTwoShow = false;
            } else if (value == 2) {
                this.dashboardOneShow = false;
                this.dashboardTwoShow = true;
            }
        }
    }" @change-dashboard.window="toggleDashboard($event.detail.value)">

        @include('layouts.dashboard-layout')


        @if ($openSubmissions > 0)
            <div class="row">
                <div class="col">

                    <div class="alert alert-danger alert-dismissible fade show" role="alert">

                        <strong>Submission are open!</strong> Please submit your data/reports before the closing
                        dates. <a href="/external/submission-periods" class="alert-link text-decoration-underline">Click
                            Here</a>
                    </div>



                </div>
            </div>
        @endif
        <div x-show="dashboardOneShow" x-transition.duration.500ms>
            <livewire:dashboard-charts />


        </div>


        <div x-show="dashboardTwoShow" x-transition.duration.500ms>
            <livewire:dashboard-2-charts />
        </div>





    </div>

</div>
