<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('pedidos', 'detalles')) {
            return;
        }

        $pedidos = DB::table('pedidos')
            ->select('id', 'detalles', 'created_at', 'updated_at')
            ->whereNotNull('detalles')
            ->get();

        foreach ($pedidos as $pedido) {
            $tieneDetallesNormalizados = DB::table('pedido_detalles')
                ->where('pedido_id', $pedido->id)
                ->exists();

            if ($tieneDetallesNormalizados) {
                continue;
            }

            $items = json_decode($pedido->detalles, true);

            if (! is_array($items)) {
                continue;
            }

            foreach ($items as $item) {
                $pares = (int) ($item['pares'] ?? 0);
                $precio = (float) ($item['precio'] ?? 0);

                DB::table('pedido_detalles')->insert([
                    'pedido_id' => $pedido->id,
                    'modelo' => $item['modelo'] ?? 'Sin modelo',
                    'color' => $item['color'] ?? 'Sin color',
                    'pares' => $pares,
                    'precio_unitario' => $precio,
                    'subtotal' => (float) ($item['subtotalItem'] ?? round($pares * $precio, 2)),
                    'created_at' => $pedido->created_at,
                    'updated_at' => $pedido->updated_at,
                ]);
            }
        }

        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn('detalles');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('pedidos', 'detalles')) {
            return;
        }

        Schema::table('pedidos', function (Blueprint $table) {
            $table->json('detalles')->nullable()->after('cliente');
        });

        $pedidos = DB::table('pedidos')->select('id')->get();

        foreach ($pedidos as $pedido) {
            $detalles = DB::table('pedido_detalles')
                ->where('pedido_id', $pedido->id)
                ->orderBy('id')
                ->get()
                ->map(fn ($detalle) => [
                    'modelo' => $detalle->modelo,
                    'color' => $detalle->color,
                    'pares' => (int) $detalle->pares,
                    'precio' => (float) $detalle->precio_unitario,
                    'subtotalItem' => (float) $detalle->subtotal,
                ])
                ->values();

            DB::table('pedidos')
                ->where('id', $pedido->id)
                ->update(['detalles' => $detalles->toJson()]);
        }
    }
};
