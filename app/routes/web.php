<?php

use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ReservationController::class, 'create'])->name('reservation.create');
Route::post('/rezerwacja', [ReservationController::class, 'store'])->name('reservation.store');
Route::get('/rezerwacja/sukces', [ReservationController::class, 'success'])->name('reservation.success');

// API – dla formularza klienta (AJAX)
Route::get('/api/menus', [ReservationController::class, 'menus'])->name('api.menus');
Route::get('/api/blocked-dates', [ReservationController::class, 'blockedDates'])->name('api.blocked-dates');
