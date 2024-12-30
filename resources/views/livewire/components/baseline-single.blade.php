<div>
    <div x-data>


        <form wire:submit="singleSave({{ $modelId }})">
            <input type="text" wire:loading.attr='disabled' wire:loading.class="bg-secondary-subtle"
                class="form-control @error('baseline_value') is-invalid @enderror" name="id"
                value="{{ $baseline_value }}" wire:model.debounce.500ms='baseline_value'>

            <div class="my-2">
                <button class="btn btn-success btn-sm" type="submit">Save</button>
                <button class="btn btn-danger btn-sm" type="button" wire:click='cancel'>Cancel</button>
            </div>
            @error('baseline_value')
                <x-error>{{ $message }}</x-error>
            @enderror



        </form>
    </div>
</div>
