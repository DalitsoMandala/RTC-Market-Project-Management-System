<div wire:ignore x-cloak x-data x-init="() => {
    flatpickr($refs.input, {});
}">
    <input {{ $attributes->merge(['class' => 'form-control flapickr-full-width ', 'placeholder' => 'Choose date']) }}
        x-ref="input" />

</div>
