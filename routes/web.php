<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\AvailabilityController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect(route('groups.index'));
});

Route::get('/dashboard', function () {
    return redirect()->route('groups.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Group routes
    Route::resource('groups', GroupController::class);
    Route::post('/groups/join', [GroupController::class, 'join'])->name('groups.join');
    Route::post('/groups/{group}/leave', [GroupController::class, 'leave'])->name('groups.leave');
    Route::delete('/groups/{group}/members/{member}', [GroupController::class, 'removeMember'])->name('groups.remove-member');
    
    // Availability routes
    Route::get('/groups/{group}/availability', [AvailabilityController::class, 'index'])->name('availability.index');
    Route::get('/groups/{group}/availability/create', [AvailabilityController::class, 'create'])->name('availability.create');
    Route::post('/groups/{group}/availability', [AvailabilityController::class, 'store'])->name('availability.store');
    Route::get('/groups/{group}/availability/{availability}', [AvailabilityController::class, 'show'])->name('availability.show');
    Route::get('/groups/{group}/availability/{availability}/edit', [AvailabilityController::class, 'edit'])->name('availability.edit');
    Route::put('/groups/{group}/availability/{availability}', [AvailabilityController::class, 'update'])->name('availability.update');
    Route::delete('/groups/{group}/availability/{availability}', [AvailabilityController::class, 'destroy'])->name('availability.destroy');
    Route::get('/groups/{group}/calendar', [AvailabilityController::class, 'calendar'])->name('availability.calendar');
    Route::delete('/groups/{group}/availability/clear-date', [AvailabilityController::class, 'clearDate'])->name('availability.clear-date');
});

require __DIR__.'/auth.php';
