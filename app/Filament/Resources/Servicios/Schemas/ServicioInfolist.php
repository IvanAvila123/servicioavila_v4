<?php

namespace App\Filament\Resources\Servicios\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

class ServicioInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
               Section::make('Detalles del Servicio')
                    ->description('Información detallada del servicio')
                    ->icon('heroicon-o-information-circle')
                    ->iconColor('secondary')
                    ->compact(false)
                    ->collapsible()
                    ->persistCollapsed()
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('cliente.nombre')
                            ->label('Cliente')
                            ->weight('bold')
                            ->size('lg')
                            ->icon('heroicon-o-user')
                            ->copyable()
                            ->copyMessage('Nombre del cliente copiado')
                            ->iconColor('primary'),
                        TextEntry::make('nombre')
                            ->label('Nombre del Servicio')
                            ->weight('bold')
                            ->size('lg')
                            ->icon('heroicon-o-wrench')
                            ->copyable()
                            ->copyMessage('Nombre del servicio copiado')
                            ->iconColor('primary'),
                        TextEntry::make('descripcion')
                            ->label('Descripción del Servicio')
                            ->copyable()
                            ->copyMessage('Descripción del servicio copiada')
                            ->icon('heroicon-o-document-text')
                            ->iconColor('primary'),
                        TextEntry::make('cliente.direccion')
                            ->label('Dirección del Cliente')
                            ->copyable()
                            ->copyMessage('Dirección del cliente copiada')
                            ->icon('heroicon-o-map-pin')
                            ->iconColor('primary'),
                     ]),

                     Section::make('Información Adicional')
                     ->description('Estado, prioridad y tipo del servicio')
                     ->icon('heroicon-o-list-bullet')
                     ->iconColor('secondary')
                     ->compact(false)
                     ->collapsible()
                     ->persistCollapsed()
                     ->columnSpanFull()
                     ->columns(3)
                     ->schema([
                        TextEntry::make('estado')
                            ->label('Estado')
                            ->size('lg')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Pendiente' => 'info',
                                'En Progreso' => 'primary',
                                'Completado' => 'success',
                                'Cancelado' => 'danger',
                                default => 'gray',
                            })
                            ->copyable()
                            ->copyMessage('Estado del servicio copiado')
                            ->icon('heroicon-o-list-bullet')
                            ->iconColor('primary'),
                        TextEntry::make('prioridad')
                            ->label('Prioridad')
                            ->size('lg')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'alta' => 'danger',
                                'media' => 'warning',
                                'baja' => 'success',
                                default => 'gray',
                            })
                            ->copyable()
                            ->copyMessage('Prioridad del servicio copiada')
                            ->icon('heroicon-o-exclamation-circle')
                            ->iconColor('primary'),
                        TextEntry::make('tipo')
                            ->label('Tipo de Servicio')
                            ->size('lg')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Mantenimiento' => 'primary',
                                'Instalación' => 'info',
                                'Reparación' => 'danger',
                                default => 'gray',
                            })
                            ->copyable()
                            ->copyMessage('Tipo de servicio copiado')
                            ->icon('heroicon-o-cog')
                            ->iconColor('primary'),
                     ]),

                Section::make('Fechas del Servicio')
                    ->description('Fechas de creación y actualización del servicio')
                    ->icon('heroicon-o-clock')
                    ->iconColor('secondary')
                    ->compact(false)
                    ->collapsible()
                    ->persistCollapsed()
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('fecha_inicio')
                            ->label('Fecha De Inicio')
                            ->dateTime()
                            ->copyable()
                            ->copyMessage('Fecha de creación copiada')
                            ->icon('heroicon-o-calendar')
                            ->iconColor('primary'),
                        TextEntry::make('fecha_fin')
                            ->label('Fecha De Finalización')
                            ->dateTime()
                            ->copyable()
                            ->copyMessage('Fecha de actualización copiada')
                            ->icon('heroicon-o-calendar')
                            ->iconColor('primary'),
                        TextEntry::make('proximo_mantenimiento')
                            ->label('Próximo Mantenimiento')
                            ->date()
                            ->copyable()
                            ->copyMessage('Fecha del próximo mantenimiento copiada')
                            ->icon('heroicon-o-calendar')
                            ->iconColor('primary'),
                    ]),

                Section::make('Orden Firmada')
                    ->description('Documento firmado por el cliente')
                    ->icon('heroicon-o-document-check')
                    ->iconColor('success')
                    ->collapsible()
                    ->persistCollapsed()
                    ->columnSpanFull()
                    ->schema([
                        ImageEntry::make('orden_firmada')
                            ->disk('public')
                            ->label('Documento')
                            ->height(500)
                            ->hidden(fn ($state): bool => empty($state) || str_ends_with(strtolower((string) $state), '.pdf')),
                        TextEntry::make('orden_firmada')
                            ->label('Documento PDF')
                            ->formatStateUsing(fn (): string => 'Abrir / Descargar PDF')
                            ->url(fn (string $state): string => Storage::disk('public')->url($state))
                            ->openUrlInNewTab()
                            ->hidden(fn ($state): bool => empty($state) || !str_ends_with(strtolower((string) $state), '.pdf')),
                    ])
                    ->visible(fn ($record): bool => !empty($record->orden_firmada)),
            ]);
    }
}
