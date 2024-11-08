<div>

    <x-required-notice />
    <x-alerts />
    <form wire:submit.prevent="save">
        <div class="row my-5">
            @foreach ($disaggregations as $disaggregation)
                <div class="mb-3 col">
                    <label for="disaggregation_{{ $disaggregation->id }}" class="form-label">
                        {{ $disaggregation->name }} <!-- Assuming you have a 'name' column in your model -->
                    </label>
                    <input type="number" id="disaggregation_{{ $disaggregation->id }}"
                        wire:model="inputs.{{ $disaggregation->id }}"
                        class="form-control @error('inputs.' . $disaggregation->id) is-invalid @enderror">
                    @error('inputs.' . $disaggregation->id)
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            @endforeach
        </div>

        <div class="d-grid col-12 justify-content-center">
            <button class="btn btn-primary px-5" type="submit">
                Submit
            </button>
        </div>
    </form>
</div>