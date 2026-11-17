<?php

use App\Http\Controllers\FavoritesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MyTrailsController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\PublicProfileController;
use App\Http\Controllers\TrailController;
use App\Http\Controllers\UserLocationController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::delete('/profil', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'premium'])->group(function () {
    Route::get('/routen', [TrailController::class, 'index'])->name('trails.index');
    Route::post('/routen/standort', [UserLocationController::class, 'store'])->name('location.store');
    Route::delete('/routen/standort', [UserLocationController::class, 'destroy'])->name('location.destroy');
    Route::get('/routen/erstellen', [TrailController::class, 'create'])->name('trails.create');
    Route::post('/routen', [TrailController::class, 'store'])->name('trails.store');
    Route::get('/routen/{trail}', [TrailController::class, 'show'])->name('trails.show');
    Route::get('/routen/{trail}/bearbeiten', [TrailController::class, 'edit'])->name('trails.edit');
    Route::get('/routen/{trail}/caches', [TrailController::class, 'caches'])->name('trails.caches');
    Route::get('/routen/{trail}/vorschau', [TrailController::class, 'preview'])->name('trails.preview');
    Route::post('/routen/{trail}/veroeffentlichen', [TrailController::class, 'publish'])->name('trails.publish');
    Route::put('/routen/{trail}', [TrailController::class, 'update'])->name('trails.update');
    Route::delete('/routen/{trail}', [TrailController::class, 'destroy'])->name('trails.destroy');

    Route::get('/meine-routen', MyTrailsController::class)->name('trails.mine');
    Route::get('/favoriten', FavoritesController::class)->name('favorites.index');
    Route::get('/nutzer/{user}', PublicProfileController::class)->where('user', '[0-9]+(-[a-z0-9-]*)?')->name('profiles.show');
});
