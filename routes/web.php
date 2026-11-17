<?php

use App\Http\Controllers\Profile\ProfileController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::delete('/profil', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'premium'])->group(function () {
    Route::view('/routen', 'trails.index')->name('trails.index');
    Route::view('/routen/erstellen', 'trails.create')->name('trails.create');
    Route::view('/meine-routen', 'trails.mine')->name('trails.mine');
    Route::view('/favoriten', 'favorites.index')->name('favorites.index');
});
