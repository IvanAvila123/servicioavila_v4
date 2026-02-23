<?php

namespace App\Filament\Resources\Clientes\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ClienteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Cliente')
                    ->description('Complete la información básica del cliente')
                    ->icon('heroicon-o-user-circle')
                    ->iconcolor('secondary')
                    ->compact(false)
                    ->collapsible()
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('nombre')
                            ->label('Nombre Completo')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, $set) => $set('nombre', ucwords(strtolower($state))))
                            ->maxLength(255),
                        TextInput::make('telefono')
                            ->label('Número de Teléfono')
                            ->tel()
                            ->required()
                            ->maxLength(20),
                        TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('rfc')
                            ->label('RFC')
                            ->default('sin RFC')
                            ->maxLength(13),
                    ]),

                    Section::make('Información Adicional')
                    ->description('Complete la dirección del cliente')
                    ->icon('heroicon-o-map-pin')
                    ->iconcolor('secondary')
                    ->compact(false)
                    ->collapsible()
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('direccion')
                            ->label('Dirección')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, $set) => $set('direccion', ucwords(strtoupper($state))))
                            ->maxLength(255),
                    ]),

                    Section::make('información de Auditoría')
                    ->description('Fechas de creación y actualización del cliente')
                    ->icon('heroicon-o-clock')
                    ->iconcolor('secondary')
                    ->compact(false)
                    ->collapsible()
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        DatePicker::make('created_at')
                            ->label('Fecha de Creación')
                            ->prefixIcon(Heroicon::Calendar)
                            ->prefixIconColor('secondary')
                            ->default(now())
                            ->disabled(),
                        DatePicker::make('updated_at')
                            ->label('Fecha de Actualización')
                            ->prefixIcon(Heroicon::Calendar)
                            ->prefixIconColor('secondary')
                            ->default(now())
                            ->disabled(),
                    ]),
                
            ]);
    }
}
