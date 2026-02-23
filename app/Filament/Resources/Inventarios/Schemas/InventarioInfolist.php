<?php

namespace App\Filament\Resources\Inventarios\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Laravel\Pail\File;

class InventarioInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalles de la Refacción')
                    ->description('Información detallada de la refacción seleccionada.')
                    ->icon('heroicon-o-information-circle')
                    ->compact(false)
                    ->collapsible()
                    ->persistCollapsed()
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('refaccion')
                            ->label('Refacción')
                            ->weight('bold')
                            ->size('lg')
                            ->copyable(),

                        TextEntry::make('marca')
                            ->label('Marca')
                            ->size('md')
                            ->copyable(),
                    ]),

                    Section::make('Cantidad y Costos')
                        ->description('Informacion sobre stock y costos de la refacción.')
                        ->icon('heroicon-o-information-circle')
                        ->compact(false)
                        ->collapsible()
                        ->persistCollapsed()
                        ->columnSpanFull()
                        ->columns(2)
                        ->schema([
                            TextEntry::make('cantidad')
                                ->label('Cantidad Disponible')
                                ->size('md')
                                ->copyable(),

                            TextEntry::make('stock_minimo')
                                ->label('Stock Mínimo')
                                ->size('md')
                                ->copyable(),
                            
                            TextEntry::make('costo_compra')
                                ->label('Costo de Compra')
                                ->prefix('$')
                                ->size('md')
                                ->copyable(),
                            
                            TextEntry::make('precio_venta')
                                ->label('Precio de Venta')
                                ->prefix('$')
                                ->size('md')
                                ->copyable(),
                        ]),

                        Section::make('Imagen de la Refacción')
                        ->description('Imagen representativa de la refacción.')
                        ->icon('heroicon-o-photo')
                        ->compact(false)
                        ->collapsible()
                        ->persistCollapsed()
                        ->columnSpanFull()
                        ->columns(1)
                        ->schema([
                            TextEntry::make('imagen')
                                ->label('URL de la Imagen')
                                ->size('md')
                                ->copyable()
                                ->afterStateUpdated(function (callable $set, callable $get) {
                                    $url = $get('imagen');
                                    if ($url) {
                                        $set('imagen', $url);
                                    }
                                }),
                        ]),
            ]);
    }
}
