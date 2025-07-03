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
    }">

        @include('layouts.dashboard-layout')

        <div x-if="dashboardOneShow">
            dashboard1
        </div>


        <div x-if="dashboardOneTwo">
            dashboard2
        </div>
        <livewire:dashboard-charts />



    </div>


</div>
