<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class FacturaController extends Controller
{
    private const CLIENTE_FACTURACION = 'calzado vel';

    public function index(): View
    {
        $pedidosSinFactura = Pedido::query()
            ->activos()
            ->whereRaw('LOWER(TRIM(cliente)) = ?', [self::CLIENTE_FACTURACION])
            ->whereNull('pdf_factura')
            ->orderByDesc('created_at')
            ->get();

        $pedidosConFactura = Pedido::query()
            ->activos()
            ->whereRaw('LOWER(TRIM(cliente)) = ?', [self::CLIENTE_FACTURACION])
            ->whereNotNull('pdf_factura')
            ->orderByDesc('updated_at')
            ->get();

        return view('facturas', compact('pedidosSinFactura', 'pedidosConFactura'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pedido_id' => ['required', 'integer', 'exists:pedidos,id'],
            'folio_factura' => ['required', 'string', 'max:100'],
            'archivo_pdf' => ['required', 'file', 'mimes:pdf', 'max:2048'],
        ]);

        $pedido = Pedido::query()
            ->activos()
            ->whereRaw('LOWER(TRIM(cliente)) = ?', [self::CLIENTE_FACTURACION])
            ->findOrFail($validated['pedido_id']);

        if ($pedido->pdf_factura) {
            return redirect()
                ->route('facturas')
                ->withErrors(['pedido_id' => 'Este pedido ya tiene una factura vinculada.']);
        }

        $file = $request->file('archivo_pdf');

        $nombreArchivo = sprintf(
            'factura_%s_%s.%s',
            $pedido->id,
            now()->format('YmdHis'),
            $file->getClientOriginalExtension()
        );

        $file->storeAs('facturas', $nombreArchivo, 'public');

        $pedido->update([
            'folio_factura' => trim($validated['folio_factura']),
            'pdf_factura' => $nombreArchivo,
        ]);

        return redirect()
            ->route('facturas')
            ->with('success', 'Factura vinculada correctamente.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $pedido = Pedido::query()
            ->activos()
            ->whereRaw('LOWER(TRIM(cliente)) = ?', [self::CLIENTE_FACTURACION])
            ->findOrFail($id);

        if ($pedido->pdf_factura) {
            Storage::disk('public')->delete('facturas/' . $pedido->pdf_factura);
        }

        $pedido->update([
            'folio_factura' => null,
            'pdf_factura' => null,
        ]);

        return redirect()
            ->route('facturas')
            ->with('success', 'Factura eliminada. Ya puedes subir una nueva.');
    }
}
