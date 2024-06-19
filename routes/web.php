<?php

use App\Http\Controllers\TestingController;
use App\Livewire\External\Dashboard as ExternalDashboard;
use App\Livewire\External\ViewIndicator;
use App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption\AddData as HRCAddData;
use App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption\Details;
use App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption\ViewData as HRCViewData;
use App\Livewire\Forms\RtcMarket\RtcProductionFarmers\Add as RTCMAddData;
use App\Livewire\Forms\RtcMarket\RtcProductionFarmers\View as RTCMViewData;
use App\Livewire\Internal\Cip\Dashboard;
use App\Livewire\Internal\Cip\Forms;
use App\Livewire\Internal\Cip\Indicators;
use App\Livewire\Internal\Cip\Reports;
use App\Livewire\Internal\Cip\Submissions;
use App\Livewire\Internal\Cip\SubPeriod;
use App\Livewire\Internal\Cip\ViewIndicators;
use App\Livewire\Internal\Cip\ViewSubmissions;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/test', function () {
    $currentDateTime = now(); // or \Carbon\Carbon::now() for Carbon instances
    echo $currentDateTime; // Output the current datetime in Harare timezone

});

Route::get('/export/{name}', [TestingController::class, 'index']);

Route::get('/profile', \App\Livewire\Profile\Details::class)->middleware(['auth'])->name('profile');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', \App\Livewire\Admin\Dashboard::class)->name('admin-dashboard');
    Route::get('/users', \App\Livewire\Admin\ManageUsers::class)->name('admin-users');
    Route::get('/organisations', \App\Livewire\Admin\ManageOrganisations::class)->name('admin-organisations');
});

Route::middleware(['auth', 'role:internal', 'role:cip'])->prefix('cip')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('cip-internal-dashboard');
    Route::get('/indicators', Indicators::class)->name('cip-internal-indicators');
    Route::get('/indicators/view/{id}', ViewIndicators::class)->name('cip-internal-indicator-view');
    Route::get('/forms', Forms::class)->name('cip-internal-forms');
    Route::get('/submissions', Submissions::class)->name('cip-internal-submissions');
    Route::get('/submissions/view/{batch_no}', ViewSubmissions::class)->name('cip-internal-submission-view');
    Route::get('/reports', Reports::class)->name('cip-internal-reports');
    Route::get('/submission-period', SubPeriod::class)->name('cip-internal-submission-period');
    Route::get('/submissions/{name}/{id}', Details::class);

    //forms
    Route::get('/forms/{project}/household-consumption-form/add', HRCAddData::class);
    Route::get('/forms/{project}/household-consumption-form/view', HRCViewData::class);
    Route::get('/forms/{project}/household-consumption-form/{batch}/view', HRCViewData::class);

    Route::get('/forms/{project}/rtc-production-and-marketing-form-farmers/add', RTCMAddData::class);
    Route::get('/forms/{project}/rtc-production-and-marketing-form-farmers/view', RTCMViewData::class);
    Route::get('/forms/{project}/rtc-production-and-marketing-form-farmers/{batch}/view', RTCMViewData::class);

    Route::get('/forms/{project}/rtc-production-and-marketing-form-processors/add', App\Livewire\Forms\RtcMarket\RtcProductionProcessors\Add::class);
    Route::get('/forms/{project}/rtc-production-and-marketing-form-processors/view', App\Livewire\Forms\RtcMarket\RtcProductionProcessors\View::class);
    Route::get('/forms/{project}/rtc-production-and-marketing-form-processors/{batch}/view', App\Livewire\Forms\RtcMarket\RtcProductionProcessors\View::class);

    Route::get('/forms/{project}/school-rtc-consumption-form/add', App\Livewire\Forms\RtcMarket\SchoolConsumption\Add::class);
    Route::get('/forms/{project}/school-rtc-consumption-form/view', App\Livewire\Forms\RtcMarket\SchoolConsumption\View::class);
    Route::get('/forms/{project}/school-rtc-consumption-form/{batch}/view', App\Livewire\Forms\RtcMarket\SchoolConsumption\View::class);

});

Route::middleware(['auth', 'role:external'])->prefix('external')->group(function () {
    Route::get('/dashboard', ExternalDashboard::class)->name('external-dashboard');
    Route::get('/indicators', \App\Livewire\External\Indicators::class)->name('external-indicators');
    Route::get('/indicators/view/{id}', ViewIndicator::class)->name('external-indicator-view');
    Route::get('/forms', \App\Livewire\External\Forms::class)->name('external-forms');
    Route::get('/submissions', \App\Livewire\External\Submissions::class);

    Route::get('/forms/household-rtc-consumption/add', HRCAddData::class);
    Route::get('/forms/household-rtc-consumption/view', HRCViewData::class);
    Route::get('/submissions/household-rtc-consumption/{id}', Details::class);

});

// Route::middleware(['auth', 'role:external', 'role:cip'])->prefix('internal')->prefix('cip')->group(function () {

// });

require __DIR__ . '/auth.php';
