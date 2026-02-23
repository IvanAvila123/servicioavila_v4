<?php

namespace App\Filament\Resources\Clientes\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClienteInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Cliente')
                    ->description('Detalles básicos del cliente')
                    ->icon('heroicon-o-user-circle')
                    ->iconColor('secondary')
                    ->compact(false)
                    ->collapsible()
                    ->persistCollapsed()
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('nombre')
                            ->label('Nombre Completo')
                            ->weight('bold')
                            ->size('lg')
                            ->icon('heroicon-o-user')
                            ->copyable()
                            ->copyMessage('Nombre del cliente copiado')
                            ->iconColor('primary'),
                        TextEntry::make('telefono')
                            ->label('Número de Teléfono')
                            ->copyable()
                            ->copyMessage('Teléfono del cliente copiado')
                            ->icon('heroicon-o-phone')
                            ->iconColor('primary'),
                        TextEntry::make('email')
                            ->label('Correo Electrónico')
                            ->copyable()
                            ->copyMessage('Correo electrónico del cliente copiado')
                            ->icon('heroicon-o-envelope')
                            ->iconColor('primary'),
                        TextEntry::make('rfc')
                            ->label('RFC')
                            ->copyable()
                            ->copyMessage('RFC del cliente copiado')
                            ->icon('heroicon-o-identification')
                            ->iconColor('primary'),
                    ]),

                Section::make('Domicilio del Cliente')
                    ->description('Dirección del cliente')
                    ->icon('heroicon-o-map-pin')
                    ->iconColor('secondary')
                    ->compact(false)
                    ->collapsible()
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('direccion')
                            ->label('Dirección')
                            ->copyable()
                            ->copyMessage('Dirección del cliente copiada')
                            ->icon('heroicon-o-home')
                            ->iconColor('primary'),
                    ]),

                Section::make('Información de Auditoría')
                    ->description('Fechas de creación y actualización del cliente')
                    ->icon('heroicon-o-clock')
                    ->iconColor('secondary')
                    ->compact(false)
                    ->collapsible()
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Fecha de Creación')
                            ->icon('heroicon-o-calendar')
                            ->iconColor('primary')
                            ->disabled(),
                        TextEntry::make('updated_at')
                            ->label('Fecha de Actualización')
                            ->icon('heroicon-o-calendar')
                            ->iconColor('primary')
                            ->disabled(),
                    ]),
            ]);
    }
}
