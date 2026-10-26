@props(['title', 'subtitle' => null, 'url' => null, 'actionLabel' => 'View All'])

<div class="card w-100 h-100 border shadow-sm">
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

            @if ($url)
                <a href="{{ $url }}" class="btn btn-warning btn-sm text-nowrap flex-shrink-0">
                    {{ $actionLabel }}
                    <i class="bx bx-right-arrow-alt ms-2"></i>
                </a>
            @endif
        </div>
    </div>

    {{-- Table content --}}
    <div class="card-body p-0 d-flex flex-column">
        <div class="table-responsive flex-grow-1">
            {{ $slot }}
        </div>
    </div>
</div>
