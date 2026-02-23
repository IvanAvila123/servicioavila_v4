<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    protected $fillable = [
        'refaccion', 'marca', 'imagen', 'cantidad', 'stock_minimo',
        'costo_compra', 'precio_venta'
    ];

    public function detalleServicios()
    {
        return $this->hasMany(DetalleServicio::class);
    }

    /**
     * Alias de la relación anterior (por compatibilidad)
     */
    public function detalles()
    {
        return $this->hasMany(DetalleServicio::class);
    }
}
