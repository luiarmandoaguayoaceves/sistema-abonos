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
        // El cliente ahora viene de su propio input
        $cliente = $request->input('cliente');
        $items = json_decode($request->datos_pedido, true);
        $aplicaIva = $request->input('iva_aplicado') == 1;

        $subtotal = collect($items)->sum('subtotalItem');
        $iva = $aplicaIva ? ($subtotal * 0.16) : 0;

        Pedido::create([
            'cliente'  => $cliente,
            'detalles' => $items,
            'subtotal' => $subtotal,
            'iva'      => $iva,
            'total'    => $subtotal + $iva,
        ]);

        return redirect()->route('seguimiento')->with('success', 'Pedido de ' . $cliente . ' guardado.');
    }
}
