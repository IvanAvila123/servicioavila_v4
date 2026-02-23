<?php

namespace App\Filament\Resources\Servicios\Schemas;

use Dom\Text;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServicioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Servicio')
                    ->description('Complete los datos del servicio')
                    ->icon('heroicon-o-wrench')
                    ->iconColor('info')
                    ->compact(false)
                    ->collapsible()
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Select::make('cliente_id')
                            ->label('Cliente')
                            ->searchable()
                            ->preload()
                            ->relationship('cliente', 'nombre')
                            ->required(),
                        Select::make('estado')
                            ->label('Estado')
                            ->searchable()
                            ->preload()
                            ->options([
                                'Pendiente' => 'Pendiente',
                                'En Progreso' => 'En Progreso',
                                'Completado' => 'Completado',
                                'Cancelado' => 'Cancelado',
                            ])->default('Pendiente')
                            ->required(),
                        Select::make('prioridad')
                            ->label('Prioridad')
                            ->searchable()
                            ->preload()
                            ->options([
                                'Alta' => 'Alta',
                                'Media' => 'Media',
                                'Baja' => 'Baja',

                            ])->default('Media')
                            ->required(),
                        Select::make('tipo')
                            ->label('Tipo de Servicio')
                            ->searchable()
                            ->preload()
                            ->options([
                                'Mantenimiento' => 'Mantenimiento',
                                'Instalación' => 'Instalación',
                                'Reparación' => 'Reparación',
                            ])->default('Reparación')
                            ->required(),
                        TextInput::make('nombre')
                            ->label('Nombre del Servicio')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn($state, $set) => $set('nombre', ucwords(strtolower($state))))
                            ->required(),
                        Textarea::make('descripcion')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn($state, $set) => $set('descripcion', ucwords(strtolower($state))))
                            ->label('Descripción del Servicio'),

                        TextInput::make('notas')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn($state, $set) => $set('notas', ucwords(strtolower($state))))
                            ->label('Notas Adicionales')
                            ->columnSpanFull()
                            ->helperText('Información adicional o comentarios sobre el servicio')
                    ]),

                Section::make('Fechas del Servicio')
                    ->description('Fechas de inicio y fin del servicio')
                    ->icon('heroicon-o-calendar')
                    ->iconColor('info')
                    ->compact(false)
                    ->collapsible()
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        DatePicker::make('fecha_inicio')
                            ->label('Fecha de Inicio')
                            ->default(now())
                            ->required(),

                        DatePicker::make('fecha_fin')
                            ->label('Fecha de Fin')
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $fechaInicio = $get('fecha_inicio');
                                if ($fechaInicio && $state < $fechaInicio) {
                                    $set('fecha_fin', $fechaInicio);
                                }
                            }),
                    ]),

                Section::make('Cierre y Facturación')
                    ->schema([
                        Group::make()->schema([
                            // 💰 CAMPO: MANO DE OBRA
                            TextInput::make('costo_estimado')
                                ->label('Costo Mano de Obra / Servicio')
                                ->numeric()
                                ->prefix('$')
                                ->default(0)
                                ->required()
                                ->minValue(0)
                                ->step(0.01)
                                ->helperText('Costo por el servicio técnico o mantenimiento (sin incluir refacciones)')
                                ->placeholder('0.00'),

                            // 📅 CAMPO: PRÓXIMO MANTENIMIENTO
                            DatePicker::make('proximo_mantenimiento')
                                ->label('Programar Siguiente Visita')
                                ->placeholder('Automático al finalizar...')
                                ->native(false) // Usa el calendario bonito de Filament
                                ->displayFormat('d/m/Y')
                                ->minDate(now())
                                ->helperText('Si lo dejas vacío, el sistema calculará 6 meses o 1 año automáticamente al terminar el servicio.'),
                        ])->columns(2),
                    ])
                    ->collapsible(),
            ]);
    }
}
