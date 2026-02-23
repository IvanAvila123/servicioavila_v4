<?php

namespace App\Filament\Resources\Inventarios\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use PHPUnit\Metadata\Group;
use Termwind\Components\Div;

class InventarioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Inventario')
                    ->description('Información básica del inventario')
                    ->icon('heroicon-o-information-circle')
                    ->iconColor('primary')
                    ->collapsed(false)
                    ->columns(2)
                    ->schema([
                        TextInput::make('refaccion')
                            ->label('Refacción')
                            ->columnSpan(2)
                            ->required()
                            ->live(onBlur:true)
                            ->afterStateUpdated(fn ($state, $set) => $set('refaccion', ucwords(strtolower($state))))
                            ->maxLength(255),
                        TextInput::make('marca')
                            ->label('Marca')
                            ->live(onBlur:true)
                            ->afterStateUpdated(fn ($state, $set) => $set('marca', ucwords(strtolower($state))))
                            ->maxLength(500),
                    ]),

                Section::make('Imagen del Inventario')
                    ->description('Sube una imagen del inventario')
                    ->icon('heroicon-o-photo')
                    ->iconColor('secondary')
                    ->collapsed(false)
                    ->columns(1)
                    ->schema([
                        FileUpload::make('imagen')
                            ->image()
                            ->label('Imagen')
                            ->directory('inventarios')
                            ->columnSpanFull(),
                    ]),

                Section::make('Detalles de Stock y Precios')
                    ->description('Información de stock y precios del inventario')
                    ->icon('heroicon-o-currency-dollar')
                    ->iconColor('success')
                    ->collapsed(false)
                    ->columns(2)
                    ->schema([
                        TextInput::make('cantidad')
                            ->label('Cantidad')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        TextInput::make('stock_minimo')
                            ->label('Stock Mínimo')
                            ->default(2)
                            ->numeric()
                            ->required(),
                        TextInput::make('costo_compra')
                            ->label('Costo de Compra')
                            ->prefix('$')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (callable $set, callable $get) {})
                            ->numeric()
                            ->required(),
                        TextInput::make('precio_venta')
                            ->label('Precio de Venta')
                            ->prefix('$')
                            ->live(onBlur: true)
                            ->numeric()
                            ->required(),
                        Placeholder::make('ganancia_visual')
                            ->label('Ganancia Estimada')
                            ->content(function (callable $get) {
                                $costo = floatval($get('costo_compra'));
                                $venta = floatval($get('precio_venta'));

                                // Si no ha puesto precios, no mostramos nada
                                if ($costo == 0 || $venta == 0) {
                                    return '---';
                                }

                                $ganancia = $venta - $costo;
                                $porcentaje = $costo > 0 ? round(($ganancia / $costo) * 100) : 0;

                                // Formato bonito de dinero
                                return '$' . number_format($ganancia, 2) . ' (' . $porcentaje . '% de margen)';
                            })
                            ->extraAttributes(function (callable $get) {
                                // Truco visual: Verde si gana, Rojo si pierde
                                $costo = floatval($get('costo_compra'));
                                $venta = floatval($get('precio_venta'));
                                $esGanancia = ($venta - $costo) >= 0;

                                return [
                                    'class' => $esGanancia
                                        ? 'text-success-600 font-bold text-lg' // Verde y grande
                                        : 'text-danger-600 font-bold text-lg', // Rojo y grande
                                ];
                            }),
                    ]),
            ]);
    }
}
