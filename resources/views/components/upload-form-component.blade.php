<div>
    @php
        use Ramsey\Uuid\Uuid;
        use illuminate\Support\Facades\Route;
        $currentUrl = url()->current();
        $uuid = Route::current()->parameters()['uuid'] ?? null;
        $newUuid = Uuid::uuid4()->toString();
        $addDataRoute = $uuid ? str_replace($uuid, '', $currentUrl) : $currentUrl;
        $addDataRoute = str_replace('upload', 'add', $addDataRoute);
        $routePrefix = Route::current()->getPrefix();
    @endphp
    @section('title')
        {{ $pageTitle }}
    @endsection

    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-block align-items-center justify-content-between">
                    <h4 class="mb-2">{{ $pageTitle }}</h4>

                    <div class="page-title-right" wire:ignore>


                        <ol class="m-0 breadcrumb">


                            <li class="breadcrumb-item">
                                <a href="/">Dashboard</a>
                            </li>

                            @role('admin|manager')
                                <li class="breadcrumb-item">
                                    <a href="/cip/submission-period">Submission Periods</a>
                                </li>
                            @endrole

                            @role('external')
                                <li class="breadcrumb-item"></li>
                                <a href="/external/submission-periods">Submission Periods</a>
                                </li>
                            @endrole

                            <li class="breadcrumb-item "> <a href="{{ $addDataRoute }}">Add Data</a></li>
                            <li class="breadcrumb-item active">
                                Upload Data
                            </li>
                            <li class="breadcrumb-item">

                                <a href="{{ $routePrefix }}/forms/rtc-market/{{ $formRoute }}/view">
                                    {{ ucwords(strtolower($formName)) }} Data
                                </a>
                            </li>



                        </ol>

                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">
                <h3 class="mb-5 text-center text-warning">{{ $formName }}</h3>

                <x-alerts />

                @isset($selectedMonth, $selectedFinancialYear)
                    <x-period-detail :period="$selectedMonth" :year="$selectedFinancialYear" />
                @endisset

                @if (!$targetSet && isset($targetIds))
                    <livewire:forms.rtc-market.set-targets-form :submissionTargetIds="$targetIds" />
                @endif

                @if (!$openSubmission)
                    <div class="alert alert-danger" role="alert">
                        You can not submit a form right now
                        because submissions are closed for the moment!
                    </div>
                @endif

                <div
                    class="my-2 border shadow-none card card-body @if (!$openSubmission) opacity-25 pe-none @endif">
                    <h5> Instructions</h5>
                    <p class="alert bg-secondary-subtle text-uppercase">Download the template & upload your data.</p>

                    @if ($importing && !$importingFinished)
                        <div class="alert alert-warning d-flex justify-content-between"
                            wire:poll.5000ms='checkProgress()'>Importing your file
                            Please wait....

                            <div class="d-flex align-content-center">
                                <span class="text-warning fw-bold me-2"> {{ $progress }}%</span>
                                <div class="spinner-border text-warning spinner-border-sm" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>

                        <div x-data class="my-2 progress progress-sm">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning"
                                role="progressbar" style="width: {{ $progress . '%' }}" aria-valuenow="25"
                                aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    @endif


                    {{ $slot }}

                </div>
            </div>
        </div>
    </div>
</div>
