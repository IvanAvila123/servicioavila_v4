<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleServicio extends Model
{
    protected $fillable = [
        'servicio_id', 'inventario_id', 'descripcion', 'num_serie', 'num_producto', 'modelo', 'cantidad', 'precio_unitario', 'subtotal'
    ];

    public function servicio()
    {
        return $this->belongsTo(Servicio::class);
    }

    public function inventario()
    {
        return $this->belongsTo(Inventario::class);
    }
}
