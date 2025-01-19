<div class="page-title-right">
    <ol class="m-0 breadcrumb">
        <li class="breadcrumb-item"><a href="/">Dashboard</a></li>

        {{-- Breadcrumb for "Add Data" --}}
        <li class="breadcrumb-item @if (str_contains($currentUrl, 'add')) active @endif">
            <a class="@if (str_contains($currentUrl, 'add')) d-none @endif" href="{{ $currentUrl }}">Add Data</a>
            @if (str_contains($currentUrl, 'add'))
                Add Data
            @endif
        </li>

        {{-- Breadcrumb for "Upload Data" --}}
        <li class="breadcrumb-item @if (str_contains($currentUrl, 'upload')) active @endif">
            <a class="@if (str_contains($currentUrl, 'upload')) d-none @endif" href="{{ $replaceUrl }}">Upload Data</a>
            @if (str_contains($currentUrl, 'upload'))
                Upload Data
            @endif
        </li>
    </ol>
</div>
