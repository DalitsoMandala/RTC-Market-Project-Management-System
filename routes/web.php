<?php

use App\Http\Controllers\TestingController;
use App\Livewire\External\Cip\Dashboard as ExternalDashboard;
use App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption\AddData as HRCAddData;
use App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption\ViewData as HRCViewData;
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

Route::get('/a1', function () {

});

Route::get('/export', [TestingController::class, 'index']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('admin-dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

Route::middleware(['auth', 'role:admin'])->group(function () {

});

// Route::middleware(['auth', 'role:internal', 'role:desira'])->group(function () {

// });

// Route::middleware(['auth', 'role:external', 'role:desira'])->group(function () {

// });

Route::middleware(['auth', 'role:internal', 'role:cip'])->prefix('cip')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('cip-internal-dashboard');
    Route::get('/indicators', Indicators::class)->name('cip-internal-indicators');
    Route::get('/indicators/view/{id}', ViewIndicators::class)->name('cip-internal-indicator-view');
    Route::get('/forms', Forms::class)->name('cip-internal-forms');
    Route::get('/submissions', Submissions::class)->name('cip-internal-submissions');
    Route::get('/submissions/view/{batch_no}', ViewSubmissions::class)->name('cip-internal-submission-view');
    Route::get('/reports', Reports::class)->name('cip-internal-reports');
    Route::get('/submission-period', SubPeriod::class)->name('cip-internal-submission-period');

    //forms
    Route::get('/forms/household-rtc-consumption/add', HRCAddData::class);
    Route::get('/forms/household-rtc-consumption/view', HRCViewData::class);

});

Route::middleware(['auth', 'role:external'])->prefix('external')->group(function () {
    Route::get('/dashboard', ExternalDashboard::class)->name('external-dashboard');

});

// Route::middleware(['auth', 'role:external', 'role:cip'])->prefix('internal')->prefix('cip')->group(function () {

// });

require __DIR__ . '/auth.php';