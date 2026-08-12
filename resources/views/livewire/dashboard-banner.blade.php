<div>
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

        :root {
            --peach-light: #FDEBD3;
            --peach-mid: #F7D9AE;
            --orange: #F5A031;
            --brown: #8A4B26;
            --ink: #2A1B10;
            --ink-soft: #6B5B4C;
        }


        .preview-wrap {

            margin: 0 auto;
        }

        /* ===== Swiper shell ===== */
        .rtc-swiper {
            border-radius: 20px;
            overflow: hidden;
            /* box-shadow: 0 12px 28px -14px rgba(138, 75, 38, 0.28), 0 0 0 1px rgba(138, 75, 38, 0.06) inset; */
        }

        .rtc-swiper .swiper-slide {
            min-height: 340px;
            height: auto;
        }

        /* ===== Slide 1: stats banner ===== */
        .rtc-banner {
            position: relative;
            background: linear-gradient(120deg, var(--peach-light) 0%, var(--peach-mid) 100%);
            padding: 38px 42px;
            color: var(--ink);
            min-height: 340px;
        }

        .rtc-banner::after {
            content: "";
            position: absolute;
            width: 320px;
            height: 320px;
            right: -110px;
            bottom: -140px;
            background: radial-gradient(circle, rgba(245, 160, 49, 0.18) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 1;
        }

        .rtc-content {
            position: relative;
            z-index: 2;
        }

        .rtc-top-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 24px;
            flex-wrap: wrap;
        }

        .rtc-text-block {
            flex: 1 1 380px;
            min-width: 0;
        }

        .rtc-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(245, 160, 49, 0.14);
            border: 1px solid rgba(245, 160, 49, 0.4);
            color: var(--brown);
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 6px 14px;
            border-radius: 999px;
        }

        .rtc-eyebrow.on-photo {
            background: rgba(255, 255, 255, 0.16);
            border-color: rgba(255, 255, 255, 0.4);
            color: #fff;
            backdrop-filter: blur(4px);
        }

        .rtc-eyebrow .dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--orange);
            box-shadow: 0 0 0 3px rgba(245, 160, 49, 0.25);
        }

        .rtc-eyebrow.on-photo .dot {
            background: #FFA239;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.3);
        }

        .rtc-title {
            font-weight: 800;
            font-size: clamp(1.5rem, 2.6vw, 2.1rem);
            letter-spacing: -0.01em;
            margin: 16px 0 6px;
            color: var(--ink);
        }

        .rtc-subtitle {
            color: var(--ink-soft);
            font-size: 1rem;
            max-width: 520px;
            margin-bottom: 0;
        }

        .rtc-hero-img {
            flex: 0 0 auto;
            width: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .rtc-hero-img img {
            width: 100%;
            height: auto;
            filter: drop-shadow(0 12px 18px rgba(138, 75, 38, 0.28));
        }

        .rtc-divider {
            border: none;
            height: 1px;
            background: linear-gradient(to right, rgba(138, 75, 38, 0.22), rgba(138, 75, 38, 0.02));
            margin: 26px 0 24px;
        }

        .rtc-stats {
            --cols: 5;
            display: grid;
            grid-template-columns: repeat(var(--cols), 1fr);
            gap: 14px;
        }

        @media (max-width: 992px) {
            .rtc-stats {
                --cols: 3;
            }
        }

        @media (max-width: 576px) {
            .rtc-stats {
                --cols: 2;
            }
        }

        .rtc-stat {
            background: rgba(245, 160, 49, 0.14);
            border: 1px solid rgba(245, 160, 49, 0.4);
            border-radius: 14px;
            padding: 15px 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .rtc-stat:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 18px -8px rgba(138, 75, 38, 0.28);
        }

        .rtc-stat-icon {
            flex-shrink: 0;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--orange), var(--brown));
            color: #fff;
            font-size: 1rem;
            box-shadow: 0 4px 10px rgba(245, 160, 49, 0.35);
        }

        .rtc-stat-value {
            font-weight: 700;
            font-size: 1.02rem;
            line-height: 1.15;
            white-space: nowrap;
            color: var(--ink);
        }

        .rtc-stat-label {
            font-size: 0.7rem;
            color: var(--ink-soft);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        /* ===== Slide 2: background-image hero with overlay ===== */
        .rtc-photo-slide {
            position: relative;
            min-height: 340px;
            height: 100%;
            display: flex;
            align-items: center;
            /* placeholder gradient standing in for a real photo — swap background-image below */
            background-image: url('{{ asset('assets/images/panel-bg2.jpg') }}');
            background-size: cover;
            background-position: center center;
        }

        /* if you use a real photo instead of the gradient placeholder,
     just change the second background-image above to:
     url('{{ asset('assets/images/rtc-bg.jpg') }}') */

        .rtc-photo-content {
            position: relative;
            z-index: 2;
            padding: 38px 48px;
            max-width: 620px;
        }

        .rtc-photo-title {
            font-weight: 800;
            font-size: clamp(1.6rem, 3vw, 2.4rem);
            letter-spacing: -0.01em;
            color: #fff;
            margin: 16px 0 10px;
            text-shadow: 0 2px 12px rgba(0, 0, 0, 0.35);
        }

        .rtc-photo-sub {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1rem;
            text-shadow: 0 1px 6px rgba(0, 0, 0, 0.3);
            margin-bottom: 0;
        }

        /* ===== Swiper pagination / nav, themed ===== */
        .rtc-swiper .swiper-pagination {
            bottom: 14px !important;
        }

        .rtc-swiper .swiper-pagination-bullet {
            width: 8px;
            height: 8px;
            background: rgba(138, 75, 38, 0.35);
            opacity: 1;
            transition: width .2s ease, border-radius .2s ease, background .2s ease;
        }

        .rtc-swiper .swiper-pagination-bullet-active {
            background: var(--orange);
            width: 22px;
            border-radius: 5px;
        }

        .rtc-swiper .swiper-button-prev,
        .rtc-swiper .swiper-button-next {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.75);
            border-radius: 50%;
            color: var(--brown);
            opacity: 0;
            transition: opacity .2s ease, background .2s ease;
            backdrop-filter: blur(3px);
        }

        .rtc-swiper .swiper-button-prev:after,
        .rtc-swiper .swiper-button-next:after {
            font-size: 16px;
            font-weight: 700;
        }

        .rtc-swiper:hover .swiper-button-prev,
        .rtc-swiper:hover .swiper-button-next {
            opacity: 1;
        }

        .rtc-swiper .swiper-button-prev:hover,
        .rtc-swiper .swiper-button-next:hover {
            background: #fff;
        }

        .rtc-note {
            text-align: center;
            color: #9a8b7c;
            font-size: 0.85rem;
            margin-top: 20px;
        }

        @media (max-width: 700px) {
            .rtc-hero-img {
                display: none;
            }
        }

        /* ===== Slide 3: actors-reached progress ring ===== */
        .rtc-progress-slide {
            position: relative;
            background: linear-gradient(120deg, var(--peach-light) 0%, var(--peach-mid) 100%);
            padding: 38px 42px;
            min-height: 340px;
            display: flex;
            align-items: center;
        }

        .rtc-progress-slide::after {
            content: "";
            position: absolute;
            width: 280px;
            height: 280px;
            left: -100px;
            top: -120px;
            background: radial-gradient(circle, rgba(245, 160, 49, 0.16) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 1;
        }

        .rtc-progress-row {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 32px;
            flex-wrap: wrap;
            width: 100%;
        }

        .rtc-progress-text {
            flex: 1 1 320px;
            min-width: 0;
        }

        .rtc-progress-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 18px;
        }

        .rtc-progress-tags .tag {
            background: rgba(255, 255, 255, 0.55);
            border: 1px solid rgba(138, 75, 38, 0.15);
            color: var(--brown);
            font-size: 0.75rem;
            font-weight: 700;
            padding: 6px 13px;
            border-radius: 999px;
        }

        .rtc-ring-card {
            flex: 0 0 auto;
            background: rgba(255, 255, 255, 0.55);
            border: 1px solid rgba(138, 75, 38, 0.12);
            border-radius: 18px;
            padding: 22px 30px;
            text-align: center;
        }

        .rtc-ring-label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--brown);
            margin-bottom: 2px;
        }

        .rtc-ring-sub {
            font-size: 0.72rem;
            color: var(--ink-soft);
            margin-bottom: 14px;
        }

        .rtc-ring-wrap {
            position: relative;
            width: 168px;
            height: 168px;
            margin: 0 auto;
        }

        .rtc-ring-wrap svg {
            width: 100%;
            height: 100%;
            transform: rotate(-90deg);
        }

        .rtc-ring-track {
            fill: none;
            stroke: rgba(138, 75, 38, 0.14);
            stroke-width: 14;
        }

        .rtc-ring-value-path {
            fill: none;
            stroke: url(#rtcRingGradient);
            stroke-width: 14;
            stroke-linecap: round;
            stroke-dasharray: 439.8;
            stroke-dashoffset: 167.1;
            /* 62% progress: offset = 439.8 - (439.8 * percent/100) */
            transition: stroke-dashoffset 1.2s ease-in-out;
        }

        .rtc-ring-center {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .rtc-ring-pct {
            font-weight: 800;
            font-size: 1.7rem;
            color: var(--ink);
            line-height: 1;
        }

        .rtc-ring-count {
            font-size: 0.72rem;
            color: var(--ink-soft);
            margin-top: 4px;
        }

        .rtc-ring-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 14px;
            font-size: 0.72rem;
            color: var(--ink-soft);
        }

        .rtc-ring-status .dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--orange);
        }

        @media (max-width: 700px) {
            .rtc-progress-row {
                justify-content: center;
                text-align: center;
            }

            .rtc-progress-tags {
                justify-content: center;
            }
        }

        .rtc-banner,
        .rtc-photo-slide,
        .rtc-progress-slide {
            height: 100%;
            min-height: 340px;
            /* keep as a floor, height:100% does the real work now */
        }
    </style>
    <!-- end page title -->




    <div>


        <div class="mt-4 row">
            <div class="col-12">

                <h3 class="mb-0 text-dark"> Welcome Back, {{ auth()->user()->name }}!
                    ({{ auth()->user()->organisation->name }})</h3>

                <hr>
            </div>

        </div>
        <div class="mt-0 row">
            <div class="col-12">

                <div class="preview-wrap">

                    <div class="swiper rtc-swiper">
                        <div class="swiper-wrapper">

                            <!-- ===== SLIDE 1 — stats banner ===== -->
                            <div class="swiper-slide">
                                <div class="rtc-banner">
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
                                            <div class="rtc-hero-img">
                                                <div class="rtc-stat">
                                                    <img src="{{ asset('assets/images/rtc.png') }}" alt="App-logo">
                                                </div>

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
