<?php

use App\Models\Pedido;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('permite crear un pedido con detalles normalizados', function () {
    $items = [
        [
            'modelo' => 'Bota 100',
            'color' => 'Negro',
            'pares' => 10,
            'precio' => 250,
            'subtotalItem' => 2500,
        ],
        [
            'modelo' => 'Zapato 200',
            'color' => 'Café',
            'pares' => 5,
            'precio' => 300,
            'subtotalItem' => 1500,
        ],
    ];

    $response = $this->post(route('pedidos.store'), [
        'pedido' => 'PED-001',
        'cliente' => 'Cliente Demo',
        'datos_pedido' => json_encode($items),
        'iva_aplicado' => true,
    ]);

    $response->assertRedirect(route('seguimiento'));

    $this->assertDatabaseHas('pedidos', [
        'n_pedido' => 'PED-001',
        'cliente' => 'Cliente Demo',
        'subtotal' => 4000,
        'iva' => 640,
        'total' => 4640,
    ]);

    $pedido = Pedido::where('n_pedido', 'PED-001')->firstOrFail();

    expect($pedido->detallesPedido)->toHaveCount(2);

    $this->assertDatabaseHas('pedido_detalles', [
        'pedido_id' => $pedido->id,
        'modelo' => 'Bota 100',
        'color' => 'Negro',
        'pares' => 10,
        'precio_unitario' => 250,
        'subtotal' => 2500,
    ]);

    $this->assertDatabaseHas('pedido_detalles', [
        'pedido_id' => $pedido->id,
        'modelo' => 'Zapato 200',
        'color' => 'Café',
        'pares' => 5,
        'precio_unitario' => 300,
        'subtotal' => 1500,
    ]);
});

it('no permite crear un pedido sin productos', function () {
    $response = $this->post(route('pedidos.store'), [
        'pedido' => 'PED-002',
        'cliente' => 'Cliente Demo',
        'datos_pedido' => json_encode([]),
        'iva_aplicado' => false,
    ]);

    $response->assertSessionHasErrors(['datos_pedido']);

    $this->assertDatabaseMissing('pedidos', [
        'n_pedido' => 'PED-002',
    ]);
});

it('permite actualizar la fecha de entrega sin modificar created_at', function () {
    $pedido = Pedido::create([
        'n_pedido' => 'PED-003',
        'cliente' => 'Cliente Demo',
        'detalles' => [],
        'subtotal' => 1000,
        'iva' => 0,
        'total' => 1000,
        'fecha_entrega' => '2026-06-15 10:00:00',
    ]);

    $createdAtOriginal = $pedido->created_at;

    $response = $this->put(route('pedidos.updateFecha', $pedido->id), [
        'fecha_entrega' => '2026-06-20 15:30:00',
    ]);

    $response->assertRedirect();

    $pedido->refresh();

    expect($pedido->fecha_entrega->format('Y-m-d H:i:s'))->toBe('2026-06-20 15:30:00');
    expect($pedido->created_at->format('Y-m-d H:i:s'))->toBe($createdAtOriginal->format('Y-m-d H:i:s'));
});