<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>

        <!-- Fonts -->
        <link data-navigate-once rel="preconnect" href="https://fonts.bunny.net">
        <link data-navigate-once href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap"
            rel="stylesheet" />


        <!-- Bootstrap Css -->
        <link data-navigate-once href="{{ asset('assets/css/bootstrap.min.css') }}" id="bootstrap-style"
            rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="{{ asset('assets/libs/choicesjs/styles/choices.min.css') }}">
        <!-- Icons Css -->
        <link data-navigate-once href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link data-navigate-once href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet"
            type="text/css" />


        <link data-navigate-once href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />

        <link data-navigate-once rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script data-navigate-once src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

        <link data-navigate-once rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/TableExport/5.2.0/css/tableexport.css"
            integrity="sha512-+m+NCQG6uttXsLjwxHTUdhov99LW3TSFEiM2LSFMwfOePszb2as348/96cCBG35mOK+3Gp4P0EQRWpKLZfGTnA=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />


        <style>
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
                <div class="page-content">
                    {{ $slot }}

                </div>
                </main>
            </div>
            <script data-navigate-once src="{{ asset('assets/libs/jquery/jquery-3.7.1.min.js') }}"></script>
            <script data-navigate-once src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
            <script data-navigate-once src="{{ asset('assets/libs/metismenujs/metismenujs.min.js') }}"></script>
            <script data-navigate-once src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
            <script data-navigate-once src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
            <script data-navigate-once src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script data-navigate-once
                src="https://unpkg.com/filepond-plugin-image-exif-orientation/dist/filepond-plugin-image-exif-orientation.js">
            </script>
            <script data-navigate-once src="https://unpkg.com/filepond-plugin-image-resize/dist/filepond-plugin-image-resize.js">
            </script>
            <script data-navigate-once src="https://unpkg.com/filepond-plugin-file-encode/dist/filepond-plugin-file-encode.js">
            </script>
            <script data-navigate-once
                src="https://unpkg.com/filepond-plugin-image-transform/dist/filepond-plugin-image-transform.js"></script>
            <script data-navigate-once src="https://unpkg.com/filepond-plugin-image-edit/dist/filepond-plugin-image-edit.js">
            </script>
            <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
            <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
            <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
            <script data-navigate-once src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>
            <script data-navigate-once src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
            <script data-navigate-once src="{{ asset('assets/libs/choicesjs/scripts/choices.min.js') }}"></script>
            <script>
                FilePond.registerPlugin(FilePondPluginFileValidateSize);
                FilePond.registerPlugin(FilePondPluginFileValidateType);
                FilePond.registerPlugin(FilePondPluginImagePreview);
            </script>
            <script src="https://cdn.jsdelivr.net/npm/exceljs@4.4.0/dist/exceljs.min.js"></script>
            <x-livewire-alert::scripts />
            <script src="https://cdn.jsdelivr.net/npm/jquery-table2excel@1.1.1/dist/jquery.table2excel.min.js"
                integrity="sha256-UbQOHbRdBiTxFQd7J+zvg9v9eXjTMepIyA+67ohTgtY=" crossorigin="anonymous"></script>

            @stack('scripts')
            <script>
                $(document).ready(function() {
                    $('.preloader').fadeOut('slow');

                });
            </script>
            <script data-navigate-once src="{{ asset('assets/js/app.js') }}"></script>
    </body>

</html>
