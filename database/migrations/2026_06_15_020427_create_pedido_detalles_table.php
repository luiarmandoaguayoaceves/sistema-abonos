<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedido_detalles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pedido_id')
                ->constrained('pedidos')
                ->cascadeOnDelete();

            $table->string('modelo');
            $table->string('color');
            $table->unsignedInteger('pares');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);

            $table->timestamps();

            $table->index('pedido_id');
            $table->index('modelo');
            $table->index('color');
        });

        $pedidos = DB::table('pedidos')
            ->select('id', 'detalles', 'created_at', 'updated_at')
            ->whereNotNull('detalles')
            ->get();

        foreach ($pedidos as $pedido) {
            $items = json_decode($pedido->detalles, true);

            if (! is_array($items)) {
                continue;
            }

            foreach ($items as $item) {
                DB::table('pedido_detalles')->insert([
                    'pedido_id' => $pedido->id,
                    'modelo' => $item['modelo'] ?? 'Sin modelo',
                    'color' => $item['color'] ?? 'Sin color',
                    'pares' => (int) ($item['pares'] ?? 0),
                    'precio_unitario' => (float) ($item['precio'] ?? 0),
                    'subtotal' => (float) ($item['subtotalItem'] ?? 0),
                    'created_at' => $pedido->created_at,
                    'updated_at' => $pedido->updated_at,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pedido_detalles');
    }
};