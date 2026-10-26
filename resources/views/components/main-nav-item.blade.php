@props(['item', 'depth' => 1])

@if (!empty($item['children']))
    <li class="dropdown-submenu">
        <a href="#" @class([
            'dropdown-item',
            'dropdown-toggle',
            'active' => $item['active'] ?? false,
        ])>
            {{ $item['label'] }}
        </a>
        <ul class="dropdown-menu">
            @foreach ($item['children'] as $child)
                <x-main-nav-item :item="$child" :depth="$depth + 1" />
            @endforeach
        </ul>
    </li>
@else
    <li>
        <a href="{{ route($item['route']) }}" @class(['dropdown-item', 'active' => $item['active'] ?? false])>
            {{ $item['label'] }}
        </a>
    </li>
@endif
