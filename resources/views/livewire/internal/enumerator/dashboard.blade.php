<div>
    @section('title')
        Dashboard
    @endsection


    <div class="container-fluid" x-data="{
        dashboardOneShow: true,
        dashboardTwoShow: false,
        dashboardThreeShow: false,
        toggleDashboard(value) {
            if (value == 1) {
                this.dashboardOneShow = true;
                this.dashboardTwoShow = false;
                this.dashboardThreeShow = false;
            } else if (value == 2) {
                this.dashboardOneShow = false;
                this.dashboardTwoShow = true;
                this.dashboardThreeShow = false;
            }else if (value == 3) {
                this.dashboardOneShow = false;
                this.dashboardTwoShow = false;
                this.dashboardThreeShow = true;
            }
        }
    }" @change-dashboard.window="toggleDashboard($event.detail.value)" x-init="() => {
    const userRole = @js(Auth::user()->getRoleNames()->first());
    if(userRole == 'enumerator') {
        toggleDashboard(3);
    }
    }">

        @include('layouts.dashboard-layout')



        <div x-show="dashboardOneShow" x-transition.duration.500ms>
            <livewire:dashboard-charts />


        </div>


        <div x-show="dashboardTwoShow" x-transition.duration.500ms>
            <livewire:dashboard-2-charts />
        </div>





    </div>

</div>
