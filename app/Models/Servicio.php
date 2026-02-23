<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    protected $fillable = [
        'cliente_id', 'nombre', 'descripcion', 'notas', 'costo_estimado', 'estado', 'prioridad', 'tipo', 'fecha_inicio', 'fecha_fin', 'proximo_mantenimiento', 'orden_firmada'
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'proximo_mantenimiento' => 'date',
        'costo_estimado' => 'decimal:2', // Asegura que sea número
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function detalles()
    {
        return $this->hasMany(DetalleServicio::class);
    }

    public function detalleServicios()
    {
        return $this->hasMany(DetalleServicio::class);
    }



    public function inventarios()
    {
        return $this->belongsToMany(Inventario::class, 'servicio_inventario')
                    ->withPivot('cantidad', 'precio_aplicado')
                    ->withTimestamps();
    }

    protected static function booted(): void
    {
        static::updating(function ($servicio) {
            // Si el estado cambió a 'terminado' y aún no tiene fecha próxima
            if ($servicio->isDirty('estado') && $servicio->estado === 'Completado' && is_null($servicio->proximo_mantenimiento)) {
                
                // Lógica: Si es Mantenimiento = 1 año, si es Reparación = 6 meses (ejemplo)
                $meses = match($servicio->tipo) {
                    'Mantenimiento' => 12, // 1 año
                    'Instalación' => 12,
                    default => 6, // Reparaciones cada 6 meses
                };

                $servicio->proximo_mantenimiento = now()->addMonths($meses);
            }
        });
    }
}
