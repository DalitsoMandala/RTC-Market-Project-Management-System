<div>
    @section('title')
        Update Baseline Values
    @endsection

    <div class="container-fluid">
        <!-- start page title -->
        <div class="my-2 row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Update Baseline Values</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                            <li class="breadcrumb-item active">Update Baseline Values</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <x-alerts />
        <!-- end page title -->

        <div class=" row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Baseline Table</h5>
                    </div>
                    <div class="px-0 card-body">


                        <livewire:tables.baseline-table />
                    </div>
                </div>
            </div>
        </div>

        <!-- Confirmation Modal -->
        <div wire:ignore.self class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmModalLabel">Confirm Update</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to update this baseline data?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-warning" wire:click="save"
                            data-bs-dismiss="modal">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@script
    <script>
        $wire.on('submit-form', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>
@endscript
