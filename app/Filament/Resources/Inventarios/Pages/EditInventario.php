<?php

namespace App\Filament\Resources\Inventarios\Pages;

use App\Filament\Resources\Inventarios\InventarioResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditInventario extends EditRecord
{
    protected static string $resource = InventarioResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Editar Refacción: ' . $this->record->refaccion;
    }


    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
            ->label('Ver Refacción'),
            DeleteAction::make()
            ->label('Eliminar Refacción')
            ->requiresConfirmation()
             ->modalHeading('¿Estás seguro de eliminar esta refacción?')
             ->modalSubheading('Esta acción no se puede deshacer.'),
        ];
    }
}
