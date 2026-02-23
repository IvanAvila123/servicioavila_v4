<?php

namespace App\Filament\Widgets;

use App\Models\Servicio;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecordatoriosMantenimiento extends BaseWidget
{
    // Configuración para que se vea ancho completo
    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = '📅 Próximos Mantenimientos (Siguientes 30 días)';

    protected static ?int $sort = 2; // Para que salga arriba de todo

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Buscamos servicios cuya fecha próxima sea entre HOY y dentro de un mes
                Servicio::query()
                    ->where('proximo_mantenimiento', '>=', now()->subMonths(2))
                    ->where('proximo_mantenimiento', '<=', now()->addDays(30))
                    ->where('estado', 'Completado') // Solo mostrar si el servicio actual ya está completado
            )
            ->columns([
                Tables\Columns\TextColumn::make('proximo_mantenimiento')
                    ->label('Fecha Sugerida')
                    ->date('d M Y') // Ej: 14 Feb 2027
                    ->sortable()
                    ->badge()
                    ->color('warning'), // Color amarillo de alerta

                Tables\Columns\TextColumn::make('cliente.nombre')
                    ->label('Cliente')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('cliente.telefono')
                    ->label('Teléfono')
                    ->icon('heroicon-m-phone')
                    ->url(fn($record) => "tel:{$record->cliente->telefono}") // Clic para llamar
                    ->color('gray'),

                Tables\Columns\TextColumn::make('nombre')
                    ->label('Equipo / Servicio Anterior')
                    ->description(fn($record) => 'Realizado el: ' . $record->updated_at->format('d/m/Y')),
            ])
            ->actions([
                // Botón rápido para crear el NUEVO servicio
                Action::make('crear_nuevo')
                    ->label('Agendar')
                    ->icon('heroicon-o-calendar-days')
                    ->button()
                    ->url(fn($record) => route('filament.admin.resources.servicios.create', [
                        'cliente_id' => $record->cliente_id, // Pre-llenar cliente
                        'tipo' => 'mantenimiento', // Pre-llenar tipo
                        'descripcion' => "Mantenimiento anual programado (Equipo: {$record->nombre})",
                    ])),
            ]);
    }
}
