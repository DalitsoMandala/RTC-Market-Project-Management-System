<div>
    <div class="container-fluid">


        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <div class="card bg-warning">
                    <div class="card-body">
                        <div class="py-3 text-center">
                            <ul class="bg-bubbles ps-0">
                                <li><i class="bx bx-grid-alt font-size-24"></i></li>
                                <li><i class="bx bx-tachometer font-size-24"></i></li>
                                <li><i class="bx bx-store font-size-24"></i></li>
                                <li><i class="bx bx-cube font-size-24"></i></li>
                                <li><i class="bx bx-cylinder font-size-24"></i></li>
                                <li><i class="bx bx-command font-size-24"></i></li>
                                <li><i class="bx bx-hourglass font-size-24"></i></li>
                                <li><i class="bx bx-pie-chart-alt font-size-24"></i></li>
                                <li><i class="bx bx-coffee font-size-24"></i></li>
                                <li><i class="bx bx-polygon font-size-24"></i></li>
                            </ul>
                            <div class="main-wid position-relative">


                                <h3 class="mb-0 text-white"> Welcome, {{ auth()->user()->name }}!</h3>
                                <h4 class="text-white">({{ auth()->user()->organisation->name }})</h4>

                                <p class="px-4 mt-4 text-white-50"> Explore, engage, and make the most of your
                                    experience. We're thrilled to have you on board!"</p>

                                <div class="pt-2 mt-4 mb-2">
                                    <a href="/profile" class="btn btn-light">View Profile <i
                                            class="mdi mdi-arrow-right ms-1"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>




    </div>
    <livewire:donor.reports />

</div>