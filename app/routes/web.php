<?php

use App\Http\Controllers\IcsController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ReservationController::class, 'create'])->name('reservation.create');
Route::post('/rezerwacja', [ReservationController::class, 'store'])->name('reservation.store');
Route::get('/rezerwacja/sukces', [ReservationController::class, 'success'])->name('reservation.success');

// API – dla formularza klienta (AJAX)
Route::get('/api/menus', [ReservationController::class, 'menus'])->name('api.menus');
Route::get('/api/blocked-dates', [ReservationController::class, 'blockedDates'])->name('api.blocked-dates');
Route::get('/api/menu-products', [ReservationController::class, 'menuProducts'])->name('api.menu-products');

// API – kalendarz admina (chroniony middleware auth)
Route::get('/api/calendar-events', [ReservationController::class, 'calendarEvents'])
    ->middleware(['auth'])
    ->name('api.calendar-events');

// ICS eksport – klient (signed URL, bez logowania)
Route::get('/ics/{reservation}', [IcsController::class, 'single'])->name('ics.single');

// ICS eksport – admin
Route::middleware(['auth'])->prefix('admin-ics')->group(function () {
    Route::get('/reservation/{reservation}', [IcsController::class, 'adminSingle'])->name('ics.admin.single');
    Route::get('/all', [IcsController::class, 'adminAll'])->name('ics.admin.all');
});
