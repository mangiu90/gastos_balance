<?php

use App\Http\Controllers\EventosController;
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
    Route::post('/evento/{evento}/unirse', [EventosController::class, 'unirse'])->name('eventos.unirse');
    Route::post('/evento/{evento}/nuevo-gasto', [EventosController::class, 'nuevoGasto'])->name('eventos.nuevo-gasto');
});
