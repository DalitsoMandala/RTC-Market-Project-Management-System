<div>
    @section('title')
        Update Baseline Values
    @endsection

    <div class="container-fluid">
        <!-- start page title -->
        <div class="my-2 row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">


                    <div class="page-title-left col-12">
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

                <div class="shadow-md card">

                    <div class="card-header card-title fw-bold border-bottom-0 ">
                        Baseline Data
                    </div>
                    <div class=" card-body">


                        <livewire:tables.baseline-table />
                    </div>

                </div>
            </div>


            <div x-data x-init="$wire.on('editData', (e) => {
                setTimeout(() => {
                    $wire.dispatch('set', { indicator_id: e.indicator_id });
                    //  $wire.setData(e.rowId);
                    const myModal = new bootstrap.Modal(document.getElementById(e.name), {})
                    myModal.show();
                }, 500);


            })
            $wire.on('hideModal', (e) => {
                const modals = document.querySelectorAll('.modal.show');

                // Iterate over each modal and hide it using Bootstrap's modal hide method
                modals.forEach(modal => {
                    const modalInstance = bootstrap.Modal.getInstance(modal);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                });
            })">




                <x-modal id="view-baseline-modal" title="Update baseline value">

                    <x-alpine-alerts />
                    {{ var_export($errors) }}
                    {{ var_export($baseline_value) }}
                    <h4 class="mb-5 text-center h4">Please confirm whether you would like to update this baseline?
                    </h4>





                    <form wire:submit="save">
                        <div class="mb-3">
                            <label for="" class="form-label">Baseline value(s) for <span
                                    class="fw-bold ">{{ $indicator }}</span></label>

                            @if ($multiple)
                                @foreach ($baselineValues as $index => $value)
                                <div class="d-block">


                                    <label for="" class="form-label">{{ $baselineValues[$index]['name'] }}</label>
                                    <input type="number" wire:model="baselineValues.{{ $index }}.baseline_value"
                                        class="form-control @error('baselineValues.' . $index . '.baseline_value') is-invalid @enderror" />

                                    @error('baselineValues.' . $index . '.baseline_value')
                                        <x-error>{{ $message }}</x-error>
                                    @enderror
   </div>
                                @endforeach
                            @else
                                <label for="" class="form-label">Value</label>
                                <input type="number" wire:model="baseline_value"
                                    class="form-control @error('baseline_value') is-invalid @enderror" />
                                @error('baseline_value')
                                    <x-error>{{ $message }}</x-error>
                                @enderror
                            @endif


                        </div>


                        <div class="gap-1 d-flex justify-content-center">
                            <button type="submit"
                                onclick="  window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });"
                                wire:loading.attr="disabled" class="btn btn-warning">Update</button>
                            <button type="button" data-bs-dismiss="modal" wire:loading.attr="disabled"
                                class="btn btn-secondary me-2"> Cancel</button>
                        </div>


                    </form>



                </x-modal>

            </div>
        </div>

    </div>
</div>
