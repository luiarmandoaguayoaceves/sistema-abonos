<?php

use App\Http\Controllers\AbonoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\PedidoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', function () {
    return view('welcome');
})->name('home');

Route::get('/facturas', function () {
    return view('facturas');
})->name('facturas');

Route::get('/pedidos', function () {
    $clientes = \App\Models\Cliente::all();
    return view('pedidos', compact('clientes'));
})->name('pedidos');

Route::get('clientes',[ClienteController::class, 'index'])->name('clientes.index');
Route::post('clientes', [ClienteController::class, 'store'])->name('clientes.store');
Route::delete('clientes/{id}', [ClienteController::class, 'destroy'])->name('clientes.destroy');

Route::get('/seguimiento', [PedidoController::class, 'index'])->name('seguimiento');

Route::post('/pedidos/guardar', [PedidoController::class, 'store'])->name('pedidos.store');

Route::get('/facturas', [FacturaController::class, 'index'])->name('facturas');
Route::post('/facturas', [FacturaController::class, 'store'])->name('facturas.store');
Route::delete('/facturas/{id}', [FacturaController::class, 'destroy'])->name('facturas.destroy');

Route::get('/abonos', [AbonoController::class, 'index'])->name('abonos.index');
Route::post('/abonos', [AbonoController::class, 'store'])->name('abonos.store');
Route::patch('/abonos/{id}/pagar', [AbonoController::class, 'marcarPagado'])->name('abonos.pagado');

Route::put('pedidos/update-fecha/{id}', [PedidoController::class, 'updateFecha'])->name('pedidos.updateFecha');