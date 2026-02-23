<?php

namespace App\Filament\Resources\Inventarios\Pages;

use App\Filament\Resources\Inventarios\InventarioResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewInventario extends ViewRecord
{
    protected static string $resource = InventarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
            ->label('Editar Refacción')
            ->icon('heroicon-o-pencil')
            ->color('info')
            ->requiresConfirmation()
            ->modalHeading('¿Está seguro de editar esta refacción?')
            ->modalSubheading('Asegúrese de revisar los cambios antes de confirmar.'),
            DeleteAction::make()
            ->label('Eliminar Refacción')
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('¿Está seguro de eliminar esta refacción?')
            ->modalSubheading('Esta acción no se puede deshacer.'),
        ];
    }

    public function getTitle(): string
    {
        return "Refacción: " . $this->record->refaccion;
    }
}
