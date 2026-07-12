<?php

use App\Models\Abono;
use App\Models\Pedido;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('permite registrar un abono a un pedido pendiente', function () {
    $pedido = Pedido::create([
        'n_pedido' => 'PED-ABO-001',
        'cliente' => 'Cliente Abono',
        'subtotal' => 1000,
        'iva' => 160,
        'total' => 1160,
        'fecha_entrega' => now(),
        'pagado' => false,
    ]);

    $response = $this->post(route('abonos.store'), [
        'pedido_id' => $pedido->id,
        'monto' => 500,
        'metodo_pago' => 'Efectivo',
        'fecha_pago' => now()->format('Y-m-d'),
    ]);

    $response->assertRedirect(route('abonos.index'));

    $this->assertDatabaseHas('abonos', [
        'pedido_id' => $pedido->id,
        'monto' => '500.00',
        'metodo_pago' => 'Efectivo',
    ]);

    $pedido->refresh();

    expect($pedido->pagado)->toBeFalse();
    expect($pedido->saldoPendiente())->toBe(660.00);
});

it('no permite registrar un abono mayor al saldo pendiente', function () {
    $pedido = Pedido::create([
        'n_pedido' => 'PED-ABO-002',
        'cliente' => 'Cliente Abono',
        'subtotal' => 1000,
        'iva' => 0,
        'total' => 1000,
        'fecha_entrega' => now(),
        'pagado' => false,
    ]);

    $response = $this->post(route('abonos.store'), [
        'pedido_id' => $pedido->id,
        'monto' => 1500,
        'metodo_pago' => 'Transferencia',
        'fecha_pago' => now()->format('Y-m-d'),
    ]);

    $response->assertSessionHasErrors(['monto']);

    $this->assertDatabaseMissing('abonos', [
        'pedido_id' => $pedido->id,
        'monto' => '1500.00',
    ]);

    $pedido->refresh();

    expect($pedido->pagado)->toBeFalse();
});

it('marca automaticamente el pedido como pagado cuando el abono liquida el saldo', function () {
    $pedido = Pedido::create([
        'n_pedido' => 'PED-ABO-003',
        'cliente' => 'Cliente Abono',
        'subtotal' => 1000,
        'iva' => 0,
        'total' => 1000,
        'fecha_entrega' => now(),
        'pagado' => false,
    ]);

    $response = $this->post(route('abonos.store'), [
        'pedido_id' => $pedido->id,
        'monto' => 1000,
        'metodo_pago' => 'Transferencia',
        'fecha_pago' => now()->format('Y-m-d'),
    ]);

    $response->assertRedirect(route('abonos.index'));

    $pedido->refresh();

    expect($pedido->pagado)->toBeTrue();
    expect($pedido->saldoPendiente())->toBe(0.00);
});

it('no permite registrar abonos en pedidos ya pagados', function () {
    $pedido = Pedido::create([
        'n_pedido' => 'PED-ABO-004',
        'cliente' => 'Cliente Abono',
        'subtotal' => 1000,
        'iva' => 0,
        'total' => 1000,
        'fecha_entrega' => now(),
        'pagado' => true,
    ]);

    $response = $this->post(route('abonos.store'), [
        'pedido_id' => $pedido->id,
        'monto' => 100,
        'metodo_pago' => 'Efectivo',
        'fecha_pago' => now()->format('Y-m-d'),
    ]);

    $response->assertSessionHasErrors(['pedido_id']);

    $this->assertDatabaseMissing('abonos', [
        'pedido_id' => $pedido->id,
        'monto' => '100.00',
    ]);
});

it('permite marcar como pagado un pedido con saldo cubierto', function () {
    $pedido = Pedido::create([
        'n_pedido' => 'PED-ABO-005',
        'cliente' => 'Cliente Abono',
        'subtotal' => 1000,
        'iva' => 0,
        'total' => 1000,
        'fecha_entrega' => now(),
        'pagado' => false,
    ]);

    Abono::create([
        'pedido_id' => $pedido->id,
        'monto' => 1000,
        'metodo_pago' => 'Efectivo',
        'fecha_pago' => now()->format('Y-m-d'),
    ]);

    $response = $this->patch(route('abonos.pagado', $pedido->id));

    $response->assertRedirect(route('abonos.index'));

    $pedido->refresh();

    expect($pedido->pagado)->toBeTrue();
});
