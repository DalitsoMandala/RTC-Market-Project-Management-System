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
                            inputed: {},
                            disabledButton: false,
                            checkValidation() {
                                let forms = document.querySelectorAll('.needs-validation');
                                let isValid = true;

                                // Loop over them and prevent submission
                                Array.from(forms).forEach(form => {
                                    if (!form.checkValidity()) {
                                        form.classList.add('was-validated');
                                        isValid = false;
                                    }
                                });

                                if (isValid) {
                                    // Trigger Livewire form submission if the form is valid
                                    let values = this.inputed;
                                    this.disabledButton = true;
                                    setTimeout(() => {
                                        this.disabledButton = false;
                                        $wire.save(values);
                                    }, 2000)

                                    this.inputed = {};

                                    Array.from(forms).forEach(form => {

                                        form.classList.remove('was-validated');


                                    });

                                }
                            }
                        }">
                            <form x-ref="livewireForm" class="needs-validation" @submit.prevent="checkValidation()"
                                novalidate>


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