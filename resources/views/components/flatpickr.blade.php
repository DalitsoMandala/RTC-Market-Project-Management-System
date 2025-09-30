@props(['maxDate' => null, 'enableTime' => false, 'time' =>'00:00'])

<div wire:ignore x-cloak x-data="{
    enableTime: @json($enableTime),
    maxDate: {{ $maxDate ? "'$maxDate'" : 'null' }},
    time: {{ "'$time'"}}
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
