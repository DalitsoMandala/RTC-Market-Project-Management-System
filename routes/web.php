<?php

use App\Http\Controllers\ProfileController;
use App\Livewire\Internal\Cip\Dashboard;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

Route::middleware(['auth', 'role:admin'])->group(function () {

});

Route::middleware(['auth', 'role:internal', 'role:desira'])->group(function () {

});

Route::middleware(['auth', 'role:external', 'role:desira'])->group(function () {

});

Route::middleware(['auth', 'role:internal', 'role:cip'])->prefix('internal')->prefix('cip')->group(function () {
    Route::get('/dashboard', [Dashboard::class])->name('cip-internal-dashboard');


});

Route::middleware(['auth', 'role:external', 'role:cip'])->prefix('internal')->prefix('cip')->group(function () {

});



require __DIR__ . '/auth.php';