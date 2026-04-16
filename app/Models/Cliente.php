<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = ['nombre', 'cve_cliente', 'marca', 'notas', 'telefono', 'correo', 'frecuencia_pago'];
}
