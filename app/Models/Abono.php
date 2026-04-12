<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Abono extends Model
{
    protected $fillable = ['pedido_id', 'monto', 'metodo_pago', 'fecha_pago'];
}
