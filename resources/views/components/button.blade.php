@props([
    'type' => 'button',
    'variant' => 'warning',
    'size' => 'sm',
    'loadingText' => 'Processing...',
    'icon' => null,
    'padding' => false,
])

<button
    type="{{ $type }}"
    {{
        $attributes->class([
            'btn',
            'btn-' . $variant,
            'btn-' . $size,
            'd-inline-flex',
            'align-items-center',
            'justify-content-center',
            'gap-2',
            'px-5 py-2' => $padding,
        ])
    }}
    wire:loading.attr="disabled"
>
    <span
        wire:loading
        class="spinner-border spinner-border-sm"
        role="status"
        aria-hidden="true"
    ></span>

    <span wire:loading class="visually-hidden">
        {{ $loadingText }}
    </span>

    <span wire:loading.remove class="d-inline-flex align-items-center gap-2">
        @if ($icon)
            <i class="{{ $icon }}" aria-hidden="true"></i>
        @endif

        {{ $slot }}
    </span>
</button>
