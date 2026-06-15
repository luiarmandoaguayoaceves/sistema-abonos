<?php

use App\Models\Cliente;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('permite crear un cliente con datos válidos', function () {
    $response = $this->post(route('clientes.store'), [
        'cve_cliente' => 1001,
        'nombre' => 'Cliente Demo',
        'marca' => 'Marca Demo',
        'notas' => 'Cliente de prueba',
        'telefono' => '3312345678',
        'correo' => 'cliente@example.com',
        'frecuencia_pago' => 'Semanal',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('clientes', [
        'cve_cliente' => 1001,
        'nombre' => 'Cliente Demo',
        'correo' => 'cliente@example.com',
        'frecuencia_pago' => 'Semanal',
    ]);
});

it('requiere nombre y frecuencia de pago para crear un cliente', function () {
    $response = $this->post(route('clientes.store'), []);

    $response->assertSessionHasErrors([
        'nombre',
        'frecuencia_pago',
    ]);
});

it('permite eliminar un cliente existente', function () {
    $cliente = Cliente::create([
        'cve_cliente' => 1002,
        'nombre' => 'Cliente a eliminar',
        'frecuencia_pago' => 'Mensual',
    ]);

    $response = $this->delete(route('clientes.destroy', $cliente->id));

    $response->assertRedirect();

    $this->assertDatabaseMissing('clientes', [
        'id' => $cliente->id,
    ]);
});