<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PedidoController extends Controller
{
    public function index(): View
    {
        $pedidos = Pedido::query()
        ->where('eliminado', false)
        ->with('detallesPedido')
        ->orderByDesc('fecha_entrega')
        ->orderByDesc('created_at')
        ->get();

        return view('seguimiento', compact('pedidos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pedido' => ['nullable', 'string', 'max:255'],
            'cliente' => ['required', 'string', 'max:255'],
            'fecha_entrega' => ['required', 'date'],
            'datos_pedido' => ['required', 'json'],
            'iva_aplicado' => ['required', 'boolean'],
        ]);

        $items = json_decode($validated['datos_pedido'], true);

        if (! is_array($items) || count($items) === 0) {
            return back()
                ->withErrors(['datos_pedido' => 'El pedido debe tener al menos un producto.'])
                ->withInput();
        }

        foreach ($items as $index => $item) {
            if (
                empty($item['modelo']) ||
                empty($item['color']) ||
                ! isset($item['pares']) ||
                ! isset($item['precio'])
            ) {
                return back()
                    ->withErrors(['datos_pedido' => "El producto #" . ($index + 1) . " está incompleto."])
                    ->withInput();
            }
        }

        $aplicaIva = $request->boolean('iva_aplicado');

        $subtotal = collect($items)->sum(function ($item) {
            return ((int) $item['pares']) * ((float) $item['precio']);
        });

        $iva = $aplicaIva ? round($subtotal * 0.16, 2) : 0;
        $total = round($subtotal + $iva, 2);

        DB::transaction(function () use ($validated, $items, $subtotal, $iva, $total) {
            $pedido = Pedido::create([
                'n_pedido' => $validated['pedido'] ?? null,
                'cliente' => $validated['cliente'],
                'detalles' => $items, // respaldo temporal
                'subtotal' => $subtotal,
                'iva' => $iva,
                'total' => $total,
                'fecha_entrega' => $validated['fecha_entrega'],
                'eliminado' => false,
            ]);

            foreach ($items as $item) {
                $pares = (int) $item['pares'];
                $precio = (float) $item['precio'];

                $pedido->detallesPedido()->create([
                    'modelo' => $item['modelo'],
                    'color' => $item['color'],
                    'pares' => $pares,
                    'precio_unitario' => $precio,
                    'subtotal' => round($pares * $precio, 2),
                ]);
            }
        });

        return redirect()
            ->route('seguimiento')
            ->with('success', 'Pedido de ' . $validated['cliente'] . ' guardado.');
    }

    public function updateFecha(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'fecha_entrega' => ['required', 'date'],
        ]);

        $pedido = Pedido::findOrFail($id);

        $pedido->update([
            'fecha_entrega' => $validated['fecha_entrega'],
        ]);

        return redirect()
            ->back()
            ->with('success', 'Fecha actualizada correctamente.');
    }
    public function destroy(int $id): RedirectResponse
    {
        $pedido = Pedido::query()
            ->where('eliminado', false)
            ->findOrFail($id);

        $pedido->update([
            'eliminado' => true,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Pedido eliminado correctamente.');
    }
}