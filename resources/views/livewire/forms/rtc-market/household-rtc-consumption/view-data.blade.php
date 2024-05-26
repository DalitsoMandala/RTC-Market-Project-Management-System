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
                            <li class="breadcrumb-item"><a href="../../forms">Forms</a></li>
                            <li class="breadcrumb-item active">View</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header" x-data="{
                        is_open: false
                    }">
                        <a class="btn btn-primary" href="add" role="button">Add
                            Data +</a>
                        <a class="btn btn-primary" href="#" data-toggle="modal" role="button"
                            @click="is_open = !is_open">
                            Import </a>



                        <div class="my-2 border shadow-none card card-body" x-show="is_open">
                            <h5> Instructions</h5>
                            <p class="alert bg-info-subtle">Download the household
                                RTC
                                consumption template & fill some small details in the form below after uploading your
                                data.</p>

                            <form>
                                <div>
                                    <a class="btn btn-soft-primary" href="#" data-toggle="modal" role="button">
                                        Download template <i class="bx bx-download"></i> </a>
                                    <hr>
                                </div>
                                <div id="table-form">
                                    <div class="row">
                                        <div class=" col-12 col-md-8">

                                            <div class="mb-3">
                                                <label for="" class="form-label">ENTERPRISE</label>
                                                <x-text-input wire:model='enterprise' />
                                                @error('enterprise')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="" class="form-label">DISTRICT</label>
                                                <select class="form-select" wire:model='district'>
                                                    <option selected>Select a district</option>
                                                    <option>Balaka</option>
                                                    <option>Blantyre</option>
                                                    <option>Chikwawa</option>
                                                    <option>Chiradzulu</option>
                                                    <option>Chitipa</option>
                                                    <option>Dedza</option>
                                                    <option>Dowa</option>
                                                    <option>Karonga</option>
                                                    <option>Kasungu</option>
                                                    <option>Lilongwe</option>
                                                    <option>Machinga</option>
                                                    <option>Mangochi</option>
                                                    <option>Mchinji</option>
                                                    <option>Mulanje</option>
                                                    <option>Mwanza</option>
                                                    <option>Mzimba</option>
                                                    <option>Neno</option>
                                                    <option>Nkhata Bay</option>
                                                    <option>Nkhotakota</option>
                                                    <option>Nsanje</option>
                                                    <option>Ntcheu</option>
                                                    <option>Ntchisi</option>
                                                    <option>Phalombe</option>
                                                    <option>Rumphi</option>
                                                    <option>Salima</option>
                                                    <option>Thyolo</option>
                                                    <option>Zomba</option>
                                                </select>
                                                @error('district')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label for="" class="form-label">EPA</label>
                                                <x-text-input wire:model='epa' />
                                                @error('epa')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label for="" class="form-label">SECTION</label>
                                                <x-text-input wire:model='section' />
                                                @error('section')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>


                                        </div>
                                        <div class="col-12 col-md-4">
                                            <x-filepond-single wire:model='upload' />
                                        </div>
                                    </div>

                                </div>
                            </form>

                            <small></small>
                        </div>
                    </div>
                    <div class="card-body">
                        <livewire:household-rtc-consumption-table />
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

</div>
