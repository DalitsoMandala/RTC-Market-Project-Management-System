<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/png" href="{{ asset('assets/images/rtc.png') }}" sizes="32x32" />


        <title> @yield('title') {{ '| ' . config('app.name') }} </title>

        <!-- Fonts -->
        {{-- <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" /> --}}



        <!-- Bootstrap Css -->
        <link href="{{ asset('assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet"
            type="text/css" />
        <link rel="stylesheet" href="{{ asset('assets/libs/choicesjs/styles/choices.min.css') }}">
        <!-- Icons Css -->
        <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />


        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/filepond@4.31.1/dist/filepond.min.css">

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/TableExport/5.2.0/css/tableexport.css"
            integrity="sha512-+m+NCQG6uttXsLjwxHTUdhov99LW3TSFEiM2LSFMwfOePszb2as348/96cCBG35mOK+3Gp4P0EQRWpKLZfGTnA=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
        <link rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
        <link rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.11/build/css/intlTelInput.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css">


        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/keen-slider@6.8.6/keen-slider.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
        <!-- Scripts -->
        @vite(['resources/js/app.js', 'resources/css/app.css'])
    </head>

    <body data-layout="horizontal" data-topbar="light">
        <div id="layout-wrapper">
            <div class="preloader">
                <div class="spinner-border text-warning spinner"></div>
            </div>


            @include('layouts.navigation')



            <!-- Page Content -->
            <div class="main-content">
                <div class="mb-1 page-content ">

                    @if (\App\Models\SystemDetail::find(1)?->maintenance_mode == 1)
                        <div class="container-fluid">
                            <div class="alert alert-warning ">
                                <span><i class="bx bx-info-circle"></i></span> <strong>The system is in maintance
                                    mode!</strong>

                            </div>
                        </div>
                    @endif

                    {{ $slot }}



                </div>


                <footer class="py-3 mt-auto footer bg-light">
                    {{-- <button type="button" class="btn btn-warning btn-lg rounded-circle " id="btn-back-to-top">
                        <i class="fas fa-angle-up"></i>
                    </button> --}}
                    <div class="container text-center">
                        <span class="text-muted">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights
                            reserved.</span>
                    </div>
                </footer>
            </div>

            <script src="{{ asset('assets/libs/jquery/jquery-3.7.1.min.js') }}"></script>
            <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
            <script src="{{ asset('assets/libs/metismenujs/metismenujs.min.js') }}"></script>
            <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
            <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
            <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script
                src="https://cdn.jsdelivr.net/npm/filepond-plugin-image-exif-orientation@1.0.11/dist/filepond-plugin-image-exif-orientation.min.js">
            </script>
            <script src="https://cdn.jsdelivr.net/npm/filepond-plugin-image-resize@2.0.10/dist/filepond-plugin-image-resize.min.js">
            </script>
            <script src="https://cdn.jsdelivr.net/npm/filepond-plugin-file-encode@2.1.14/dist/filepond-plugin-file-encode.min.js">
            </script>
            <script
                src="https://cdn.jsdelivr.net/npm/filepond-plugin-image-transform@3.8.7/dist/filepond-plugin-image-transform.min.js">
            </script>
            <script src="https://cdn.jsdelivr.net/npm/filepond-plugin-image-edit@1.6.3/dist/filepond-plugin-image-edit.min.js">
            </script>
            <script
                src="https://cdn.jsdelivr.net/npm/filepond-plugin-file-validate-type@1.2.9/dist/filepond-plugin-file-validate-type.min.js">
            </script>
            <script
                src="https://cdn.jsdelivr.net/npm/filepond-plugin-file-validate-size@2.2.8/dist/filepond-plugin-file-validate-size.min.js">
            </script>
            <script
                src="https://cdn.jsdelivr.net/npm/filepond-plugin-image-preview@4.6.12/dist/filepond-plugin-image-preview.min.js">
            </script>
            <script src="https://cdn.jsdelivr.net/npm/filepond@4.31.1/dist/filepond.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

            <script>
                window.Apex = {
                    chart: {

                        toolbar: {
                            show: true,

                            tools: {
                                download: true,
                                selection: true,
                                zoom: true,
                                zoomin: true,
                                zoomout: true,
                                pan: true,
                                reset: true,
                            },
                            export: {
                                csv: {
                                    filename: 'chart-data',
                                    columnDelimiter: ',',
                                    headerCategory: 'Category',
                                    headerValue: 'Value',
                                },
                                svg: {
                                    filename: 'chart'
                                },
                                png: {
                                    filename: 'chart'
                                },
                            },
                        },
                    },

                    xaxis: {

                        labels: {
                            rotateAlways: true,
                            rotate: -45, // slants labels
                            style: {
                                fontSize: '12px',

                            }
                        },

                        axisBorder: {
                            show: true,
                            color: '#F97316', // orange line
                            height: 1.5, // thickness
                            width: '100%', // full width
                            offsetX: 0,
                            offsetY: 0
                        },

                    },



                };
            </script>
            <script src="{{ asset('assets/libs/choicesjs/scripts/choices.min.js') }}"></script>
            <script>
                FilePond.registerPlugin(FilePondPluginFileValidateSize);
                FilePond.registerPlugin(FilePondPluginFileValidateType);
                FilePond.registerPlugin(FilePondPluginImagePreview);
            </script>
            <script src="https://cdn.jsdelivr.net/npm/exceljs@4.4.0/dist/exceljs.min.js"></script>
            <x-livewire-alert::scripts />
            <script src="https://cdn.jsdelivr.net/npm/jquery-table2excel@1.1.1/dist/jquery.table2excel.min.js"
                integrity="sha256-UbQOHbRdBiTxFQd7J+zvg9v9eXjTMepIyA+67ohTgtY=" crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.11/build/js/intlTelInput.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
            <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.13/index.global.min.js'></script>
            <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
            <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css" rel="stylesheet">
            <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
            <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/jquery-table2excel@1.1.1/dist/jquery.table2excel.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
            <script src="https://cdn.lordicon.com/lordicon.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"
                integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg=="
                crossorigin="anonymous" referrerpolicy="no-referrer"></script>
            <!-- ✅ Global build of jsPDF -->

            <!-- ✅ AutoTable plugin -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.1/jspdf.plugin.autotable.min.js"></script>

            <script src="https://cdn.jsdelivr.net/npm/keen-slider@6.8.5/keen-slider.min.js"></script>


            <script>
                document.addEventListener('DOMContentLoaded', () => {

                    // Fade out preloader
                    const preloader = document.querySelector('.preloader');
                    if (preloader) {
                        preloader.style.transition = 'opacity 0.6s ease';

                        setTimeout(() => {
                            preloader.style.opacity = '0';

                            setTimeout(() => {
                                preloader.style.display = 'none';
                            }, 600);
                        }, 0);
                    }

                    // Back to top button
                    const mybutton = document.getElementById('btn-back-to-top');

                    if (mybutton) {
                        mybutton.style.opacity = '0';
                        mybutton.style.visibility = 'hidden';
                        mybutton.style.transition = 'opacity 0.5s ease';

                        function scrollFunction() {
                            if (
                                document.body.scrollTop > 20 ||
                                document.documentElement.scrollTop > 20
                            ) {
                                mybutton.style.visibility = 'visible';
                                mybutton.style.opacity = '1';
                            } else {
                                mybutton.style.opacity = '0';

                                setTimeout(() => {
                                    mybutton.style.visibility = 'hidden';
                                }, 500);
                            }
                        }

                        window.addEventListener('scroll', scrollFunction);
                        scrollFunction(); // Run once on page load

                        mybutton.addEventListener('click', () => {
                            window.scrollTo({
                                top: 0,
                                behavior: 'smooth'
                            });
                        });
                    }

                    // Open tab from URL hash
                    if (window.location.hash) {
                        const button = document.querySelector(
                            `a[href="${window.location.hash}"]`
                        );

                        if (button) {
                            setTimeout(() => button.click());
                        }
                    }

                    // Enumerator dashboard
                    const getUserRole = @json(auth()->user()->getRoleNames()->first());

                    if (getUserRole === 'enumerator') {
                        const button = document.querySelector('a[href="#dashboard-2"]');

                        if (button) {
                            setTimeout(() => button.click());
                        }
                    }

                    // Mobile dropdown submenu
                    document.querySelectorAll('.topnav .dropdown-menu .dropdown-toggle')
                        .forEach(el => {
                            el.addEventListener('click', function(e) {

                                if (window.innerWidth >= 992) {
                                    return;
                                }

                                e.preventDefault();
                                e.stopPropagation();

                                const parent = this.parentElement;
                                parent.classList.toggle('show');

                                const submenu = this.nextElementSibling;

                                if (submenu) {
                                    submenu.classList.toggle('show');
                                }
                            });
                        });
                });


                // Global AJAX error (Fetch API)
                const originalFetch = window.fetch;

                window.fetch = async (...args) => {
                    const response = await originalFetch(...args);

                    if (response.status === 419) {
                        window.location.href = '/login';
                    }

                    return response;
                };

                // Global AJAX error (XMLHttpRequest)
                (function() {
                    const originalOpen = XMLHttpRequest.prototype.open;

                    XMLHttpRequest.prototype.open = function() {
                        this.addEventListener('load', function() {
                            if (this.status === 419) {
                                window.location.href = '/login';
                            }
                        });

                        return originalOpen.apply(this, arguments);
                    };
                })();

                // Livewire 3
                document.addEventListener('livewire:init', () => {
                    Livewire.hook('request', ({
                        fail
                    }) => {
                        fail(({
                            status,
                            preventDefault
                        }) => {
                            if (status === 419) {
                                location.reload(true);
                                preventDefault();
                            }
                        });
                    });


                });
            </script>

            <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
            <script>
                new Swiper('.rtc-swiper', {
                    effect: 'fade',
                    fadeEffect: {
                        crossFade: true
                    },
                    loop: true,
                    autoplay: {
                        delay: 10000,
                        disableOnInteraction: false,
                        pauseOnMouseEnter: true
                    },
                    speed: 700,
                    pagination: {
                        el: '.rtc-swiper .swiper-pagination',
                        clickable: true
                    },
                    navigation: {
                        nextEl: '.rtc-swiper .swiper-button-next',
                        prevEl: '.rtc-swiper .swiper-button-prev'
                    }
                });
            </script>

            @stack('scripts')
    </body>

</html>
