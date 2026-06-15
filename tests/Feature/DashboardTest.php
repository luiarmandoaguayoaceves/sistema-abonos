<?php

use App\Models\Abono;
use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('muestra clientes con su saldo pendiente en el dashboard', function () {

    $cliente = Cliente::create([
        'nombre' => 'Cliente Dashboard',
        'cve_cliente' => 100,
        'marca' => 'Marca Demo',
        'frecuencia_pago' => 'Semanal',
    ]);

    $pedido = Pedido::create([
        'n_pedido' => 'PED-DASH-001',
        'cliente' => $cliente->nombre,
        'detalles' => [],
        'subtotal' => 1000,
        'iva' => 0,
        'total' => 1000,
        'fecha_entrega' => now(),
        'pagado' => false,
    ]);

    Abono::create([
        'pedido_id' => $pedido->id,
        'monto' => 400,
        'metodo_pago' => 'Efectivo',
        'fecha_pago' => now()->format('Y-m-d'),
    ]);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Cliente Dashboard');
    $response->assertSee('Marca Demo');
    $response->assertSee('$600.00');
});