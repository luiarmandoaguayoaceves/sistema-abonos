<?php

use App\Models\Pedido;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('permite vincular una factura PDF a un pedido', function () {
    Storage::fake('public');

    $pedido = Pedido::create([
        'n_pedido' => 'PED-FAC-001',
        'cliente' => 'Calzado vel',
        'detalles' => [],
        'subtotal' => 1000,
        'iva' => 160,
        'total' => 1160,
        'fecha_entrega' => now(),
    ]);

    $pdf = UploadedFile::fake()->create('factura.pdf', 100, 'application/pdf');

    $response = $this->post(route('facturas.store'), [
        'pedido_id' => $pedido->id,
        'folio_factura' => 'A-100',
        'archivo_pdf' => $pdf,
    ]);

    $response->assertRedirect(route('facturas'));

    $pedido->refresh();

    expect($pedido->folio_factura)->toBe('A-100');
    expect($pedido->pdf_factura)->not->toBeNull();

    Storage::disk('public')->assertExists('facturas/' . $pedido->pdf_factura);
});

it('no permite vincular factura si el pedido ya tiene factura', function () {
    Storage::fake('public');

    $pedido = Pedido::create([
        'n_pedido' => 'PED-FAC-002',
        'cliente' => 'Calzado vel',
        'detalles' => [],
        'subtotal' => 1000,
        'iva' => 160,
        'total' => 1160,
        'fecha_entrega' => now(),
        'folio_factura' => 'A-100',
        'pdf_factura' => 'factura_existente.pdf',
    ]);

    $pdf = UploadedFile::fake()->create('factura_nueva.pdf', 100, 'application/pdf');

    $response = $this->post(route('facturas.store'), [
        'pedido_id' => $pedido->id,
        'folio_factura' => 'A-200',
        'archivo_pdf' => $pdf,
    ]);

    $response->assertSessionHasErrors(['pedido_id']);

    $pedido->refresh();

    expect($pedido->folio_factura)->toBe('A-100');
    expect($pedido->pdf_factura)->toBe('factura_existente.pdf');
});

it('permite eliminar una factura vinculada', function () {
    Storage::fake('public');

    Storage::disk('public')->put('facturas/factura_demo.pdf', 'PDF demo');

    $pedido = Pedido::create([
        'n_pedido' => 'PED-FAC-003',
        'cliente' => 'Calzado vel',
        'detalles' => [],
        'subtotal' => 1000,
        'iva' => 160,
        'total' => 1160,
        'fecha_entrega' => now(),
        'folio_factura' => 'A-300',
        'pdf_factura' => 'factura_demo.pdf',
    ]);

    $response = $this->delete(route('facturas.destroy', $pedido->id));

    $response->assertRedirect(route('facturas'));

    $pedido->refresh();

    expect($pedido->folio_factura)->toBeNull();
    expect($pedido->pdf_factura)->toBeNull();

    Storage::disk('public')->assertMissing('facturas/factura_demo.pdf');
});

it('solo muestra pedidos de calzado vel en facturacion', function () {
    Pedido::create([
        'n_pedido' => 'PED-VEL-001',
        'cliente' => 'Calzado vel',
        'detalles' => [],
        'subtotal' => 1000,
        'iva' => 160,
        'total' => 1160,
        'fecha_entrega' => now(),
    ]);

    Pedido::create([
        'n_pedido' => 'PED-OTRO-001',
        'cliente' => 'Otro Cliente',
        'detalles' => [],
        'subtotal' => 1000,
        'iva' => 160,
        'total' => 1160,
        'fecha_entrega' => now(),
    ]);

    Pedido::create([
        'n_pedido' => 'PED-OTRO-FAC',
        'cliente' => 'Otro Cliente',
        'detalles' => [],
        'subtotal' => 1000,
        'iva' => 160,
        'total' => 1160,
        'fecha_entrega' => now(),
        'folio_factura' => 'A-999',
        'pdf_factura' => 'factura_otro.pdf',
    ]);

    $this->get(route('facturas'))
        ->assertOk()
        ->assertSee('PED-VEL-001')
        ->assertDontSee('PED-OTRO-001')
        ->assertDontSee('PED-OTRO-FAC');
});
