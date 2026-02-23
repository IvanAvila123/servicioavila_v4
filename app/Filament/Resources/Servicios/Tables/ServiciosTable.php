<?php

namespace App\Filament\Resources\Servicios\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
;
use SebastianBergmann\CodeCoverage\Filter;

class ServiciosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // LAYOUT PRINCIPAL CON GRID RESPONSIVO
                Grid::make([
                    'default' => 1,
                    'sm' => 1,
                    'md' => 1,
                    'lg' => 1,
                ])
                ->schema([
                    // FILA PRINCIPAL - Split divide en dos secciones
                    Split::make([
                        // SECCIÓN IZQUIERDA: Info principal del servicio
                        Stack::make([
                            // Cliente destacado
                            TextColumn::make('cliente.nombre')
                                ->weight(FontWeight::Bold)
                                ->size('lg')
                                ->searchable()
                                ->icon('heroicon-m-user')
                                ->iconColor('primary')
                                ->color('primary'),
                            
                            // Nombre del servicio
                            TextColumn::make('nombre')
                                ->description(fn ($record) => '#' . str_pad($record->id, 4, '0', STR_PAD_LEFT))
                                ->size('sm')
                                ->color('gray')
                                ->icon('heroicon-m-wrench-screwdriver')
                                ->iconColor('gray'),
                        ])
                        ->space(1),

                        // SECCIÓN DERECHA: Badges de estado y metadata
                        Stack::make([
                            // Badges en línea horizontal
                            Split::make([
                                TextColumn::make('estado')
                                    ->badge()
                                    ->icon(fn ($state) => match ($state) {
                                        'Pendiente' => 'heroicon-m-clock',
                                        'En Proceso' => 'heroicon-m-arrow-path',
                                        'Completado' => 'heroicon-m-check-circle',
                                        'Cancelado' => 'heroicon-m-x-circle',
                                        default => 'heroicon-m-exclamation-circle',
                                    })
                                    ->color(fn ($state) => match ($state) {
                                        'Pendiente' => 'warning',
                                        'En Proceso' => 'info',
                                        'Completado' => 'success',
                                        'Cancelado' => 'danger',
                                        default => 'gray',
                                    })
                                    ->grow(false),

                                TextColumn::make('prioridad')
                                    ->badge()
                                    ->icon(fn ($state) => match ($state) {
                                        'Alta' => 'heroicon-m-arrow-up',
                                        'Media' => 'heroicon-m-minus',
                                        'Baja' => 'heroicon-m-arrow-down',
                                        default => 'heroicon-m-minus',
                                    })
                                    ->color(fn ($state) => match ($state) {
                                        'Alta' => 'danger',
                                        'Media' => 'warning',
                                        'Baja' => 'success',
                                        default => 'gray',
                                    })
                                    ->grow(false),
                            ])
                            ->from('md'),

                            // Tipo de servicio con icono
                            TextColumn::make('tipo')
                                ->description('Categoría')
                                ->size('xs')
                                ->icon('heroicon-m-tag')
                                ->iconColor('gray')
                                ->color('gray')
                                ->grow(false),
                        ])
                        ->alignment(Alignment::End)
                        ->space(2),
                    ])
                    ->from('sm'),
                ]),

                // PANEL COLAPSABLE: Detalles adicionales
                Panel::make([
                    Grid::make([
                        'default' => 1,
                        'md' => 2,
                    ])
                    ->schema([
                        // Columna izquierda del panel
                        Stack::make([
                            // Descripción/Diagnóstico
                            TextColumn::make('descripcion')
                                ->label('Diagnóstico del servicio')
                                ->color('gray')
                                ->wrap()
                                ->lineClamp(3)
                                ->html()
                                ->icon('heroicon-m-document-text')
                                ->iconColor('primary'),

                            // Fechas importantes
                            Split::make([
                                TextColumn::make('fecha_inicio')
                                    ->label('Fecha de inicio')
                                    ->date('d/M/Y')
                                    ->icon('heroicon-m-calendar')
                                    ->iconColor('success')
                                    ->color('success')
                                    ->size('sm')
                                    ->description('Inicio'),

                                TextColumn::make('fecha_fin')
                                    ->label('Fecha estimada')
                                    ->date('d/M/Y')
                                    ->icon('heroicon-m-calendar-days')
                                    ->iconColor('warning')
                                    ->color('warning')
                                    ->size('sm')
                                    ->description('Entrega')
                                    ->placeholder('Sin definir'),
                            ]),
                        ])
                        ->space(3),

                        // Columna derecha del panel
                        Stack::make([
                            // Información de contacto o adicional
                            TextColumn::make('cliente.telefono')
                                ->label('Teléfono de contacto')
                                ->icon('heroicon-m-phone')
                                ->iconColor('info')
                                ->color('gray')
                                ->size('sm')
                                ->description('Contacto')
                                ->placeholder('Sin teléfono'),

                            TextColumn::make('cliente.email')
                                ->label('Email')
                                ->icon('heroicon-m-envelope')
                                ->iconColor('info')
                                ->color('gray')
                                ->size('sm')
                                ->copyable()
                                ->copyMessage('Email copiado!')
                                ->copyMessageDuration(1500)
                                ->description('Correo')
                                ->placeholder('Sin email'),

                        ])
                        ->space(2),
                    ]),
                ])
                ->collapsible()
                ->collapsed()
                ->extraAttributes(['class' => 'bg-gray-50 dark:bg-gray-800/50']),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->label('Filtrar por estado')
                    ->options([
                        'Pendiente' => 'Pendiente',
                        'En Progreso' => 'En Progreso',
                        'Completado' => 'Completado',
                        'Cancelado' => 'Cancelado',
                    ]),

                SelectFilter::make('prioridad')
                    ->label('Filtrar por prioridad')
                    ->options([
                        'Alta' => 'Alta',
                        'Media' => 'Media',
                        'Baja' => 'Baja',
                    ]),
            ])
            ->recordActions([
                
                ViewAction::make()
                    ->label('Ver')
                    ->icon('heroicon-m-eye')
                    ->color('info'),
                    

                ActionGroup::make([
                    Action::make('completar')
                        ->label('Completado')
                        ->icon('heroicon-m-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('¿Marcar como Completado?')
                        ->modalDescription('El servicio se marcará como completado.')
                        ->modalSubmitActionLabel('Sí, completar')
                        ->action(fn ($record) => $record->update(['estado' => 'Completado']))
                        ->hidden(fn ($record) => $record->estado === 'Completado'),
                    Action::make('cancelar')
                        ->label('Cancelar Servicio')
                        ->icon('heroicon-m-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('¿Cancelar este servicio?')
                        ->modalDescription('El servicio se marcará como cancelado.')
                        ->modalSubmitActionLabel('Sí, cancelar')
                        ->action(fn ($record) => $record->update(['estado' => 'Cancelado']))
                        ->hidden(fn ($record) => $record->estado === 'Cancelado'),
                ])
                ->label('Estado')
                ->icon('heroicon-m-arrow-path')
                ->color('gray'),

            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Eliminar seleccionados')
                        ->icon('heroicon-m-trash')
                        ->requiresConfirmation()
                        ->modalHeading('¿Eliminar servicios seleccionados?')
                        ->modalDescription('Esta acción eliminará todos los servicios seleccionados y no se puede deshacer.')
                        ->modalSubmitActionLabel('Sí, eliminar')
                        ->modalCancelActionLabel('Cancelar'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->poll('30s'); // Auto-actualiza cada 30 segundos
    }
}