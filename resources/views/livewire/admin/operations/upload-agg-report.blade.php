<div>
    <div class="my-2 ">
        <x-card-header>Instructions</x-card-header>
        <div class="card-body @if (auth()->user()->hasAnyRole('monitor')) pe-none opacity-50 @endif">
            <p class="alert bg-secondary-subtle text-uppercase">Download the template & upload your data.
            </p>

            @if ($importing && !$importingFinished)
                <div class="alert alert-warning d-flex justify-content-between" wire:poll.5000ms='checkProgress()'>
                    Importing your file
                    Please wait....

                    <div class="d-flex align-content-center">
                        <span class="text-warning fw-bold me-2"> {{ $progress }}%</span>
                        <div class="spinner-border text-warning spinner-border-sm" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>

                <div x-data class="my-2 progress progress-sm">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" role="progressbar"
                        style="width: {{ $progress . '%' }}" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
            @endif




            <form wire:submit='submitUpload'>
                <div x-data>
                    <button class="btn btn-warning" type="button" @click="$wire.downloadTemplate()"
                        @if ($importing && !$importingFinished) disabled @endif wire:loading.attr='disabled'>
                        <!-- Border spinner -->
                        <div class="mx-2 text-white opacity-30 spinner-border" style="width: 1rem; height: 1rem;"
                            wire:loading wire:target='downloadTemplate' role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        Download template <i class="bx bx-download"></i>
                    </button>
                    <hr>
                </div>

                <div class="row justify-content-center">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Organisation</label>
                                <select
                                    class="form-select @error('selectedOrganisation')
                                            is-invalid
                                        @enderror"
                                    name="" id="" wire:model="selectedOrganisation">
                                    <option value="">Select one</option>
                                    @foreach ($organisations as $organisation)
                                        <option value="{{ $organisation->id }}">{{ $organisation->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('selectedOrganisation')
                                    <x-error class="mb-1">{{ $message }}</x-error>
                                @enderror
                            </div>


                        </div>
                        <div class="col-12 col-md-6 ">

                            <div class="mb-3">
                                <label for="" class="form-label">Crop</label>
                                <select
                                    class="form-select @error('selectedCrop')
                                            is-invalid
                                        @enderror"
                                    name="" id="" wire:model="selectedOrganisation">
                                    <option value="All Crops">All Crops</option>
                                    <option value="Sweet Potato">Sweet Potato</option>
                                    <option value="Potato">Potato</option>
                                    <option value="Cassava">Cassava</option>


                                </select>
                                @error('selectedCrop')
                                    <x-error class="mb-1">{{ $message }}</x-error>
                                @enderror

                            </div>



                        </div>


                    </div>
                    <div class="col-12 @if ($importing) pe-none opacity-25 @endif ">

                        <x-filepond-single instantUpload="true" wire:model='upload' />


                        @error('upload')
                            <div class="d-flex justify-content-center">
                                <x-error class="text-center">{{ $message }}</x-error>
                            </div>
                        @enderror




                        <div class="mt-5 d-flex justify-content-center" x-data="{ disableButton: false }">
                            <button type="submit" @uploading-files.window="disableButton = true"
                                wire:loading.attr='disabled' @finished-uploading.window="disableButton = false"
                                :disabled="disableButton === true" class="px-5 btn btn-warning">
                                <!-- Border spinner -->
                                <div class="mx-2 opacity-30 spinner-border text-light"
                                    style="width: 1rem; height: 1rem;" wire:loading
                                    wire:target='submitUpload, downloadTemplate' wire:loading.attr='disabled'
                                    role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                Submit data
                            </button>
                        </div>
                    </div>
                </div>
            </form>

        </div>


    </div>
</div>
