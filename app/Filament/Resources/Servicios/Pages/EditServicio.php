<?php

namespace App\Filament\Resources\Servicios\Pages;

use App\Filament\Resources\Servicios\ServicioResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditServicio extends EditRecord
{
    protected static string $resource = ServicioResource::class;
    protected static ?string $title = 'Editar Servicio';

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
            ->label('Ver Detalles del Servicio'),
            DeleteAction::make()
            ->label('Eliminar Servicio')
            ->requiresConfirmation()
            ->modalHeading('¿Está seguro de eliminar este servicio?')
            ->modalSubheading('Esta acción no se puede deshacer.'),
        ];
    }
}
