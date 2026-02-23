<?php

namespace App\Filament\Resources\Inventarios\Tables;

use Dom\Text;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Nette\Utils\Image;

class InventariosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Usamos 'Stack' para apilar los elementos uno sobre otro dentro de la tarjeta
                Stack::make([
                    ImageColumn::make('imagen')
                        ->imageWidth('80%')
                        ->imageHeight('90%')
                        ->wrap()
                        ->extraImgAttributes(['class' => 'object-cover h-48 w-full rounded-t-xl']), // Estilo CSS para que se vea bonita

                    // Dentro de la tarjeta, separamos Titulo y Precio
                    Stack::make([
                        TextColumn::make('refaccion')
                        ->weight('bold')
                        ->size('lg')
                        ->searchable(),

                        TextColumn::make('marca')
                        ->prefix('Marca: ')
                        ->color('gray')
                        ->size('sm')
                        ->searchable(),

                        // Un Split para poner Precio a la izquierda y Stock a la derecha
                        Split::make([
                            TextColumn::make('precio_venta')
                            ->money('MXN')
                            ->weight('bold')
                            ->color('primary')
                            ->size('lg'), // Precio grande

                           TextColumn::make('cantidad')
                            ->badge()
                            ->color(fn ($state) => $state <= 2 ? 'danger' : 'success')
                            ->label('Stock'),
                        ]),
                    ])
                    ->space(2)->extraAttributes(['class' => 'p-4']) // Espacio interno
                    ->extraAttributes(['class' => 'p-4']), // Añadimos "aire" (padding) dentro de la tarjeta
                ])
            ])
            ->contentGrid([ // ESTO ES LA CLAVE: Define cuántas columnas hay
                'md' => 2, // En tabletas: 2 columnas
                'xl' => 3, // En pantallas grandes: 3 columnas
            ])
            ->stackedOnMobile()
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                ->label('Ver Refacción'),
                EditAction::make()
                ->label('Editar Refacción'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
