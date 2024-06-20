<div>


    <style>
        input,
        select,
        label {
            text-transform: uppercase;
        }
    </style>
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-end">

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="../../">Forms</a></li>
                            <li class="breadcrumb-item active">Add</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row justify-content-center">
            <div class="col-8">

                <h3 class="mb-5 text-center text-primary">HOUSEHOLD CONSUMPTION FORM</h3>

                @if (session()->has('success'))
                    <x-success-alert>{!! session()->get('success') !!}</x-success-alert>
                @endif
                @if (session()->has('error'))
                    <x-error-alert>{!! session()->get('error') !!}</x-error-alert>
                @endif


                @if ($openSubmission === false)
                    <div class="alert alert-warning" role="alert">
                        You can not submit a form right now
                        because submissions are closed for the moment!
                    </div>
                @endif
                <div class="mb-1 row justify-content-center @if ($openSubmission === false) opacity-25  pe-none @endif"
                    x-data="{
                        selectedFinancialYear: $wire.entangle('selectedFinancialYear').live,
                        selectedMonth: $wire.entangle('selectedMonth').live,
                        selectedIndicator: $wire.entangle('selectedIndicator').live,
                    }">

                    <form wire:submit='save'>
                        @include('livewire.forms.rtc-market.period-view')
                        <span x-text="console.log(selectedFinancialYear)"></span>
                        <div class="row "
                            :class="{
                                'pe-none opacity-25': !(selectedFinancialYear !== '' &&
                                    selectedFinancialYear !== null &&
                                    selectedMonth !== '' && selectedMonth !== null &&
                                    selectedIndicator !== '' && selectedIndicator !== null)
                            }">
                            <div class="card col-12">
                                <div class="card-header fw-bold text-uppercase">Location</div>
                                <div class="card-body">
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
                                            <option>BALAKA</option>
                                            <option>BLANTYRE</option>
                                            <option>CHIKWAWA</option>
                                            <option>CHIRADZULU</option>
                                            <option>CHITIPA</option>
                                            <option>DEDZA</option>
                                            <option>DOWA</option>
                                            <option>KARONGA</option>
                                            <option>KASUNGU</option>
                                            <option>LILONGWE</option>
                                            <option>MACHINGA</option>
                                            <option>MANGOCHI</option>
                                            <option>MCHINJI</option>
                                            <option>MULANJE</option>
                                            <option>MWANZA</option>
                                            <option>MZIMBA</option>
                                            <option>NENO</option>
                                            <option>NKHATA BAY</option>
                                            <option>NKHOTAKOTA</option>
                                            <option>NSANJE</option>
                                            <option>NTCHEU</option>
                                            <option>NTCHISI</option>
                                            <option>PHALOMBE</option>
                                            <option>RUMPHI</option>
                                            <option>SALIMA</option>
                                            <option>THYOLO</option>
                                            <option>ZOMBA</option>
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
                            </div>

                        </div>

                        <div class="row"
                            :class="{
                                'pe-none opacity-25': !(selectedFinancialYear !== '' &&
                                    selectedFinancialYear !== null &&
                                    selectedMonth !== '' && selectedMonth !== null &&
                                    selectedIndicator !== '' && selectedIndicator !== null)
                            }">
                            <div class="col-12 ">

                                @php
                                    $form_count = 0;
                                @endphp
                                @foreach ($inputs as $key => $value)
                                    <div class="card">


                                        <div
                                            x-data ="{
                                    currentIndex : '{{ $key }}',

                                }"class="card-header fw-bold d-flex justify-content-between align-items-center text-uppercase">
                                            <div>
                                                Questions <span
                                                    class="badge bg-warning d-none">{{ ++$form_count }}</span>
                                            </div>

                                            <span class="d-none"><button wire:click='removeInput({{ $key }})'
                                                    :disabled="parseInt(currentIndex) === 0" type="button"
                                                    class="btn btn-danger">
                                                    Remove
                                                </button></span>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="assessmentDate" class="form-label">Date of Assessment
                                                    (YY/MM/DD)
                                                </label>
                                                <input type="date" class="form-control"
                                                    wire:model="inputs.{{ $key }}.date_of_assessment">

                                                @error('inputs.' . $key . '.date_of_assessment')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="actorType" class="form-label">Actor Type</label>
                                                <select class="form-select"
                                                    wire:model="inputs.{{ $key }}.actor_type">

                                                    <option value="FARMER">Farmer</option>
                                                    <option value="PROCESSOR">Processor</option>
                                                    <option value="TRADER">Trader</option>
                                                    <option value="INDIVIDUALS FROM NUTRITION INTERVENTION">Individuals
                                                        from
                                                        Nutrition
                                                        Interventions</option>
                                                    <option value="OTHER">Other</option>
                                                </select>
                                                @error('inputs.' . $key . '.actor_type')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="rtcGroup" class="form-label">RTC Group/Platform</label>
                                                <select class="form-select"
                                                    wire:model="inputs.{{ $key }}.rtc_group_platform">

                                                    <option value="Household">Household</option>
                                                    <option value="Seed">Seed Producer</option>
                                                    {{-- <option value="Producer Organisation">Producer Organisation</option> --}}
                                                </select>
                                                @error('inputs.' . $key . '.rtc_group_platform')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="producerOrganisation" class="form-label">Name of Producer
                                                    Organisation</label>
                                                <input type="text" class="form-control"
                                                    wire:model="inputs.{{ $key }}.producer_organisation">
                                                @error('inputs.' . $key . '.producer_organisation')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="actorName" class="form-label">Name of Actor</label>
                                                <input type="text" class="form-control"
                                                    wire:model="inputs.{{ $key }}.actor_name">
                                                @error('inputs.' . $key . '.actor_name')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="ageGroup" class="form-label">Age Group</label>
                                                <select class="form-select"
                                                    wire:model="inputs.{{ $key }}.age_group">

                                                    <option value="" selected>Select Age Group</option>
                                                    <option value="YOUTH">Youth (18-35 yrs)</option>
                                                    <option value="NOT YOUTH">Not Youth (35+ yrs)</option>
                                                </select>
                                                @error('inputs.' . $key . '.age_group')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="sex" class="form-label">Sex</label>
                                                <select class="form-select"
                                                    wire:model="inputs.{{ $key }}.sex">

                                                    <option value="MALE">Male</option>
                                                    <option value="FEMALE">Female</option>
                                                </select>
                                                @error('inputs.' . $key . '.sex')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="phoneNumber" class="form-label">Phone Number</label>
                                                <x-phone type="tel" class="form-control"
                                                    wire:model="inputs.{{ $key }}.phone_number" />
                                                @error('inputs.' . $key . '.phone_number')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="householdSize" class="form-label">Household Size</label>
                                                <input type="number" class="form-control"
                                                    wire:model="inputs.{{ $key }}.household_size">
                                                @error('inputs.' . $key . '.household_size')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="under5" class="form-label">Number of Under 5 in
                                                    Household</label>
                                                <input type="number" class="form-control"
                                                    wire:model="inputs.{{ $key }}.under_5_in_household">
                                                @error('inputs.' . $key . '.under_5_in_household')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror

                                            </div>

                                            <div class="mb-3">
                                                <label for="consumingRTC" class="form-label">Number of People in
                                                    Household
                                                    Who
                                                    Consume
                                                    RTC and Their
                                                    Derived Products</label>
                                                <div class="gap-2 border shadow-none card card-body">
                                                    <div class=" row">
                                                        <div class="col-12 col-md-2 col-form-label"> <label
                                                                for="">Total</label></div>
                                                        <div class="col"> <input type="number" placeholder=""
                                                                class="form-control "
                                                                wire:model="inputs.{{ $key }}.rtc_consumers">
                                                            @error('inputs.' . $key . '.rtc_consumers')
                                                                <x-error>{{ $message }}</x-error>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class=" row">
                                                        <div class="col-12 col-md-2 col-form-label"> <label
                                                                for="">CASSAVA</label></div>
                                                        <div class="col">
                                                            <input type="number" placeholder="" class="form-control"
                                                                wire:model="inputs.{{ $key }}.rtc_consumers_cassava">

                                                            @error('inputs.' . $key . '.rtc_consumers_cassava')
                                                                <x-error>{{ $message }}</x-error>
                                                            @enderror
                                                        </div>


                                                    </div>

                                                    <div class="row ">
                                                        <div class="col-12 col-md-2 col-form-label"> <label
                                                                for="">SWEET
                                                                POTATO</label></div>
                                                        <div class="col"> <input type="number" placeholder=""
                                                                class="form-control"
                                                                wire:model="inputs.{{ $key }}.rtc_consumers_sw_potato">
                                                            @error('inputs.' . $key . '.rtc_consumers_sw_potato')
                                                                <x-error>{{ $message }}</x-error>
                                                            @enderror
                                                        </div>



                                                    </div>

                                                    <div class=" row">
                                                        <div class="col-12 col-md-2 col-form-label"> <label
                                                                for="">POTATO</label></div>
                                                        <div class="col"> <input type="number" placeholder=""
                                                                class="form-control"
                                                                wire:model="inputs.{{ $key }}.rtc_consumers_potato">

                                                            @error('inputs.' . $key . '.rtc_consumers_potato')
                                                                <x-error>{{ $message }}</x-error>
                                                            @enderror
                                                        </div>



                                                    </div>



                                                </div>

                                            </div>
                                            <div class="mb-3">
                                                <label for="rtcConsumptionFrequency" class="form-label">Frequency of
                                                    RTC
                                                    and
                                                    Derived
                                                    Products
                                                    Consumption per Week (Number)</label>
                                                <input type="number" class="form-control"
                                                    wire:model.live="inputs.{{ $key }}.rtc_consumption_frequency">

                                                @error('inputs.' . $key . '.rtc_consumption_frequency')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror

                                            </div>
                                            <div class="mb-3">
                                                <label for="mainFood" class="form-label">Which of These RTC and Their
                                                    Derived
                                                    Products
                                                    Do You Use
                                                    as the Main Food (Breakfast, Lunch and Dinner)? (Multiple
                                                    Options)</label>

                                                <div class="list-group">
                                                    <label class="list-group-item">
                                                        <input class="form-check-input me-1" type="checkbox"
                                                            wire:model='inputs.{{ $key }}.main_food'
                                                            value="CASSAVA" />
                                                        CASSAVA
                                                    </label>
                                                    <label class="list-group-item">
                                                        <input class="form-check-input me-1" type="checkbox"
                                                            wire:model='inputs.{{ $key }}.main_food'
                                                            value="POTATO" />
                                                        POTATO
                                                    </label>
                                                    <label class="list-group-item">
                                                        <input class="form-check-input me-1"
                                                            wire:model='inputs.{{ $key }}.main_food'
                                                            type="checkbox" value="SWEET POTATO" />
                                                        SWEET POTATO
                                                    </label>

                                                </div>

                                                @error('inputs.' . $key . '.main_food')
                                                    <x-error>{{ $message }}</x-error>
                                                @enderror
                                            </div>

                                        </div>


                                    </div>
                                @endforeach


                            </div>
                        </div>

                        <div class="d-grid justify-content-center">
                            <button class="btn btn-primary d-none" type="button" wire:click="addInput">Add More
                                +</button>
                            <button class="btn btn-success btn-lg" type="submit">Submit</button>
                        </div>
                    </form>
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
            let textInputs = document.querySelectorAll('input[type="text"]');

            // Attach event listener to each input
            textInputs.forEach(function(input) {
                input.addEventListener('input', function() {
                    // Convert input value to uppercase
                    this.value = this.value.toUpperCase();
                });

            });
            $wire.on('to-top', () => {

                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                })
            });
        </script>
    @endscript
</div>
