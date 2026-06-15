<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pedido extends Model
{
    protected $fillable = [
        'cliente',
        'n_pedido',
        'detalles',
        'subtotal',
        'iva',
        'total',
        'fecha_entrega',
        'folio_factura',
        'pdf_factura',
        'pagado',
    ];

    protected $casts = [
        'detalles' => 'array',
        'fecha_entrega' => 'datetime',
        'pagado' => 'boolean',
        'subtotal' => 'decimal:2',
        'iva' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function detallesPedido(): HasMany
    {
        return $this->hasMany(PedidoDetalle::class);
    }

    public function abonos(): HasMany
    {
        return $this->hasMany(Abono::class);
    }

    public function saldoPendiente(): float
    {
        return round($this->total - $this->abonos()->sum('monto'), 2);
    }
}