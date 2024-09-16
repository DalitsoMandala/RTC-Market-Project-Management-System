<div>
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Add Follow Up</h4>

                    <div class="page-title-right">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="/">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">Add Data</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <h3 class="mb-5 text-center text-primary">RTC PRODUCTION AND MARKETING (FARMERS) [FOLLOW UP]</h3>
            <div class="col">
                <x-alerts />
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-8">
                <div class="card">
                    <div class="card-body">

                        <form wire:submit.debounce.1000ms="save">
                            <div wire:ignore class="mb-3" x-data="{}" x-init="() => {
                                $('#select-recruits').select2({
                                    width: '100%',
                                    theme: 'bootstrap-5',
                                    containerCssClass: 'select2--small',
                                    dropdownCssClass: 'select2--small',
                                });
                                $('#select-recruits').on('change', function(e) {
                                    data = e.target.value;

                                    setTimeout(() => {
                                        $wire.set('selectedRecruit', data);
                                    }, 1000)


                                });
                            }">
                                <label for="" class="form-label">Select Actor</label>
                                <select id="select-recruits" class="form-select" wire:model.debounce="selectedRecruit">
                                    <option selected value="">
                                        Select one
                                    </option>
                                    @foreach ($recruits as $recruit)
                                    <option value="{{ $recruit->id }}">
                                        ({{ $recruit->id }})
                                        {{ $recruit->name_of_actor }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="hide" x-data="{
                                show: $wire.entangle('show')

                            }" :class="{ 'pe-none opacity-25': show === false }">
                                <div class="mb-3">
                                    <label for="" class="form-label">Date of follow up</label>
                                    <input readonly type="date" class="form-control bg-light @error('date_of_follow_up') is-invalid @enderror" wire:model="date_of_follow_up" />
                                    @error('date_of_follow_up')
                                    <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>

                                <!-- Group -->
                                <div class="mb-3" x-data="{ group: $wire.entangle('group') }">
                                    <label for="group" class="form-label">Group</label>
                                    <select class="form-select @error('group') is-invalid @enderror bg-light" x-model="group" disabled>
                                        <option value="">Select One</option>
                                        <option value="Early generation seed producer">
                                            Early generation seed producer
                                        </option>
                                        <option value="Seed multiplier">
                                            Seed multiplier
                                        </option>
                                        <option value="Rtc producer">
                                            Rtc producer
                                        </option>
                                    </select>

                                    @error('group')
                                    <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="" class="form-label">ENTERPRISE</label>
                                    <x-text-input style="background: #f8f9fa" disabled wire:model="location_data.enterprise" :class="$errors->has('location_data.enterprise') ? 'is-invalid' : ''" />
                                    @error('location_data.enterprise')
                                    <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="" class="form-label">DISTRICT</label>
                                    <select disabled style="background: #f8f9fa" class="form-select @error('location_data.district') is-invalid @enderror" wire:model="location_data.district">
                                        @include('layouts.district-options')
                                    </select>
                                    @error('location_data.district')
                                    <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="" class="form-label">EPA</label>
                                    <x-text-input style="background: #f8f9fa" disabled wire:model="location_data.epa" :class="$errors->has('location_data.epa') ? 'is-invalid' : ''" />
                                    @error('location_data.epa')
                                    <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="" class="form-label">SECTION</label>
                                    <x-text-input style="background: #f8f9fa" disabled wire:model="location_data.section" :class="$errors->has('location_data.section') ? 'is-invalid' : ''" />
                                    @error('location_data.section')
                                    <x-error>{{ $message }}</x-error>
                                    @enderror
                                </div>

                                <hr />
                                @include('livewire.forms.rtc-market.rtc-production-farmers.follow-up-data')
                                @include('livewire.forms.rtc-market.rtc-production-farmers.repeats')

                                <div class="d-grid col-12 justify-content-center" x-data>
                                    <button class="btn btn-primary btn-lg" @click="window.scrollTo({
                                    top: 0,
                                    behavior: 'smooth'
                                })" type="submit">
                                        Submit
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
