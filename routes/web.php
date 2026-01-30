<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvitePublicController;
use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\Client\InviteController as ClientInviteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Client\EventController as ClientEventController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/i/{hash}', [InvitePublicController::class, 'show'])->name('invite.show');
Route::post('/i/{hash}/rsvp', [InvitePublicController::class, 'rsvp'])
    ->middleware('throttle:30,1')
    ->name('invite.rsvp');

// ===== Admin (tú) =====
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/tenants', [TenantController::class, 'index'])->name('tenants.index');
    Route::get('/tenants/create', [TenantController::class, 'create'])->name('tenants.create');
    Route::post('/tenants', [TenantController::class, 'store'])->name('tenants.store');
});

// ===== Cliente =====
Route::middleware(['auth', 'client'])->prefix('panel')->name('panel.')->group(function () {
    Route::get('/invites', [ClientInviteController::class, 'index'])->name('invites.index');
    Route::get('/invites/create', [ClientInviteController::class, 'create'])->name('invites.create');
    Route::post('/invites', [ClientInviteController::class, 'store'])->name('invites.store');
    Route::get('/event', [ClientEventController::class, 'edit'])->name('event.edit');
    Route::post('/event', [ClientEventController::class, 'update'])->name('event.update');
    Route::post('/event/gallery', [ClientEventController::class, 'addPhotos'])->name('event.gallery.add');
    Route::delete('/event/gallery/{photo}', [ClientEventController::class, 'deletePhoto'])->name('event.gallery.delete');
});

Route::get('/dashboard', HomeController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
