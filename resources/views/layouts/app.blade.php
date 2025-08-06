<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/png" href="{{ asset('assets/images/rtc.png') }}" sizes="32x32" />

        <title> @yield('title') {{ '| ' . config('app.name') }} </title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />


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
        <style>
            body[data-layout="horizontal"] .page-content {
                margin-top: 70px;

                padding: 70px 0 60px 0;
            }



            .topBar {
                position: fixed;
                top: 0;
                width: 100%;
                z-index: 1002;
                /* highest, so dropdowns work */
            }

            #page-topbar {
                position: fixed;
                top: 52px;
                /* Adjust depending on height of navbar-top */
                width: 100%;

                /* just under the first one */
            }

            @media (max-width: 576px) {

                #page-topbar {
                    top: 92px;
                    /* Adjust if the top navbar grows taller on mobile */
                }

                body[data-layout="horizontal"] .page-content {
                    margin-top: 120px;
                    padding: 70px 0 60px 0;

                }

                .topnav {
                    position: fixed;

                    top: 160px;

                }

            }

            /* Preloader */
            .preloader {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: #fff;
                /* semi-transparent white background */
                z-index: 9999;
                /* Ensure it appears above all other content */
            }

            /* Spinner animation */
            .spinner-border.spinner {
                position: absolute;
                top: 50%;
                left: 50%;


                /* Spin animation */
            }

            .main-content {
                overflow: visible;
            }

            .select2-container--bootstrap-5.select2-container--focus .select2-selection,
            .select2-container--bootstrap-5.select2-container--open .select2-selection {
                border-color: #9cc0e0;
                outline: 0;
                -webkit-box-shadow: 0 0 0 .25rem rgba(57, 128, 192, .25);
                box-shadow: 0 0 0 .25rem rgba(57, 128, 192, .25)
            }

            .select2-container--bootstrap-5 .select2-selection {

                font-size: .875rem;

            }

            .select2-container--bootstrap-5 .select2-dropdown .select2-search .select2-search__field:focus {
                border-color: #9cc0e0;
                outline: 0;
                -webkit-box-shadow: 0 0 0 .25rem rgba(57, 128, 192, .25);
                box-shadow: 0 0 0 .25rem rgba(57, 128, 192, .25)
            }

            .select2-container--bootstrap-5 .select2-dropdown .select2-results__options .select2-results__option.select2-results__option--selected,
            .select2-container--bootstrap-5 .select2-dropdown .select2-results__options .select2-results__option[aria-selected=true]:not(.select2-results__option--highlighted) {
                color: #fff;
                background-color: #3980c0;
            }

            .select2-container--bootstrap-5 .select2-dropdown {

                border-color: #9cc0e0;

            }

            .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered .select2-selection__choice {
                display: flex;
                flex-direction: row;

                padding-right: 10px;
                margin-right: .375rem;
                margin-bottom: .375rem;
                font-size: 12px;
                color: #212529;
                cursor: auto;

                border-radius: 10rem;
                background-color: #3980c0;
                border-color: #3980c0;
                color: #fff;
                word-break: break-all;
                box-sizing: border-box;
                font-weight: 500;
            }

            .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered .select2-selection__choice .select2-selection__choice__remove {
                width: .75rem;
                height: .75rem;
                padding: .55em;
                margin-right: .25rem;
                overflow: hidden;
                text-indent: 100%;
                white-space: nowrap;
                background: transparent url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23FFFFFF'%3e%3cpath d='M.293.293a1 1 0 011.414 0L8 6.586 14.293.293a1 1 0 111.414 1.414L9.414 8l6.293 6.293a1 1 0 01-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 01-1.414-1.414L6.586 8 .293 1.707a1 1 0 010-1.414z'/%3e%3c/svg%3e") 50%/.75rem auto no-repeat;

                border: 0;
            }


            label::first-letter {
                text-transform: uppercase;
                /* Capitalize only the first letter */
            }


            .table-sticky-col {
                position: sticky;
                left: 0;
                background-color: #fff;
                /* Ensure background matches table to avoid overlap issues */
                z-index: 1;
                /* Ensure it stays above other table content */
            }

            th {
                text-transform: uppercase;
                background-color: #ffc107;
                /* Warning color in Bootstrap */
                color: #212529;
            }

            #btn-back-to-top {
                position: fixed;
                bottom: 20px;
                right: 20px;
                display: block;
                /* Leave the display as block; visibility and opacity handle appearance */
                visibility: hidden;
                opacity: 0;
                transition: opacity 0.5s ease;
            }

            [readonly] {
                background-color: #e9ecef;
            }

            /* Custom Tooltip Trigger */
            .custom-tooltip {
                position: relative;
                cursor: pointer;
                display: inline-block;
            }

            /* Custom Tooltip Text Styling */
            .custom-tooltip::after {
                content: attr(title);
                /* Use the data-bs-title attribute */
                visibility: hidden;
                opacity: 0;
                background-color: #333;
                /* Dark background */
                color: #fff;
                /* White text */
                text-align: center;
                border-radius: 4px;
                /* Rounded corners */
                padding: 8px;
                /* Padding inside the tooltip */
                position: absolute;
                bottom: 100%;
                /* Position above the trigger */
                left: 50%;
                transform: translateX(-50%);
                z-index: 10;
                transition: opacity 0.3s;
                /* Smooth fade-in */
                white-space: nowrap;
                /* Prevent text wrapping */
                font-size: 10px;
                /* Responsive font size */
                width: max-content;
                max-width: 90vw;
                /* Ensure it fits within the viewport */
                word-wrap: break-word;
                /* Allow breaking long words if needed */
                margin: 10px 10px;
            }


            /* Show the tooltip on hover */
            .custom-tooltip:hover::after,
            .custom-tooltip:hover::before {
                visibility: visible;
                opacity: 1;
            }

            .page-title-box .breadcrumb {

                background-color: #ECEBEA;
                padding: 1.1rem;
                border-radius: 10px;

            }

            .bs5-rotate-90 {
                transform: rotate(90deg);
            }

            .bs5-rotate-0 {
                transform: rotate(0deg);
            }

            .placeholder {
                border-radius: 20px;
                background-color: #e5e7ebc4;
                cursor: auto;
            }


            .table-secondary {

                --bs-table-bg: rgb(241 241 241/1) !important;
                --bs-table-border-color: #e5e7eb !important;
            }
        </style>
        <!-- Scripts -->
        @vite(['resources/js/app.js'])
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
                    {{ $slot }}



                </div>


                <footer class="py-3 mt-auto footer bg-light">
                    <button type="button" class="btn btn-warning btn-lg rounded-circle " id="btn-back-to-top">
                        <i class="fas fa-arrow-up"></i>
                    </button>
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


            @stack('scripts')
            <script>
                $(document).ready(function() {
                    $('.preloader').fadeOut('slow');
                    // Get the button
                    let mybutton = document.getElementById("btn-back-to-top");

                    // Initially hide the button
                    mybutton.style.opacity = "0";
                    mybutton.style.visibility = "hidden";
                    mybutton.style.transition = "opacity 0.5s ease";

                    // When the user scrolls down 20px from the top of the document, show the button
                    window.onscroll = function() {
                        scrollFunction();
                    };

                    function scrollFunction() {
                        if (
                            document.body.scrollTop > 20 ||
                            document.documentElement.scrollTop > 20
                        ) {
                            mybutton.style.visibility = "visible";
                            mybutton.style.opacity = "1";
                        } else {
                            mybutton.style.opacity = "0";
                            setTimeout(() => {
                                mybutton.style.visibility = "hidden";
                            }, 500); // Match the transition duration
                        }
                    }

                    // When the user clicks on the button, scroll to the top of the document
                    mybutton.addEventListener("click", backToTop);

                    function backToTop() {
                        document.body.scrollTop = 0;
                        document.documentElement.scrollTop = 0;
                    }
                    if (window.location.hash !== '') {
                        const button = document.querySelector(`a[href='${window.location.hash}']`);

                        if (button) {
                            setTimeout(() => {
                                button.click();
                            })


                        }


                    }

                    const getUserRole = @json(auth()->user()->getRoleNames()->first());
                    if (getUserRole === 'enumerator') {
                        const button = document.querySelector(`a[href='#dashboard-2']`);

                        if (button) {
                            setTimeout(() => {
                                button.click();
                            })


                        }
                    }
                });



                $(document).ajaxError(function(event, jqxhr, settings, exception) {
                    if (jqxhr.status === 419) { // 419 is the status code for Laravel's CSRF token mismatch

                        window.location.href = '/login'; // Redirect to the login page
                    }
                });
                document.addEventListener('livewire:init', () => {

                    Livewire.hook('request', ({
                        fail
                    }) => {
                        fail(({
                            status,
                            preventDefault
                        }) => {
                            if (status === 419) {
                                location.reload(true)

                                preventDefault()
                            }
                        })
                    })
                })
            </script>

            <script src="{{ asset('assets/js/app.js') }}"></script>

    </body>

</html>
