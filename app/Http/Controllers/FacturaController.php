<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use Illuminate\Support\Facades\Storage;

class FacturaController extends Controller
{
    public function index() {
        // Separamos la lógica desde el controlador para una vista más limpia
        $pedidosSinFactura = Pedido::whereNull('pdf_factura')->orderBy('created_at', 'desc')->get();
        $pedidosConFactura = Pedido::whereNotNull('pdf_factura')->orderBy('updated_at', 'desc')->get();

        return view('facturas', compact('pedidosSinFactura', 'pedidosConFactura'));
    }

    public function store(Request $request) {
        $request->validate([
            'pedido_id' => 'required|exists:pedidos,id',
            'folio_factura' => 'required|string',
            'archivo_pdf' => 'required|mimes:pdf|max:2048', // Max 2MB
        ]);

        $pedido = Pedido::findOrFail($request->pedido_id);

        if ($request->hasFile('archivo_pdf')) {
            $file = $request->file('archivo_pdf');
            $nombreArchivo = 'factura_' . $pedido->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('facturas', $nombreArchivo, 'public');
            
            $pedido->update([
                'folio_factura' => $request->folio_factura,
                'pdf_factura' => $nombreArchivo
            ]);
        }

        return back()->with('success', 'Factura vinculada correctamente.');
    }

    public function destroy($id) {
        $pedido = Pedido::findOrFail($id);
        
        // Eliminar archivo físico
        if ($pedido->pdf_factura) {
            Storage::disk('public')->delete('facturas/' . $pedido->pdf_factura);
        }

        // Limpiar base de datos
        $pedido->update([
            'folio_factura' => null,
            'pdf_factura' => null
        ]);

        return back()->with('success', 'Factura eliminada. Ya puedes subir una nueva.');
    }
}