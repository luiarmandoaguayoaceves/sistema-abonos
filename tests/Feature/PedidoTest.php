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
        'fecha_entrega' => '2026-06-20',
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
        'fecha_entrega' => '2026-06-20',
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

it('permite eliminar logicamente un pedido', function () {
    $pedido = Pedido::create([
        'n_pedido' => 'PED-DEL-001',
        'cliente' => 'Cliente Demo',
        'detalles' => [],
        'subtotal' => 1000,
        'iva' => 0,
        'total' => 1000,
        'fecha_entrega' => now(),
        'pagado' => false,
        'eliminado' => false,
    ]);

    $response = $this->delete(route('pedidos.destroy', $pedido->id));

    $response->assertRedirect();

    $pedido->refresh();

    expect($pedido->eliminado)->toBeTrue();
});

it('no muestra pedidos eliminados en seguimiento', function () {
    Pedido::create([
        'n_pedido' => 'PED-DEL-001',
        'cliente' => 'Cliente Demo',
        'detalles' => [],
        'subtotal' => 1000,
        'iva' => 0,
        'total' => 1000,
        'fecha_entrega' => now(),
        'pagado' => false,
        'eliminado' => true,
    ]);

    $this->get(route('seguimiento'))
        ->assertOk()
        ->assertDontSee('PED-DEL-001');
});

it('filtra seguimiento por mes y muestra la suma del total', function () {
    $pedidoEneroUno = Pedido::create([
        'n_pedido' => 'PED-ENE-001',
        'cliente' => 'Cliente Enero',
        'detalles' => [],
        'subtotal' => 1000,
        'iva' => 0,
        'total' => 1000,
        'fecha_entrega' => '2026-01-10 10:00:00',
        'pagado' => false,
        'eliminado' => false,
    ]);

    $pedidoEneroUno->detallesPedido()->create([
        'modelo' => 'Bota Enero',
        'color' => 'Negro',
        'pares' => 10,
        'precio_unitario' => 100,
        'subtotal' => 1000,
    ]);

    $pedidoEneroDos = Pedido::create([
        'n_pedido' => 'PED-ENE-002',
        'cliente' => 'Cliente Enero Dos',
        'detalles' => [],
        'subtotal' => 500,
        'iva' => 0,
        'total' => 500,
        'fecha_entrega' => '2026-01-20 10:00:00',
        'pagado' => false,
        'eliminado' => false,
    ]);

    $pedidoEneroDos->detallesPedido()->create([
        'modelo' => 'Zapato Enero',
        'color' => 'Cafe',
        'pares' => 5,
        'precio_unitario' => 100,
        'subtotal' => 500,
    ]);

    Pedido::create([
        'n_pedido' => 'PED-FEB-001',
        'cliente' => 'Cliente Febrero',
        'detalles' => [],
        'subtotal' => 800,
        'iva' => 0,
        'total' => 800,
        'fecha_entrega' => '2026-02-10 10:00:00',
        'pagado' => false,
        'eliminado' => false,
    ]);

    $this->get(route('seguimiento', ['mes' => '2026-01']))
        ->assertOk()
        ->assertSee('Enero 2026')
        ->assertSee('PED-ENE-001')
        ->assertSee('PED-ENE-002')
        ->assertSee('15 pares')
        ->assertSee('$1,500.00')
        ->assertDontSee('PED-FEB-001');
});

it('filtra por created_at cuando el pedido no tiene fecha de entrega', function () {
    $pedido = Pedido::create([
        'n_pedido' => 'PED-SIN-FECHA-ENE',
        'cliente' => 'Cliente Sin Fecha',
        'detalles' => [
            [
                'modelo' => 'Zapato Legacy',
                'color' => 'Negro',
                'pares' => 3,
                'precio' => 100,
            ],
        ],
        'subtotal' => 300,
        'iva' => 0,
        'total' => 300,
        'fecha_entrega' => null,
        'pagado' => false,
        'eliminado' => false,
    ]);

    Pedido::withoutTimestamps(function () use ($pedido) {
        $pedido->forceFill([
            'created_at' => '2026-01-05 10:00:00',
            'updated_at' => '2026-01-05 10:00:00',
        ])->save();
    });

    $this->get(route('seguimiento', ['mes' => '2026-01']))
        ->assertOk()
        ->assertSee('PED-SIN-FECHA-ENE')
        ->assertSee('3 pares')
        ->assertSee('$300.00');
});

it('usa la fecha de entrega actualizada para cambiar el pedido de mes', function () {
    $pedido = Pedido::create([
        'n_pedido' => 'PED-CAMBIO-MES',
        'cliente' => 'Cliente Cambio Mes',
        'detalles' => [],
        'subtotal' => 1000,
        'iva' => 0,
        'total' => 1000,
        'fecha_entrega' => '2026-01-15 10:00:00',
        'pagado' => false,
        'eliminado' => false,
    ]);

    $this->put(route('pedidos.updateFecha', $pedido->id), [
        'fecha_entrega' => '2026-02-15 10:00:00',
    ])->assertRedirect();

    $this->get(route('seguimiento', ['mes' => '2026-01']))
        ->assertOk()
        ->assertDontSee('PED-CAMBIO-MES');

    $this->get(route('seguimiento', ['mes' => '2026-02']))
        ->assertOk()
        ->assertSee('PED-CAMBIO-MES');
});
