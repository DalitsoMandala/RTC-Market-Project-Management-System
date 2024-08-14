@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-sm alert alert-danger']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
