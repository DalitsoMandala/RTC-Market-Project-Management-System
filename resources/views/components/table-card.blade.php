@props([
    'title',
    'subtitle' => null,
    'url' => null,
    'actionLabel' => 'View All',
    'actions' => null,
    'isResponsive' => false,
    'isContent' => false,
])

<div class="card w-100    shadow-sm">
    {{-- Header --}}
    <div class="card-header bg-transparent border-bottom py-3">
        <div class="d-flex align-items-center justify-content-between gap-3">
            <div class="min-w-0">
                <h5 class="card-title mb-1">
                    {{ $title }}
                </h5>

                @if ($subtitle)
                    <p class="text-muted small mb-0">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>

            {{-- Custom action or default link --}}
            <div class="flex-shrink-0">
                @if (isset($actions))
                    {{ $actions }}
                @elseif ($url)
                    <a href="{{ $url }}" class="btn btn-warning btn-sm text-nowrap">
                        {{ $actionLabel }}
                        <i class="bx bx-right-arrow-alt ms-2"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Table content --}}
    <div class="card-body p-0 d-flex flex-column">
        <div class="{{ $isResponsive ? 'table-responsive' : '' }} flex-grow-1">
            @if ($isContent)
                <div class="p-3">
                    {{ $slot }}
                </div>
            @else
                {{ $slot }}
            @endif
        </div>
    </div>
</div>
