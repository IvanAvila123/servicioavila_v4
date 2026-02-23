<?php

namespace App\Filament\Widgets;

use App\Models\Servicio;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Validation\Rules\In;

class StatsOverview extends StatsOverviewWidget
{
    // Esto hace que se actualice solo cada 15 segundos (opcional)
    protected ?string $pollingInterval = '10s';
    
    // Para que aparezca arriba de la tabla de recordatorios
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // === 📅 1. SERVICIOS NUEVOS (Cantidad) ===
        // Contamos cuántos servicios nuevos llegaron este mes (created_at)
        $nuevosMes = Servicio::whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();
            
        // Contamos cuántos llegaron el mes pasado para comparar (↑/↓)
        $nuevosMesPasado = Servicio::whereYear('created_at', Carbon::now()->subMonth()->year)
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->count();
        
        $diffNuevos = $nuevosMes - $nuevosMesPasado;
        $descNuevos = $diffNuevos >= 0 
            ? "↑ {$diffNuevos} más que el mes pasado" 
            : "↓ " . abs($diffNuevos) . " menos que el mes pasado";


        // === 💰 2. GANANCIAS DEL MES (Dinero) ===
        // Solo sumamos los TERMINADOS, porque son los que ya se cobraron
        $serviciosTerminadosMes = Servicio::with('detalles')
            ->where('estado', 'Completado')
            ->whereYear('updated_at', Carbon::now()->year)
            ->whereMonth('updated_at', Carbon::now()->month)
            ->get();

        // Sumamos Mano de Obra + Subtotales de cada detalle
        $totalMes = $serviciosTerminadosMes->sum(function ($servicio) {
            return $servicio->costo_estimado + $servicio->detalles->sum('subtotal');
        });


        // === 📈 3. GANANCIAS DEL AÑO (Dinero) ===
        $serviciosTerminadosAnio = Servicio::with('detalles')
            ->where('estado', 'Completado')
            ->whereYear('updated_at', Carbon::now()->year)
            ->get();

        $totalAnio = $serviciosTerminadosAnio->sum(function ($servicio) {
            return $servicio->costo_estimado + $servicio->detalles->sum('subtotal');
        });

        $serviciosMes = Servicio::with(['detalles.inventario'])
            ->where('estado', 'Completado')
            ->whereYear('updated_at', Carbon::now()->year)
            ->whereMonth('updated_at', Carbon::now()->month)
            ->get();

            // 2. Inicializamos nuestros contadores en cero
        $ingresosTotales = 0; // Todo lo que entró a la caja
        $costoRefacciones = 0; // Lo que costó comprar las piezas

        // 3. Hacemos las matemáticas servicio por servicio
        foreach ($serviciosMes as $servicio) {
            // A. Sumamos la mano de obra a los ingresos
            $ingresosTotales += $servicio->costo_estimado;

            foreach ($servicio->detalles as $detalle) {
                // B. Sumamos lo que el cliente pagó por las piezas (precio_venta * cantidad)
                $ingresosTotales += $detalle->subtotal;

                // C. Sumamos lo que le costó a tu papá comprar esa pieza
                // Validamos que la refacción siga existiendo en la base de datos
                if ($detalle->inventario) {
                    $costoRefacciones += ($detalle->inventario->costo_compra * $detalle->cantidad);
                }
            }
        }

        // 4. Calculamos la Ganancia Real (Utilidad Neta)
        $gananciaNeta = $ingresosTotales - $costoRefacciones;

        // ==========================================
        //  TARJETAS QUE SE MOSTRARÁN EN EL DASHBOARD
        // ==========================================
        return [
            Stat::make('Servicios Nuevos (Mes)', $nuevosMes)
                ->description($descNuevos) // Muestra la comparación
                ->descriptionIcon($diffNuevos >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($diffNuevos >= 0 ? 'success' : 'danger')
                ->chart([7, 2, 10, 3, 15, 4, 17]), // Gráfica estética (dummy data por ahora)

            Stat::make('Ingresos Brutos (Mes)', '$' . number_format($ingresosTotales, 2))
                ->description('Total cobrado a clientes')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('info'),

            Stat::make('Inversión en Piezas (Mes)', '$' . number_format($costoRefacciones, 2))
                ->description('Costo real de las refacciones usadas')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('danger'), // Rojo/Naranja porque es dinero que salió

            Stat::make('Ganancia Libre (Mes)', '$' . number_format($gananciaNeta, 2))
                ->description('Utilidad neta (Mano de obra + margen de piezas)')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success') // Verde porque es ganancia
                ->chart([3, 5, 8, 12, 10, 15, 20]), // Gráfica motivacional

            Stat::make('Ingresos Anuales', '$' . number_format($totalAnio, 2))
                ->description('Total acumulado ' . Carbon::now()->year)
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('success'),
        ];
    }
}
