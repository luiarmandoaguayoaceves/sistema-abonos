<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Pedido;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $clientes = Cliente::query()
            ->orderBy('nombre')
            ->get();

        $pedidosPorCliente = Pedido::query()
            ->with('abonos')
            ->latest()
            ->get()
            ->groupBy(fn (Pedido $pedido) => $this->normalizeClientName($pedido->cliente));

        $clientesDashboard = $clientes
            ->map(function (Cliente $cliente) use ($pedidosPorCliente) {
                $pedidos = $pedidosPorCliente->get(
                    $this->normalizeClientName($cliente->nombre),
                    collect()
                );

                return $this->buildClientSummary($cliente, $pedidos);
            })
            ->sortByDesc('saldo_pendiente')
            ->values();

        $pedidosSinClienteRegistrado = $pedidosPorCliente
            ->filter(function (Collection $pedidos, string $clienteNombre) use ($clientes) {
                return ! $clientes->contains(
                    fn (Cliente $cliente) => $this->normalizeClientName($cliente->nombre) === $clienteNombre
                );
            })
            ->flatMap(fn (Collection $pedidos) => $pedidos)
            ->values();

        $totalVendido = $clientesDashboard->sum('total_vendido');
        $totalAbonado = $clientesDashboard->sum('total_abonado');
        $saldoPendiente = $clientesDashboard->sum('saldo_pendiente');
        $clientesConDeuda = $clientesDashboard->where('saldo_pendiente', '>', 0)->count();

        return view('welcome', compact(
            'clientesDashboard',
            'pedidosSinClienteRegistrado',
            'totalVendido',
            'totalAbonado',
            'saldoPendiente',
            'clientesConDeuda'
        ));
    }

    private function buildClientSummary(Cliente $cliente, Collection $pedidos): array
    {
        $totalVendido = $pedidos->sum(fn (Pedido $pedido) => (float) $pedido->total);

        $totalAbonado = $pedidos->sum(function (Pedido $pedido) {
            return $pedido->abonos->sum(fn ($abono) => (float) $abono->monto);
        });

        $saldoPendiente = $pedidos->sum(fn (Pedido $pedido) => max($pedido->saldoPendiente(), 0));

        return [
            'cliente' => $cliente,
            'pedidos_total' => $pedidos->count(),
            'pedidos_pendientes' => $pedidos->where('pagado', false)->count(),
            'pedidos_pagados' => $pedidos->where('pagado', true)->count(),
            'total_vendido' => round($totalVendido, 2),
            'total_abonado' => round($totalAbonado, 2),
            'saldo_pendiente' => round($saldoPendiente, 2),
            'ultimo_pedido' => $pedidos->first(),
        ];
    }

    private function normalizeClientName(?string $name): string
    {
        return mb_strtolower(trim($name ?? ''));
    }
}