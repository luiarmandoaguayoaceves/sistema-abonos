<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\Abono;

class AbonoController extends Controller
{
    public function index() {
        // Traemos pedidos pendientes y pagados por separado
        $pedidosPendientes = Pedido::where('pagado', false)->with('abonos')->orderBy('created_at', 'desc')->get();
        $pedidosPagados = Pedido::where('pagado', true)->with('abonos')->orderBy('updated_at', 'desc')->get();
        return view('abonos', compact('pedidosPendientes', 'pedidosPagados'));
    }

    public function store(Request $request) {
        $request->validate([
            'pedido_id' => 'required|exists:pedidos,id',
            'monto' => 'required|numeric|min:1',
            'metodo_pago' => 'required',
            'fecha_pago' => 'required|date',
        ]);

        Abono::create($request->all());

        return back()->with('success', 'Abono registrado correctamente.');
    }

    public function marcarPagado($id) {
        $pedido = Pedido::findOrFail($id);
        
        if($pedido->saldoPendiente() <= 0) {
            $pedido->update(['pagado' => true]);
            return back()->with('success', 'Pedido marcado como LIQUIDADO.');
        }

        return back()->with('error', 'Aún queda saldo pendiente por cubrir.');
    }
}