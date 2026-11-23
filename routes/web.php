x<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TwoFactorCodeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\ReservacionController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'two.factor'])->name('dashboard');

Route::middleware(['auth', 'two.factor', 'role:admin,empleado'])->group(function () {
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::get('users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::get('users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{id}', [UserController::class, 'update'])->name('users.update');
});

Route::middleware(['auth', 'two.factor', 'role:admin'])->group(function () {
    Route::delete('users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::patch('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::delete('users/{id}/force-delete', [UserController::class, 'forceDelete'])->name('users.forceDelete');
});

Route::middleware(['auth', 'role:profesor'])->group(function () {
    Route::resource('alumnos', AlumnoController::class)->except(['index', 'show']);
});

Route::middleware(['auth', 'two.factor', 'role:admin,empleado'])->group(function () {
    Route::get('productos', [ProductoController::class, 'index'])->name('productos.index');
});

Route::middleware(['auth', 'two.factor', 'role:admin'])->group(function () {
    Route::get('productos/create', [ProductoController::class, 'create'])->name('productos.create');
    Route::post('productos', [ProductoController::class, 'store'])->name('productos.store');
    Route::get('productos/{producto}/edit', [ProductoController::class, 'edit'])->name('productos.edit');
    Route::put('productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
    Route::delete('productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');
});


// Rutas exclusivas del Administrador para el CRUD de equipos de cómputo
Route::middleware(['auth', 'two.factor', 'role:admin'])->group(function () {
    Route::resource('equipos', EquipoController::class)->except(['show']);
});

// Rutas compartidas entre Administrador y Estudiante para consultar, crear y cancelar reservaciones
Route::middleware(['auth', 'two.factor', 'role:admin,estudiante'])->group(function () {
    Route::get('/reservaciones', [ReservacionController::class, 'index'])->name('reservaciones.index');
    Route::post('/reservaciones', [ReservacionController::class, 'store'])->name('reservaciones.store');
    Route::patch('/reservaciones/{reservacion}/cancelar', [ReservacionController::class, 'cancelar'])->name('reservaciones.cancelar');
});

Route::get('/alumnos', [AlumnoController::class, 'index'])
    ->middleware(['auth', 'role:profesor,alumno'])
    ->name('alumnos.index');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('/settings', function () {
    // ...
})->middleware(['password.confirm']);

Route::post('/settings', function () {
    // ...
})->middleware(['password.confirm']);
require __DIR__.'/auth.php';

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::middleware('auth')->group(function () {
    Route::get('verify', [TwoFactorCodeController::class, 'verify'])->middleware('signed')->name('verify');
    Route::post('verify', [TwoFactorCodeController::class, 'verifyPost'])->name('verify.post');
});