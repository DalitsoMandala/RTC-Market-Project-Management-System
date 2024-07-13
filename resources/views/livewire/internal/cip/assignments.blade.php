<div>
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Dashboard</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active">Page Name</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            Indicators & Lead partners
                        </h3>
                    </div>
                    <div class="card-body">
                        <livewire:tables.indicator-leads />
                    </div>
                </div>
            </div>
        </div>



        {{--  <div x-data x-init="$wire.on('showModal', (e) => {

            const myModal = new bootstrap.Modal(document.getElementById(e.name), {})
            myModal.show();
        })">


            <x-modal id="view-indicator-modal" title="edit">
                <form>
                    <div class="mb-3">

                        <x-text-input placeholder="Name of indicator..." />
                    </div>

                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>

                    </div>
                </form>
            </x-modal>

        </div> --}}




    </div>
    @script
        <script>
            const tooltipTriggerList = document.querySelectorAll('button[title]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

            $wire.on('reload-tooltips', () => {

                setTimeout(() => {
                    const tooltipTriggerList = document.querySelectorAll('button[title]');
                    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(
                        tooltipTriggerEl))

                }, 1000);


            })
        </script>
    @endscript
</div>
