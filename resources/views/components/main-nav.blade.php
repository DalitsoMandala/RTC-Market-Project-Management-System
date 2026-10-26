@props(['items'])

@if (!empty($items))
    <ul class="navbar-nav me-auto">
        @foreach ($items as $item)
            @if (!empty($item['children']))
                <li @class([
                    'nav-item' => true,
                    'dropdown' => true,
                    'active' => $item['active'] ?? false,
                ])>
                    <a href="#" @class([
                        'nav-link',
                        'dropdown-toggle',
                        'active' => $item['active'] ?? false,
                    ]) role="button" data-bs-toggle="dropdown">
                        @if (!empty($item['icon']))
                            <i class='bx {{ $item['icon'] }} me-1'></i>
                        @endif
                        {{ $item['label'] }}
                    </a>
                    <ul class="dropdown-menu">
                        @foreach ($item['children'] as $child)
                            <x-main-nav-item :item="$child" :depth="1" />
                        @endforeach
                    </ul>
                </li>
            @else
                <li class="nav-item">
                    <a href="{{ route($item['route']) }}" @class(['nav-link', 'active' => $item['active'] ?? false])>
                        @if (!empty($item['icon']))
                            <i class='bx {{ $item['icon'] }} me-1'></i>
                        @endif
                        {{ $item['label'] }}
                    </a>
                </li>
            @endif
        @endforeach
    </ul>
@endif
