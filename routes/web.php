<?php

use App\Http\Controllers\AbonoController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.store');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect()->route('home');
    });

    Route::get('/home', [DashboardController::class, 'index'])->name('home');

    Route::get('/pedidos', function () {
        $clientes = \App\Models\Cliente::all();

        return view('pedidos', compact('clientes'));
    })->name('pedidos');

    Route::get('clientes', [ClienteController::class, 'index'])->name('clientes.index');
    Route::post('clientes', [ClienteController::class, 'store'])->name('clientes.store');
    Route::delete('clientes/{id}', [ClienteController::class, 'destroy'])->name('clientes.destroy');

    Route::get('/seguimiento', [PedidoController::class, 'index'])->name('seguimiento');
    Route::post('/pedidos/guardar', [PedidoController::class, 'store'])->name('pedidos.store');
    Route::put('pedidos/update-fecha/{id}', [PedidoController::class, 'updateFecha'])->name('pedidos.updateFecha');

    Route::get('/facturas', [FacturaController::class, 'index'])->name('facturas');
    Route::post('/facturas', [FacturaController::class, 'store'])->name('facturas.store');
    Route::delete('/facturas/{id}', [FacturaController::class, 'destroy'])->name('facturas.destroy');

    Route::get('/abonos', [AbonoController::class, 'index'])->name('abonos.index');
    Route::post('/abonos', [AbonoController::class, 'store'])->name('abonos.store');
    Route::patch('/abonos/{id}/pagar', [AbonoController::class, 'marcarPagado'])->name('abonos.pagado');
});