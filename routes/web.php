<?php

use App\Http\Controllers\EventosController;
use App\Http\Controllers\MovimientoController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::location(route('platform.main'));
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/eventos', [EventosController::class, 'index'])->name('eventos');
    Route::get('/evento/{evento}', [EventosController::class, 'detalle'])->name('evento.detalle');

    Route::post('/evento/{evento}/unirse', [EventosController::class, 'unirse'])->name('eventos.unirse');
    Route::post('/evento/{evento}/nuevo-gasto', [EventosController::class, 'nuevoGasto'])->name('eventos.nuevo-gasto');
    Route::post('/evento/crear', [EventosController::class, 'crearEvento'])->name('eventos.crear');

    Route::post('/movimiento/{movimiento}/editar', [MovimientoController::class, 'editar'])->name('movimiento.editar');
    Route::delete('/movimiento/{movimiento}', [MovimientoController::class, 'eliminar'])->name('movimiento.eliminar');

});
