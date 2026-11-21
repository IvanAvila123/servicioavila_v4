<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\DetalleServicio;
use App\Models\Inventario;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Datos de ejemplo para el modo demo (php artisan demo:reset).
 * Las fechas son relativas a hoy para que el dashboard siempre tenga datos del mes.
 */
class DemoModeSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Usuario Demo',
            'email' => config('demo.login'),
            'password' => config('demo.password'),
        ]);

        $clientes = collect([
            ['Ferretería El Tornillo', '993 214 5566', 'compras@eltornillo.mx', 'Av. Méndez 120, Centro', 'FET180512AB1'],
            ['Clínica Dental Sonrisa', '993 118 7720', 'admin@sonrisa.mx', 'Calle 5 de Mayo 45', 'CDS200301KL9'],
            ['Restaurante La Ceiba', '993 455 0912', 'gerencia@laceiba.mx', 'Paseo Tabasco 1401', 'RLC150823QW2'],
            ['Papelería Escolar Díaz', '993 302 6644', 'ventas@papeleriadiaz.mx', 'Av. Universidad 233', 'PED190715TR4'],
            ['Hotel Plaza Real', '993 600 1188', 'mantenimiento@plazareal.mx', 'Blvd. Ruiz Cortines 890', 'HPR120402MN7'],
            ['Despacho Contable Ruiz', '993 271 3390', 'contacto@ruizcontadores.mx', 'Calle Juárez 77', 'DCR170910PL3'],
        ])->map(fn ($c) => Cliente::create([
            'nombre' => $c[0], 'telefono' => $c[1], 'email' => $c[2], 'direccion' => $c[3], 'rfc' => $c[4],
        ]));

        $items = collect([
            ['Disco SSD 480 GB', 'Kingston', 14, 4, 520, 890],
            ['Memoria RAM 8 GB DDR4', 'Crucial', 9, 3, 410, 720],
            ['Fuente de poder 500 W', 'EVGA', 3, 4, 650, 1100],
            ['Tóner HP 85A', 'HP', 2, 5, 380, 690],
            ['Cable UTP Cat6 (m)', 'Belden', 180, 50, 9, 18],
            ['Pasta térmica', 'Arctic', 20, 5, 85, 180],
            ['Ventilador 120 mm', 'Cooler Master', 11, 4, 120, 240],
            ['Batería no-break 12V', 'APC', 1, 3, 450, 820],
        ])->map(fn ($i) => Inventario::create([
            'refaccion' => $i[0], 'marca' => $i[1], 'cantidad' => $i[2], 'stock_minimo' => $i[3],
            'costo_compra' => $i[4], 'precio_venta' => $i[5],
        ]));

        // [cliente, nombre, descripción, costo, estado, prioridad, tipo, inicio (días), fin (días), próximo mantenimiento (días)]
        $servicios = [
            [0, 'Mantenimiento preventivo de 6 equipos', 'Limpieza interna, cambio de pasta térmica y revisión de discos.', 2400, 'Completado', 'Media', 'Mantenimiento', -20, -19, 4],
            [1, 'Instalación de red en consultorios', 'Cableado Cat6 para 8 nodos y configuración del módem.', 6800, 'En Progreso', 'Alta', 'Instalación', -3, null, null],
            [2, 'Reparación de computadora de caja', 'No enciende; diagnóstico de fuente de poder.', 1450, 'Pendiente', 'Alta', 'Reparación', -1, null, null],
            [3, 'Mantenimiento de impresoras', 'Limpieza de rodillos y cambio de tóner en 3 impresoras.', 1900, 'Completado', 'Baja', 'Mantenimiento', -12, -12, 10],
            [4, 'Cambio de baterías de no-break', 'Reemplazo de baterías en 4 no-break de recepción.', 3600, 'En Progreso', 'Media', 'Reparación', -2, null, null],
            [5, 'Actualización de equipos de oficina', 'Instalación de SSD y memoria RAM en 4 equipos.', 7200, 'Completado', 'Media', 'Instalación', -30, -28, 16],
            [0, 'Revisión de cámaras de seguridad', 'Diagnóstico de 2 cámaras sin señal.', 950, 'Pendiente', 'Baja', 'Reparación', 0, null, null],
            [2, 'Mantenimiento trimestral', 'Servicio programado a equipos de cocina y caja.', 1800, 'Pendiente', 'Media', 'Mantenimiento', 2, null, null],
        ];

        foreach ($servicios as $i => $s) {
            $servicio = Servicio::create([
                'cliente_id' => $clientes[$s[0]]->id,
                'nombre' => $s[1],
                'descripcion' => $s[2],
                'costo_estimado' => $s[3],
                'estado' => $s[4],
                'prioridad' => $s[5],
                'tipo' => $s[6],
                'fecha_inicio' => now()->addDays($s[7])->setTime(9 + $i % 6, 0),
                'fecha_fin' => $s[8] !== null ? now()->addDays($s[8])->setTime(17, 0) : null,
                'proximo_mantenimiento' => $s[9] !== null ? now()->addDays($s[9]) : null,
            ]);

            $item = $items[$i % $items->count()];
            $cantidad = 1 + $i % 3;
            DetalleServicio::create([
                'servicio_id' => $servicio->id,
                'inventario_id' => $item->id,
                'descripcion' => $item->refaccion,
                'modelo' => 'Equipo ' . ($i + 1),
                'cantidad' => $cantidad,
                'precio_unitario' => $item->precio_venta,
                'subtotal' => $item->precio_venta * $cantidad,
            ]);
        }
    }
}
