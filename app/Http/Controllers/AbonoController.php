<?php

namespace App\Http\Controllers;

use App\Models\Abono;
use App\Models\Pedido;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AbonoController extends Controller
{
    public function index(): View
    {
        $pedidosPendientes = Pedido::query()
            ->where('pagado', false)
            ->with('abonos')
            ->orderByDesc('created_at')
            ->get();

        $pedidosPagados = Pedido::query()
            ->where('pagado', true)
            ->with('abonos')
            ->orderByDesc('updated_at')
            ->get();

        return view('abonos', compact('pedidosPendientes', 'pedidosPagados'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pedido_id' => ['required', 'integer', 'exists:pedidos,id'],
            'monto' => ['required', 'numeric', 'min:0.01'],
            'metodo_pago' => ['required', 'string', 'max:50'],
            'fecha_pago' => ['required', 'date'],
        ]);

        return DB::transaction(function () use ($validated) {
            $pedido = Pedido::query()
                ->whereKey($validated['pedido_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($pedido->pagado) {
                return redirect()
                    ->route('abonos.index')
                    ->withErrors(['pedido_id' => 'Este pedido ya está marcado como pagado.']);
            }

            $saldoPendiente = $pedido->saldoPendiente();
            $monto = round((float) $validated['monto'], 2);

            if ($monto > $saldoPendiente) {
                return redirect()
                    ->route('abonos.index')
                    ->withErrors([
                        'monto' => 'El abono no puede ser mayor al saldo pendiente de $' . number_format($saldoPendiente, 2),
                    ]);
            }

            Abono::create([
                'pedido_id' => $pedido->id,
                'monto' => $monto,
                'metodo_pago' => $validated['metodo_pago'],
                'fecha_pago' => $validated['fecha_pago'],
            ]);

            $pedido->load('abonos');

            if ($pedido->saldoPendiente() <= 0) {
                $pedido->update(['pagado' => true]);

                return redirect()
                    ->route('abonos.index')
                    ->with('success', 'Abono registrado y pedido liquidado correctamente.');
            }

            return redirect()
                ->route('abonos.index')
                ->with('success', 'Abono registrado correctamente.');
        });
    }

    public function marcarPagado(int $id): RedirectResponse
    {
        $pedido = Pedido::query()
            ->with('abonos')
            ->findOrFail($id);

        if ($pedido->saldoPendiente() <= 0) {
            $pedido->update(['pagado' => true]);

            return redirect()
                ->route('abonos.index')
                ->with('success', 'Pedido marcado como liquidado.');
        }

        return redirect()
            ->route('abonos.index')
            ->withErrors([
                'pedido_id' => 'Aún queda saldo pendiente por cubrir.',
            ]);
    }
}