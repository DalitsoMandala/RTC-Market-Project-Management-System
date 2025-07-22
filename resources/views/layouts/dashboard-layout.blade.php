<style>
    .topbox {
        position: relative;
    }

    .topbox img {
        position: absolute;
        top: -80px;
        right: -60px;
        width: 100%;
        height: auto;
        max-width: 360px;
    }

    .jumbo {
        position: relative;
    }

    .jumbo>.wave {
        position: absolute;
        top: 0;
        left: 0;

        width: 0;
        height: 0;

        transition: width .5s ease-in-out, height .5s ease-in-out;
        border-top-left-radius: 5px;

    }

    .jumbo:hover .wave {
        width: 100%;
        height: 100%;
        border-radius: 5px;

    }


    @media only screen and (max-width: 1400px) {
        .topbox img {
            top: -20px;
            right: -60px;
        }
    }

    @media only screen and (max-width: 1200px) {
        .topbox img {
            top: 10px;
            right: 0px;

            max-width: 300px;
        }
    }

    @media only screen and (max-width: 1000px) {
        .topbox img {
            display: none;
        }
    }
</style>
<!-- end page title -->



<div class="row align-items-center" >

    <div class="order-1 col-12">

        <h3 class="mb-0 text-dark"> Welcome Back, {{ auth()->user()->name }}!
            ({{ auth()->user()->organisation->name }})</h3>
        <p class="my-2 text-secondary w-75 ">Effortlessly manage and organize your data in one place. Navigate
            through
            your
            tasks, access valuable insights, and streamline your workflow with ease.</p>

        <div class="my-3 bg-opacity-25 card bg-warning jumbo">
            <div class="wave"> </div>

            <div class="card-body">
                <div class="text-left ">
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
                    <div class="main-wid position-relative topbox">

                        <div class="mx-3 my-3 row">
                            <div class="px-0 col-lg-8">

                                <h5 class="mt-2 fw-bolder h2">{{ config('app.name') }}</h5>
                                <p class="mt-4 text-body" style="font-size:14px"> The International Potato
                                    Center (CIP) has developed
                                    a novel approach that integrates research and development activities aimed
                                    at
                                    commercializing the
                                    Root and Tuber Crops (RTC) subsector (with focus on potato, sweetpotato and
                                    cassava). The goal is to increase the subsector’s contribution to food and
                                    nutrition security, incomes, job creation and economic growth in Malawi.
                                    Funded by
                                    the Embassy of Ireland to Malawi, the 4-year (May 2023 to April 2027)
                                    project namely ‘Market-led Transformation of the Root and Tuber Crops
                                    Subsector
                                    (<b>RTC-MARKET</b>) is being implemented in 16 districts of the country.
                                    Working
                                    with all possible actors along the value chains, including producer and
                                    processing
                                    organizations individuals and commercial entities at all levels, CIP has
                                    DARS, IITA and RTCDT among its key partners having expertise to ensure
                                    effective
                                    performance of diversity actors (60,000) to be reached in 4 years.</p>


                            </div>


                        </div>






                        <img class="position-absolute" src="{{ asset('assets/images/rtc bg.png') }}" class="img-fluid"
                            alt="">
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>
