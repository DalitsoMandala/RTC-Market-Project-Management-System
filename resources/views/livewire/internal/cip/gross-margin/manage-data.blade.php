<div>

    @section('title')
        Manage Gross Margins
    @endsection
    <div class="container-fluid">


        @php

            $routePrefix = Route::current()->getPrefix();

        @endphp
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">


                    <div class="page-title-left col-12">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item "> <a href="{{ $routePrefix }}/gross-margin/add-data"> Submit
                                    data</a> </li>

                            <li class="breadcrumb-item active"> View Data</li>

                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <x-alerts />


        <div class="card ">
            <x-card-header>Gross Margin Data</x-card-header>
            <div class="card-body">
                <livewire:tables.gross-margin-table />
            </div>
        </div>



    </div>

</div>
@script
    <script>
        // Listen to every button click on the page
        document.querySelectorAll(".goLeft").forEach(btn => {
            btn.addEventListener("click", function() {
                // Scroll all elements with .table-responsive back to far left
                document.querySelectorAll(".table-responsive").forEach(el => {
                    setTimeout(() => {
                        el.scrollTo({
                            left: 0,
                            behavior: 'smooth'
                        });
                    }, 2000);

                });
            });
        });
    </script>
@endscript
