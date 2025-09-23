

<button {{ $attributes->merge(['class' => "btn $color waves-effect btn-label waves-light"]) }}>
    @if($icon)
        <i class="{{ $icon }} label-icon"></i>
    @endif
    {{ $slot }}
</button>
