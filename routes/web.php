<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MyTrailsController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\TrailController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::delete('/profil', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'premium'])->group(function () {
    Route::get('/routen', [TrailController::class, 'index'])->name('trails.index');
    Route::get('/routen/erstellen', [TrailController::class, 'create'])->name('trails.create');
    Route::post('/routen', [TrailController::class, 'store'])->name('trails.store');
    Route::get('/routen/{trail}', [TrailController::class, 'show'])->name('trails.show');
    Route::get('/routen/{trail}/bearbeiten', [TrailController::class, 'edit'])->name('trails.edit');
    Route::get('/routen/{trail}/caches', [TrailController::class, 'caches'])->name('trails.caches');
    Route::put('/routen/{trail}', [TrailController::class, 'update'])->name('trails.update');
    Route::delete('/routen/{trail}', [TrailController::class, 'destroy'])->name('trails.destroy');

    Route::get('/meine-routen', MyTrailsController::class)->name('trails.mine');
    Route::view('/favoriten', 'favorites.index')->name('favorites.index');
});
