<?php

use App\Http\Controllers\TestingController;
use App\Livewire\External\Dashboard as ExternalDashboard;
use App\Livewire\External\ViewIndicator;
use App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption\AddData as HRCAddData;
use App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption\ViewData as HRCViewData;
use App\Livewire\Forms\RtcMarket\RtcProductionFarmers\Add as RTCMAddData;
use App\Livewire\Forms\RtcMarket\RtcProductionFarmers\View as RTCMViewData;
use App\Livewire\Internal\Cip\Assignments;
use App\Livewire\Internal\Cip\Dashboard;
use App\Livewire\Internal\Cip\Forms;
use App\Livewire\Internal\Cip\Indicators;
use App\Livewire\Internal\Cip\Reports;
use App\Livewire\Internal\Cip\Submissions;
use App\Livewire\Internal\Cip\SubPeriod;
use App\Livewire\Internal\Cip\ViewIndicators;
use App\Livewire\Internal\Cip\ViewSubmissions;
use Illuminate\Support\Facades\Route;

// Redirect root to login
Route::get('/', fn() => redirect()->route('login'));

// Test route (empty)
Route::get('/test', function () {
    $filePath = public_path('storage\imports\hh_1720372440.xlsx');

    return Response::download($filePath, 'hh_1720372440.xlsx');
});

// TestingController route
Route::get('/export/{name}', [TestingController::class, 'index']);

// Profile route
Route::get('/profile', \App\Livewire\Profile\Details::class)
    ->middleware(['auth'])
    ->name('profile');

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', \App\Livewire\Admin\Dashboard::class)->name('admin-dashboard');
    Route::get('/users', \App\Livewire\Admin\ManageUsers::class)->name('admin-users');
    Route::get('/organisations', \App\Livewire\Admin\ManageOrganisations::class)->name('admin-organisations');
});

// CIP Internal routes
Route::middleware(['auth', 'role:internal', 'role:cip'])->prefix('cip')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('cip-internal-dashboard');
    Route::get('/indicators', Indicators::class)->name('cip-internal-indicators');
    Route::get('/indicators/view/{id}', ViewIndicators::class)->name('cip-internal-indicator-view');
    Route::get('/forms', Forms::class)->name('cip-internal-forms');
    Route::get('/submissions', Submissions::class)->name('cip-internal-submissions');
    Route::get('/submissions/view/{batch_no}', ViewSubmissions::class)->name('cip-internal-submission-view');
    Route::get('/reports', Reports::class)->name('cip-internal-reports');
    Route::get('/submission-period', SubPeriod::class)->name('cip-internal-submission-period');
    Route::get('/indicators-and-leads', Assignments::class)->name('cip-leads');
    // Form routes
    $formPrefix = '/forms/{project}';

    Route::get($formPrefix . '/household-consumption-form/add/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', HRCAddData::class);
    Route::get($formPrefix . '/household-consumption-form/view', HRCViewData::class);
    Route::get($formPrefix . '/household-consumption-form/{batch}/view', HRCViewData::class);
    Route::get($formPrefix . '/household-consumption-form/upload/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption\Upload::class);
    Route::get($formPrefix . '/aggregate/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', App\Livewire\Forms\RtcMarket\Reports\Add::class);

    Route::get($formPrefix . '/rtc-production-and-marketing-form-farmers/add/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', RTCMAddData::class);
    Route::get($formPrefix . '/rtc-production-and-marketing-form-farmers/upload/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', App\Livewire\Forms\RtcMarket\RtcProductionFarmers\Upload::class);
    Route::get($formPrefix . '/rtc-production-and-marketing-form-farmers/{batch}/view', RTCMViewData::class);

    Route::get($formPrefix . '/rtc-production-and-marketing-form-processors/add/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', App\Livewire\Forms\RtcMarket\RtcProductionProcessors\Add::class);
    Route::get($formPrefix . '/rtc-production-and-marketing-form-processors/upload/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', App\Livewire\Forms\RtcMarket\RtcProductionProcessors\Upload::class);
    Route::get($formPrefix . '/rtc-production-and-marketing-form-processors/{batch}/view', App\Livewire\Forms\RtcMarket\RtcProductionProcessors\View::class);

    Route::get($formPrefix . '/school-rtc-consumption-form/add/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', App\Livewire\Forms\RtcMarket\SchoolConsumption\Add::class);
    Route::get($formPrefix . '/school-rtc-consumption-form/view', App\Livewire\Forms\RtcMarket\SchoolConsumption\View::class);
    Route::get($formPrefix . '/school-rtc-consumption-form/{batch}/view', App\Livewire\Forms\RtcMarket\SchoolConsumption\View::class);
});

// External routes
Route::middleware(['auth', 'role:external'])->prefix('external')->group(function () {
    Route::get('/dashboard', ExternalDashboard::class)->name('external-dashboard');
    Route::get('/indicators', \App\Livewire\External\Indicators::class)->name('external-indicators');
    Route::get('/indicators/view/{id}', ViewIndicator::class)->name('external-indicator-view');
    Route::get('/forms', \App\Livewire\External\Forms::class)->name('external-forms');
    Route::get('/submissions', \App\Livewire\External\Submissions::class)->name('external-submissions');

    // Form routes
    $formPrefix = '/forms/{project}';
    Route::get($formPrefix . '/aggregate/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', App\Livewire\Forms\RtcMarket\Reports\Add::class);

    Route::get($formPrefix . '/household-consumption-form/add/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', HRCAddData::class);
    Route::get($formPrefix . '/household-consumption-form/view', HRCViewData::class);
    Route::get($formPrefix . '/household-consumption-form/{batch}/view', HRCViewData::class);
    Route::get($formPrefix . '/household-consumption-form/upload/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption\Upload::class);
    Route::get($formPrefix . '/aggregate/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', App\Livewire\Forms\RtcMarket\Reports\Add::class);

    Route::get($formPrefix . '/rtc-production-and-marketing-form-farmers/add/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', RTCMAddData::class);
    Route::get($formPrefix . '/rtc-production-and-marketing-form-farmers/upload/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', App\Livewire\Forms\RtcMarket\RtcProductionFarmers\Upload::class);
    Route::get($formPrefix . '/rtc-production-and-marketing-form-farmers/{batch}/view', RTCMViewData::class);

    Route::get($formPrefix . '/rtc-production-and-marketing-form-processors/add/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', App\Livewire\Forms\RtcMarket\RtcProductionProcessors\Add::class);
    Route::get($formPrefix . '/rtc-production-and-marketing-form-processors/upload/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', App\Livewire\Forms\RtcMarket\RtcProductionProcessors\Upload::class);
    Route::get($formPrefix . '/rtc-production-and-marketing-form-processors/{batch}/view', App\Livewire\Forms\RtcMarket\RtcProductionProcessors\View::class);

    Route::get($formPrefix . '/school-rtc-consumption-form/add/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', App\Livewire\Forms\RtcMarket\SchoolConsumption\Add::class);
    Route::get($formPrefix . '/school-rtc-consumption-form/view', App\Livewire\Forms\RtcMarket\SchoolConsumption\View::class);
    Route::get($formPrefix . '/school-rtc-consumption-form/{batch}/view', App\Livewire\Forms\RtcMarket\SchoolConsumption\View::class);
});

// Authentication routes
require __DIR__ . '/auth.php';
