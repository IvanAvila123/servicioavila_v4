<?php

namespace App\Filament\Resources\Servicios\RelationManagers;

use App\Models\Inventario;
use Dom\Text;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DetalleServicioRelationManager extends RelationManager
{
    protected static string $relationship = 'detalles';

    protected static ?string $title = 'Refacciones y Materiales';

    protected static ?string $modelLabel = 'refacción';

    protected static ?string $pluralModelLabel = 'refacciones';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('inventario.refaccion')
            ->columns([
                Split::make([
                    Stack::make([
                        TextColumn::make('inventario.refaccion')
                            ->label('Producto/Refacción')
                            ->searchable()
                            ->sortable()
                            ->weight(FontWeight::Bold)
                            ->size('base')
                            ->icon('heroicon-m-cube')
                            ->iconColor('primary')
                            ->summarize(
                                Sum::make()
                                    ->label('Total de Refacciones')
                            ),

                        TextColumn::make('descripcion')
                            ->label('')
                            ->color('gray')
                            ->size('sm')
                            ->icon('heroicon-m-information-circle')
                            ->iconColor('gray')
                            ->placeholder('Sin notas')
                            ->limit(50),

                        TextColumn::make('num_serie')
                            ->label('N. Serie')
                            ->prefix('N° de serie: ')
                            ->color('gray')
                            ->size('sm')
                            ->icon('heroicon-m-hashtag')
                            ->iconColor('gray')
                            ->placeholder('N/A'),
                        
                        TextColumn::make('num_producto')
                            ->label('N. Producto')
                            ->prefix('N° de producto: ')
                            ->color('gray')
                            ->size('sm')                            
                            ->icon('heroicon-m-truck')
                            ->iconColor('gray')
                            ->placeholder('N/A'),
                        
                        TextColumn::make('modelo')
                            ->label('Modelo')
                            ->prefix('Modelo: ')
                            ->color('gray')
                            ->size('sm')
                            ->icon('heroicon-m-device-phone-mobile')
                            ->iconColor('gray')
                            ->placeholder('N/A'),
                    ])
                        ->space(1),

                    Stack::make([
                        Split::make([
                            TextColumn::make('cantidad')
                                ->label('Cant.')
                                ->badge()
                                ->color('info')
                                ->size('sm')
                                ->alignCenter()
                                ->grow(false)
                                ->summarize(
                                    Sum::make()
                                        ->label('Total unidades')
                                ),

                            TextColumn::make('precio_unitario')
                                ->label('P. Unit.')
                                ->money('MXN')
                                ->size('sm')
                                ->color('gray')
                                ->grow(false),

                            
                        ]),

                        TextColumn::make('subtotal')
                            ->label('Subtotal')
                            ->money('MXN')
                            ->weight(FontWeight::Bold)
                            ->size('lg')
                            ->color('success')
                            ->icon('heroicon-m-currency-dollar')
                            ->iconColor('success')
                            ->summarize([
                                Sum::make()
                                    ->label('TOTAL')
                                    ->money('MXN'),
                                Summarizer::make()
                                    ->label('Mano de obra')
                                    ->using(fn () => $this->getOwnerRecord()->costo_estimado ?? 0)
                                    ->money('MXN'),
                            ]),


                    ])

                        ->space(1)
                        ->alignment(\Filament\Support\Enums\Alignment::End),
                ])
                    ->from('sm'),

                    
            ])
            ->filters([])
            ->headerActions([
                // 🟢 BOTÓN AGREGAR CON DESCUENTO DE INVENTARIO
                Action::make('agregar')
                    ->icon('heroicon-o-plus')
                    ->label('Agregar Refacción')
                    ->color('primary')
                    ->modalHeading('Agregar Refacción al Servicio')
                    ->modalDescription('Selecciona el producto y la cantidad a agregar')
                    ->modalWidth('2xl')
                    ->modalSubmitActionLabel('Agregar')
                    ->modalCancelActionLabel('Cancelar')
                    ->form([
                        Grid::make(2)
                            ->schema([
                                Select::make('inventario_id')
                                    ->label('Refacción / Producto')
                                    ->relationship('inventario', 'refaccion')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, ?string $state) {
                                        if ($state) {
                                            $inventario = Inventario::find($state);
                                            if ($inventario) {
                                                $set('precio_unitario', $inventario->precio_venta ?? 0);
                                                $set('descripcion', $inventario->descripcion ?? '');
                                                $set('stock_disponible', $inventario->cantidad ?? 0);
                                            }
                                        }
                                    })
                                    ->createOptionForm([
                                        TextInput::make('refaccion')
                                            ->label('Nombre de la refacción')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('precio_venta')
                                            ->label('Precio de venta')
                                            ->numeric()
                                            ->prefix('$')
                                            ->required(),
                                        TextInput::make('cantidad')
                                            ->label('Stock inicial')
                                            ->numeric()
                                            ->default(1),
                                    ])
                                    ->columnSpan(2),

                                // Campo oculto para mostrar stock disponible
                                TextInput::make('stock_disponible')
                                    ->label('Stock disponible')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->suffix('unidades')
                                    ->helperText('Stock actual en inventario')
                                    ->columnSpan(2),

                                TextInput::make('cantidad')
                                    ->label('Cantidad a usar')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        self::calcularSubtotal($get, $set);
                                        
                                        // Validar stock disponible
                                        $cantidad = (int) ($get('cantidad') ?? 0);
                                        $stockDisponible = (int) ($get('stock_disponible') ?? 0);
                                        
                                        if ($cantidad > $stockDisponible) {
                                            Notification::make()
                                                ->warning()
                                                ->title('Stock insuficiente')
                                                ->body("Solo hay {$stockDisponible} unidades disponibles")
                                                ->send();
                                        }
                                    })
                                    ->helperText('Cantidad que se descontará del inventario'),

                                TextInput::make('precio_unitario')
                                    ->label('Precio Unitario')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        self::calcularSubtotal($get, $set);
                                    }),

                                TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->numeric()
                                    ->prefix('$')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->extraInputAttributes(['class' => 'text-lg font-bold'])
                                    ->columnSpan(2),

                                TextInput::make('descripcion')
                                    ->label('Nota sobre este ítem (opcional)')
                                    ->placeholder('Ej: Importada, Original, Genérica, etc.')
                                    ->maxLength(255)
                                    ->columnSpan(2),
                                TextInput::make('num_serie')
                                    ->label('Número de serie')
                                    ->placeholder('Ej: SN123456789')
                                    ->maxLength(255)
                                    ->columnSpan(2),
                                TextInput::make('num_producto')
                                    ->label('Número de producto')
                                    ->placeholder('Ej: NP987654321')
                                    ->maxLength(255)
                                    ->columnSpan(2),
                                TextInput::make('modelo')
                                    ->label('Modelo')
                                    ->placeholder('Ej: Modelo XYZ')
                                    ->maxLength(255)->columnSpan(2)
                            ]),
                                

                    ])
                    ->action(function (array $data) {
                        $servicio = $this->getOwnerRecord();
                        
                        // Validar stock disponible
                        $inventario = Inventario::find($data['inventario_id']);
                        if (!$inventario) {
                            Notification::make()
                                ->danger()
                                ->title('Error')
                                ->body('Producto no encontrado en inventario')
                                ->send();
                            return;
                        }
                        
                        if ($inventario->cantidad < $data['cantidad']) {
                            Notification::make()
                                ->danger()
                                ->title('Stock insuficiente')
                                ->body("Solo hay {$inventario->cantidad} unidades disponibles")
                                ->send();
                            return;
                        }

                        // ✅ DESCONTAR DEL INVENTARIO
                        $inventario->decrement('cantidad', $data['cantidad']);

                        // Crear el detalle
                        $servicio->detalles()->create([
                            'inventario_id' => $data['inventario_id'],
                            'cantidad' => $data['cantidad'],
                            'precio_unitario' => $data['precio_unitario'],
                            'subtotal' => $data['subtotal'],
                            'descripcion' => $data['descripcion'] ?? null,
                        ]);

                        // Notificación de éxito
                        Notification::make()
                            ->success()
                            ->title('Refacción agregada correctamente')
                            ->body("Stock actualizado: {$inventario->cantidad} unidades restantes")
                            ->send();
                    }),
            ])
            ->recordActions([
                // 🟡 BOTÓN EDITAR CON AJUSTE DE INVENTARIO
                Action::make('editar')
                    ->label('Editar')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->tooltip('Editar refacción')
                    ->modalHeading('Editar Refacción')
                    ->modalDescription('Modifica la cantidad, precio o notas')
                    ->modalWidth('2xl')
                    ->modalSubmitActionLabel('Guardar cambios')
                    ->modalCancelActionLabel('Cancelar')
                    ->fillForm(fn($record) => [
                        'inventario_id' => $record->inventario_id,
                        'cantidad_original' => $record->cantidad,
                        'cantidad' => $record->cantidad,
                        'precio_unitario' => $record->precio_unitario,
                        'subtotal' => $record->subtotal,
                        'descripcion' => $record->descripcion,
                        'num_serie' => $record->num_serie,
                        'num_producto' => $record->num_producto,
                        'modelo' => $record->modelo,
                    ])
                    ->form([
                        Grid::make(2)
                            ->schema([
                                Select::make('inventario_id')
                                    ->label('Refacción / Producto')
                                    ->relationship('inventario', 'refaccion')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->disabled() // No permitir cambiar el producto
                                    ->dehydrated()
                                    ->columnSpan(2),

                                // Campo oculto para guardar cantidad original
                                TextInput::make('cantidad_original')
                                    ->hidden()
                                    ->dehydrated(false),

                                TextInput::make('cantidad')
                                    ->label('Cantidad')
                                    ->numeric()
                                    ->minValue(1)
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        self::calcularSubtotal($get, $set);
                                    })
                                    ->helperText('Modificar la cantidad ajustará el inventario'),

                                TextInput::make('precio_unitario')
                                    ->label('Precio Unitario')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        self::calcularSubtotal($get, $set);
                                    }),

                                TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->numeric()
                                    ->prefix('$')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->extraInputAttributes(['class' => 'text-lg font-bold'])
                                    ->columnSpan(2),

                                TextInput::make('descripcion')
                                    ->label('Nota sobre este ítem (opcional)')
                                    ->placeholder('Ej: Importada, Original, Genérica, etc.')
                                    ->maxLength(255)
                                    ->columnSpan(2),
                                TextInput::make('num_serie')
                                    ->label('Número de serie')
                                    ->placeholder('Ej: SN123456789')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, $set) => $set('num_serie', ucwords(strtoupper($state))))
                                    ->maxLength(255)
                                    ->columnSpan(2),
                                TextInput::make('num_producto')
                                    ->label('Número de producto')
                                    ->placeholder('Ej: NP987654321')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, $set) => $set('num_producto', ucwords(strtoupper($state))))
                                    ->maxLength(255)
                                    ->columnSpan(2),
                                TextInput::make('modelo')
                                    ->label('Modelo')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, $set) => $set('modelo', ucwords(strtoupper($state))))
                                    ->placeholder('Ej: Modelo XYZ')
                                    ->maxLength(255)->columnSpan(2)
                            ]),
                    ])
                    ->action(function ($record, array $data) {
                        $cantidadOriginal = $record->cantidad;
                        $cantidadNueva = $data['cantidad'];
                        $diferencia = $cantidadNueva - $cantidadOriginal;

                        // ✅ AJUSTAR INVENTARIO según la diferencia
                        if ($diferencia != 0) {
                            $inventario = $record->inventario;
                            
                            if ($diferencia > 0) {
                                // Se aumentó la cantidad → descontar más del inventario
                                if ($inventario->cantidad < $diferencia) {
                                    Notification::make()
                                        ->danger()
                                        ->title('Stock insuficiente')
                                        ->body("Solo hay {$inventario->cantidad} unidades disponibles")
                                        ->send();
                                    return;
                                }
                                $inventario->decrement('cantidad', $diferencia);
                                $mensaje = "Se descontaron {$diferencia} unidades adicionales del inventario";
                            } else {
                                // Se disminuyó la cantidad → regresar al inventario
                                $devolver = abs($diferencia);
                                $inventario->increment('cantidad', $devolver);
                                $mensaje = "Se regresaron {$devolver} unidades al inventario";
                            }
                        }

                        // Actualizar el registro
                        $record->update([
                            'cantidad' => $data['cantidad'],
                            'precio_unitario' => $data['precio_unitario'],
                            'subtotal' => $data['subtotal'],
                            'descripcion' => $data['descripcion'] ?? null,
                            'num_serie' => $data['num_serie'] ?? null,
                            'num_producto' => $data['num_producto'] ?? null,
                            'modelo' => $data['modelo'] ?? null,
                        ]);

                        // Notificación de éxito
                        Notification::make()
                            ->success()
                            ->title('Refacción actualizada correctamente')
                            ->body($mensaje ?? 'Sin cambios en el inventario')
                            ->send();
                    }),

                // 🔴 BOTÓN ELIMINAR CON REGRESO A INVENTARIO
                Action::make('eliminar')
                    ->label('Eliminar')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->tooltip('Eliminar refacción')
                    ->modalHeading('¿Eliminar esta refacción?')
                    ->modalDescription('La cantidad se regresará al inventario')
                    ->modalSubmitActionLabel('Sí, eliminar')
                    ->modalCancelActionLabel('Cancelar')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        // ✅ REGRESAR AL INVENTARIO
                        if ($record->inventario) {
                            $record->inventario->increment('cantidad', $record->cantidad);
                        }

                        $cantidadDevuelta = $record->cantidad;
                        $nombreProducto = $record->inventario->refaccion ?? 'Producto';

                        // Eliminar el registro
                        $record->delete();

                        // Notificación de éxito
                        Notification::make()
                            ->success()
                            ->title('Refacción eliminada correctamente')
                            ->body("{$cantidadDevuelta} unidades de '{$nombreProducto}' regresadas al inventario")
                            ->send();
                    }),

                ForceDeleteAction::make()
                    ->label('Eliminar permanentemente'),

                RestoreAction::make()
                    ->label('Restaurar'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Eliminar seleccionadas')
                        ->modalHeading('¿Eliminar refacciones seleccionadas?')
                        ->modalDescription('Las cantidades se regresarán al inventario')
                        ->modalSubmitActionLabel('Sí, eliminar')
                        ->successNotificationTitle('Refacciones eliminadas')
                        // Hook para regresar al inventario antes de eliminar
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                if ($record->inventario) {
                                    $record->inventario->increment('cantidad', $record->cantidad);
                                }
                            }
                        }),

                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Sin refacciones agregadas')
            ->emptyStateDescription('Agrega las refacciones y materiales utilizados en este servicio')
            ->emptyStateIcon('heroicon-o-cube-transparent');
    }

    /**
     * Calcula el subtotal automáticamente
     */
    protected static function calcularSubtotal(Get $get, Set $set): void
    {
        $cantidad = (float)($get('cantidad') ?? 0);
        $precioUnitario = (float)($get('precio_unitario') ?? 0);
        $subtotal = $cantidad * $precioUnitario;

        $set('subtotal', number_format($subtotal, 2, '.', ''));
    }
}