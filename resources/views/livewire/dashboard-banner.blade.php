<div>

    <!-- end page title -->




    <div>


        <div class="mt-1 row">
            <div class="col-12 ">

                <h3 class="mb-0 text-dark fw-bolder"> Welcome Back, {{ auth()->user()->name }}
                </h3>
                <p class="text-muted mb-0">Here is the overview of the project's progress and key metrics.</p>



            </div>


        </div>
        <hr>
        <div class="mt-0 row">
            <div class="col-12">

                <div class="preview-wrap">

                    <div class="swiper rtc-swiper">
                        <div class="swiper-wrapper">

                            <!-- ===== SLIDE 1 — stats banner ===== -->
                            <div class="swiper-slide">
                                <div class="rtc-banner"
                                    style="background:url('{{ asset('assets/images/ROOTBG.png') }}'); background-size: cover; background-position: center;">
                                    <div class="rtc-content">

                                        <div class="rtc-top-row">
                                            <div class="rtc-text-block">
                                                <span class="rtc-eyebrow"><span class="dot"></span> Project
                                                    Dashboard</span>
                                                <h1 class="rtc-title">RTC Market Project Management System</h1>
                                                <p class="rtc-subtitle">Manage project data, monitor indicators, and
                                                    generate
                                                    reports across all districts and value chains.</p>
                                            </div>
                                            <div class="rtc-hero-img d-none d-lg-block">

                                                <img src="{{ asset('assets/images/root3.png') }}" alt="App-logo">


                                            </div>
                                        </div>

                                        <hr class="rtc-divider">

                                        <div class="rtc-stats">
                                            <div class="rtc-stat">
                                                <div class="rtc-stat-icon"><i class='bx bx-map'></i></div>
                                                <div>
                                                    <div class="rtc-stat-value">16</div>
                                                    <div class="rtc-stat-label">Districts</div>
                                                </div>
                                            </div>
                                            <div class="rtc-stat">
                                                <div class="rtc-stat-icon"><i class='bx bx-group'></i></div>
                                                <div>
                                                    <div class="rtc-stat-value">60,000</div>
                                                    <div class="rtc-stat-label">Target Reach</div>
                                                </div>
                                            </div>
                                            <div class="rtc-stat">
                                                <div class="rtc-stat-icon"><i class='bx bx-spa'></i></div>
                                                <div>
                                                    <div class="rtc-stat-value">3</div>
                                                    <div class="rtc-stat-label">Value Chains</div>
                                                </div>
                                            </div>
                                            <div class="rtc-stat">
                                                <div class="rtc-stat-icon"><i class='bx bx-user-voice'></i></div>
                                                <div>
                                                    <div class="rtc-stat-value">8</div>
                                                    <div class="rtc-stat-label">Partners</div>
                                                </div>
                                            </div>
                                            <div class="rtc-stat">
                                                <div class="rtc-stat-icon"><i class='bx bx-calendar'></i></div>
                                                <div>
                                                    <div class="rtc-stat-value">2023–2027</div>
                                                    <div class="rtc-stat-label">Project Period</div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>



                        </div>

                        <div class="swiper-pagination"></div>
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-button-next"></div>
                    </div>


                </div>
            </div>
        </div>
    </div>


</div>
