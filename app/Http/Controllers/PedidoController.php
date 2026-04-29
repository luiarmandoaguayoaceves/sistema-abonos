<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;

class PedidoController extends Controller
{
    public function index()
{
    // Obtenemos los pedidos ordenados del más reciente al más antiguo
    $pedidos = Pedido::orderBy('created_at', 'desc')->get();

    return view('seguimiento', compact('pedidos'));
}
    public function store(Request $request)
    {
        $request->validate([
            'pedido' => 'nullable|string',
            'cliente' => 'required|string',
            'datos_pedido' => 'required|json',
            'iva_aplicado' => 'required|boolean',
        ]);

        $cliente = $request->input('cliente');
        $items = json_decode($request->input('datos_pedido'), true);
        $aplicaIva = $request->boolean('iva_aplicado');

        $subtotal = collect($items)->sum('subtotalItem');
        $iva = $aplicaIva ? ($subtotal * 0.16) : 0;

        Pedido::create([
            'n_pedido' => $request->input('pedido'),
            'cliente' => $cliente,
            'detalles' => $items,
            'subtotal' => $subtotal,
            'iva' => $iva,
            'total' => $subtotal + $iva,
        ]);

        return redirect()->route('seguimiento')->with('success', 'Pedido de ' . $cliente . ' guardado.');
    }
}
