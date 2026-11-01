@props(['maxDate' => null, 'enableTime' => false, 'time' => '00:00'])

@php
    // Check if any error exists for this input's wire:model attribute
    // Alternatively, you can pass an explicit error state or let the wrapper handle it
    $hasError = $attributes->get('class') && str_contains($attributes->get('class'), 'is-invalid');
@endphp

<div class="{{ $hasError ? 'has-error' : '' }}">
    <div wire:ignore x-cloak x-data="{
        enableTime: @json($enableTime),
        maxDate: {{ $maxDate ? "'$maxDate'" : 'null' }},
        time: {{ "'$time'" }}
    }" x-init="() => {
        flatpickr($refs.input, {
            maxDate: maxDate,
            enableTime: enableTime,
            dateFormat: 'Y-m-d H:i:s',
        });
    }">
        <input {{ $attributes->merge(['class' => 'form-control flapickr-full-width', 'placeholder' => 'Choose date']) }}
            x-ref="input" />
    </div>

    {{-- Optional: Display error message directly inside component if needed --}}
</div>
<style>
    /* Add this to your CSS file */
.has-error .form-control {
    border-color: #dc3545;
    padding-right: calc(1.5em + 0.75rem);
    background-image: url("data:image/svg+xml,..."); /* optional bootstrap invalid icon */
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}
</style>
