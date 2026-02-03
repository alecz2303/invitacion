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
Route::get('/invite/{hash}/ics', [InvitePublicController::class, 'ics'])->name('invite.ics');

// ===== Admin (tú) =====
Route::middleware(['tenant','auth', 'admin', 'admin.host'])
    ->prefix('admin')->name('admin.')->group(function () {
        Route::get('/tenants', [TenantController::class, 'index'])->name('tenants.index');
        Route::get('/tenants/create', [TenantController::class, 'create'])->name('tenants.create');
        Route::post('/tenants', [TenantController::class, 'store'])->name('tenants.store');

        Route::get('/tenants/{tenant}/edit', [TenantController::class, 'edit'])->name('tenants.edit');
        Route::put('/tenants/{tenant}', [TenantController::class, 'update'])->name('tenants.update');
        Route::post('/tenants/{tenant}/toggle', [TenantController::class, 'toggle'])->name('tenants.toggle');
});

// ===== HOME del tenant: demo =====
// Esto aplica dentro del host del tenant, porque tu middleware tenant lo resuelve por subdominio.
// Si estás usando Route::domain, pon esto dentro del grupo de dominio del tenant.
// Si NO estás usando domain(), lo de abajo funciona igual y el middleware tenant decidirá si hay tenant.

Route::middleware(['tenant'])->group(function () {
    Route::get('/', [InvitePublicController::class, 'demo'])->name('tenant.home');
});

// ===== Cliente =====
Route::middleware(['tenant','auth','client','tenant.match'])
    ->prefix('panel')->name('panel.')->group(function () {

    Route::get('/invites', [ClientInviteController::class, 'index'])->name('invites.index');

    Route::get('/invites/create', [ClientInviteController::class, 'create'])->name('invites.create');
    Route::post('/invites', [ClientInviteController::class, 'store'])->name('invites.store');

    // ✅ NUEVO: editar / actualizar / borrar
    Route::get('/invites/{invite}/edit', [ClientInviteController::class, 'edit'])->name('invites.edit');
    Route::put('/invites/{invite}', [ClientInviteController::class, 'update'])->name('invites.update');
    Route::delete('/invites/{invite}', [ClientInviteController::class, 'destroy'])->name('invites.destroy');

    Route::get('/event', [\App\Http\Controllers\Client\EventController::class, 'edit'])->name('event.edit');
    Route::post('/event', [\App\Http\Controllers\Client\EventController::class, 'update'])->name('event.update');

    Route::post('/event/gallery', [\App\Http\Controllers\Client\EventController::class, 'addPhotos'])->name('event.gallery.add');
    Route::delete('/event/gallery/{photo}', [\App\Http\Controllers\Client\EventController::class, 'deletePhoto'])->name('event.gallery.delete');
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
