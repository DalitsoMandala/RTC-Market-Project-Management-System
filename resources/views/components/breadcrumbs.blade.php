@unless (empty($breadcrumbs))
    <div class="page-title-box col-12">
        <div class="page-title-left">
            <ol class="m-0 breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ url('/') }}">Dashboard</a>
                </li>

                @foreach ($breadcrumbs as $index => $crumb)
                    @php
                        $isLast = $index === count($breadcrumbs) - 1;
                    @endphp

                    @if ($isLast || empty($crumb['route']) || !Route::has($crumb['route']))
                        <li class="breadcrumb-item active">{{ $crumb['label'] }}</li>
                    @else
                        <li class="breadcrumb-item">
                            <a href="{{ route($crumb['route']) }}">{{ $crumb['label'] }}</a>
                        </li>
                    @endif
                @endforeach
            </ol>
        </div>
    </div>
@endunless
