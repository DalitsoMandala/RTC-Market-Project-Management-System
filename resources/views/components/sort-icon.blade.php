{{-- resources/views/components/sort-icon.blade.php --}}
@props(['field', 'current', 'dir'])

@if ($current === $field)
    <i class="bx bx-chevron-{{ $dir === 'asc' ? 'up' : 'down' }}-fill ms-1 opacity-75" style="font-size:.7rem;"></i>
@else
    <i class="opacity-25 bx bx-chevron-down ms-1" style="font-size:.7rem;"></i>
@endif
