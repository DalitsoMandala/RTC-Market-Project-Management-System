<div>




    <div class="my-2 row">
        <x-alpine-alerts />
        <div class="col-12">
            <form wire:submit.prevent="saveIndicator" wire:loading.class="opacity-25 pe-none">
                <h5>Add New Indicator</h5>

                <div class="row" x-data="{
                    fileName: $wire.entangle('fileName'),
                    indicatorNumber: $wire.entangle('indicatorNumber').live,
                    fillName() {
                        if (this.indicatorNumber === null || this.indicatorNumber === undefined || this.indicatorNumber === '') {
                            return null;
                        }


                        return 'App\\Helpers\\rtc_market\\indicators\\indicator_' + this.indicatorNumber.replace(/\./g, '_').toUpperCase();
                    }
                }">
                    <div class="col-12 col-md-4">
                        <div class="mb-3">
                            <label for="" class="form-label">Indicator Name</label>
                            <input wire:model="indicatorName" type="text"
                                class="form-control @error('indicatorName') is-invalid @enderror"
                                placeholder="Name of indicator..." aria-describedby="helpId" />
                            @error('indicatorName')
                                <x-error>{{ $message }}</x-error>
                            @enderror
                        </div>

                    </div>
                    <div class="col-12 col-md-4">
                        <div class="mb-3">
                            <label for="" class="form-label">Indicator #</label>
                            <input x-model="indicatorNumber" @input="  fileName = fillName();" wire:model="indicatorNumber"
                                type="text" class="form-control @error('indicatorNumber') is-invalid @enderror"
                                placeholder="Name of indicator..." aria-describedby="helpId" />
                            @error('indicatorNumber')
                                <x-error>{{ $message }}</x-error>
                            @enderror
                        </div>

                    </div>
                    <div class="col-12 col-md-12">
                        <div class="mb-3">
                            <label for="" class="form-label">File Name
                            </label>
                            <input readonly wire:model="fileName" type="text" class="form-control @error('fileName')
                                is-invalid
                            @enderror"
                                placeholder="File Name..." aria-describedby="helpId" />
                            @error('fileName')
                                <x-error>{{ $message }}</x-error>
                            @enderror
                        </div>

                    </div>

                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Add Disaggregations for this indicator</label>

                            @foreach ($disaggregations as $key => $disaggregation)
                                <div class="gap-2 mb-2 d-flex align-items-center">
                                    <div class="col-6">
                                        <input type="text"
                                            class="form-control @error('disaggregations.' . $key . '.name') is-invalid @enderror"
                                            placeholder="Enter Name..."
                                            wire:model="disaggregations.{{ $key }}.name" />

                                        @error('disaggregations.' . $key . '.name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <button type="button" class="btn btn-danger"
                                        wire:click="removeDisaggregation({{ $key }})"
                                        wire:loading.attr="disabled"
                                        wire:target="removeDisaggregation({{ $key }})"
                                        @disabled(count($disaggregations) === 1)>
                                        -
                                    </button>
                                </div>
                            @endforeach

                            <button type="button" class="mt-2 btn btn-success" wire:click="addDisaggregation"
                                wire:loading.attr="disabled" wire:target="addDisaggregation">
                                +
                            </button>

                            @error('disaggregations')
                                <div class=" text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12 d-flex justify-content-end">
                        <button class="btn btn-success">Save Indicator</button>
                    </div>



                </div>


            </form>
        </div>
    </div>





</div>
