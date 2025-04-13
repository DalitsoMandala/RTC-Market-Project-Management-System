@props(['maxDate' => null])

<div wire:ignore x-cloak x-data x-init="() => {
    flatpickr($refs.input, {
        maxDate: {{ $maxDate ? "'$maxDate'" : 'null' }}
    });
}">
    <input {{ $attributes->merge(['class' => 'form-control flapickr-full-width', 'placeholder' => 'Choose date']) }}
        x-ref="input" />
</div>
