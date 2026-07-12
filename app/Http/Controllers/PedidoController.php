<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PedidoController extends Controller
{
    public function index(Request $request): View
    {
        $selectedMonth = $request->query('mes');

        if ($selectedMonth && ! preg_match('/^\d{4}-\d{2}$/', $selectedMonth)) {
            $selectedMonth = null;
        }

        $monthNames = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre',
        ];

        $allPedidos = Pedido::query()
            ->activos()
            ->orderByDesc('fecha_entrega')
            ->orderByDesc('created_at')
            ->get();

        $monthOptions = $allPedidos
            ->map(function (Pedido $pedido) use ($monthNames) {
                $fecha = $pedido->fechaSeguimiento();

                return [
                    'value' => $fecha->format('Y-m'),
                    'label' => $monthNames[(int) $fecha->format('n')] . ' ' . $fecha->format('Y'),
                ];
            })
            ->unique('value')
            ->values();

        $pedidos = Pedido::query()
            ->activos()
            ->with('detallesPedido')
            ->when($selectedMonth, function ($query) use ($selectedMonth) {
                [$year, $month] = explode('-', $selectedMonth);

                $query->where(function ($query) use ($year, $month) {
                    $query->where(function ($query) use ($year, $month) {
                        $query->whereNotNull('fecha_entrega')
                            ->whereYear('fecha_entrega', $year)
                            ->whereMonth('fecha_entrega', $month);
                    })->orWhere(function ($query) use ($year, $month) {
                        $query->whereNull('fecha_entrega')
                            ->whereYear('created_at', $year)
                            ->whereMonth('created_at', $month);
                    });
                });
            })
            ->orderByDesc('fecha_entrega')
            ->orderByDesc('created_at')
            ->get();

        $pedidosPorMes = $pedidos
            ->groupBy(function (Pedido $pedido) {
                return $pedido->mesSeguimiento();
            })
            ->map(function ($pedidosMes, string $monthKey) use ($monthNames) {
                [$year, $month] = explode('-', $monthKey);

                return [
                    'label' => $monthNames[(int) $month] . ' ' . $year,
                    'pedidos' => $pedidosMes,
                    'pares' => $pedidosMes->sum(fn (Pedido $pedido) => $pedido->totalPares()),
                    'total' => $pedidosMes->sum(fn (Pedido $pedido) => (float) $pedido->total),
                ];
            });

        $totalPares = $pedidos->sum(fn (Pedido $pedido) => $pedido->totalPares());
        $totalPedidos = $pedidos->sum(fn (Pedido $pedido) => (float) $pedido->total);

        return view('seguimiento', compact('pedidos', 'pedidosPorMes', 'monthOptions', 'selectedMonth', 'totalPares', 'totalPedidos'));
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
