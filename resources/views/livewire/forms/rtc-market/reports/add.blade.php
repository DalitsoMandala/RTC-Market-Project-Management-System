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
                <x-alerts />

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Enter your data for : <span
                                class="text-primary">{{ $indicatorName }}</span> </h4>
                    </div>
                    <div class="card-body">
                        <div x-data="{
                            data: $store.disagg.dataArray,
                            name: @js($indicatorName),
                            inputed: {},
                            percentage: false,
                            disabledButton: false,
                            checkValidation() {
                                // Prevent further submissions if already submitted
                                if (this.submitted) return;
                        
                                let form = this.$refs.livewireForm; // Reference the first form
                                if (form.checkValidity()) {
                                    let values = this.inputed;
                                    this.disabledButton = true;
                                    this.submitted = true; // Mark as submitted
                        
                                    setTimeout(() => {
                                        $wire.save(values);
                                        this.resetForm(); // Reset form after submission
                                    }, 2000);
                                } else {
                                    form.classList.add('was-validated'); // Add validation feedback
                                }
                            },
                        
                            checkValidationNormal() {
                                // Prevent further submissions if already submitted
                                if (this.submitted) return;
                        
                                let form = this.$refs.livewireForm2; // Reference the second form
                                if (form.checkValidity()) {
                                    let values = this.inputed;
                                    this.disabledButton = true;
                                    this.submitted = true; // Mark as submitted
                        
                                    setTimeout(() => {
                                        $wire.save(values);
                                        this.resetForm(); // Reset form after submission
                                    }, 2000);
                                } else {
                                    form.classList.add('was-validated'); // Add validation feedback
                                }
                            },
                        
                        
                        }" x-init="() => {
                        
                        
                            if (name.includes('Percentage')) {
                                percentage = true;
                            } else {
                                percentage = false;
                            }
                        
                        
                        
                        
                        
                        }">
                            <form x-ref="livewireForm" class="needs-validation" @submit.prevent="checkValidation()"
                                novalidate x-show="percentage">

                                <div class="row">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="manual-input" class="form-label">Annual value</label>
                                            <input readonly type="number" class="form-control bg-light"
                                                id="manual-input" placeholder="Enter value"
                                                x-model="inputed['Annual value']" />
                                            <div class="invalid-feedback">
                                                This field requires a value.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="manual-input" class="form-label">Baseline value</label>
                                            <input readonly type="number" class="form-control bg-light" id="base-input"
                                                placeholder="Enter value" x-model="inputed['Baseline value']" />
                                            <div class="invalid-feedback">
                                                This field requires a value.
                                            </div>
                                        </div>
                                    </div>



                                </div>
                                <template x-for="(input, index) in data" :key="index">
                                    <div class="mb-3">
                                        <label for="" class="form-label" x-text="input.name"></label>
                                        <input :readonly="input.name === 'Total(% Percentage)'"
                                            :class="{ 'bg-light': input.name === 'Total(% Percentage)' }"
                                            :id="'input' + index" type="number" class="form-control"
                                            :required="input.name !== 'Total(% Percentage)'" placeholder="Enter value"
                                            aria-describedby="helpId" x-model='inputed[input.name]' />
                                        <div class="invalid-feedback">
                                            This field requires a value.
                                        </div>

                                    </div>


                                </template>



                                <div class="text-center">
                                    <button type="submit" :disabled="disabledButton" class="btn btn-primary btn-lg">
                                        Submit
                                    </button>
                                </div>


                            </form>


                            <form x-ref="livewireForm2" class="needs-validation"
                                @submit.prevent="checkValidationNormal()" novalidate x-show="percentage=== false">


                                <template x-for="(input, index) in data" :key="index">
                                    <div class="mb-3">
                                        <label for="" class="form-label" x-text="input.name"></label>
                                        <input :id="'input' + index" type="number" required class="form-control"
                                            placeholder="Enter value" aria-describedby="helpId"
                                            x-model='inputed[input.name]' />
                                        <div class="invalid-feedback">
                                            This field requires a value.
                                        </div>

                                    </div>


                                </template>

                                <div class="text-center">
                                    <button type="submit" :disabled="disabledButton" class="btn btn-primary btn-lg">
                                        Submit
                                    </button>
                                </div>


                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-scroll-up />

    @script
        <script>
            Alpine.store('disagg', {
                dataArray: @js($inputs)
            })
        </script>
    @endscript



</div>

</div>
