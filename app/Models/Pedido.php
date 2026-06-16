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
        'eliminado',
    ];

    protected $casts = [
        'detalles' => 'array',
        'fecha_entrega' => 'datetime',
        'pagado' => 'boolean',
        'eliminado' => 'boolean',
        'subtotal' => 'decimal:2',
        'iva' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function scopeActivos($query)
    {
        return $query->where('eliminado', false);
    }

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
        $totalAbonado = $this->relationLoaded('abonos')
            ? $this->abonos->sum('monto')
            : $this->abonos()->sum('monto');

        return round((float) $this->total - (float) $totalAbonado, 2);
    }
}
