<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\BookmarksController;
use App\Http\Controllers\ExampleTrailController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MyTrailsController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\PublicProfileController;
use App\Http\Controllers\TrailController;
use App\Http\Controllers\UserLocationController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/beispielroute', ExampleTrailController::class)->name('trails.example');

Route::middleware('auth')->group(function () {
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::delete('/profil', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/profil/infobox', [ProfileController::class, 'updateBio'])->name('profile.bio.update');
});

Route::middleware(['auth', 'verified', 'can:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::redirect('/', '/admin/meldungen')->name('index');
    Route::get('/meldungen', [Admin\ReportController::class, 'index'])->name('reports.index');
    Route::patch('/meldungen/{report}', [Admin\ReportController::class, 'update'])->name('reports.update');
    Route::get('/nutzer', [Admin\UserController::class, 'index'])->name('users.index');
    Route::patch('/nutzer/{user}', [Admin\UserController::class, 'update'])->name('users.update');
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
    Route::get('/merkliste', BookmarksController::class)->name('bookmarks.index');
    Route::redirect('/favoriten', '/merkliste', 301);
    Route::get('/nutzer/{user}', PublicProfileController::class)->where('user', '[0-9]+(-[a-z0-9-]*)?')->name('profiles.show');
});
