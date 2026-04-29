<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillable = ['cliente', 'n_pedido', 'detalles', 'subtotal', 'iva', 'total', 'folio_factura', 'pdf_factura', 'pagado'];

    protected $casts = [
        'detalles' => 'array',
    ];

    public function abonos()
    {
        return $this->hasMany(Abono::class);
    }

    public function saldoPendiente()
    {
        // Usamos round() para evitar errores de precisión de decimales en PHP
        return round($this->total - $this->abonos()->sum('monto'), 2);
    }
}
