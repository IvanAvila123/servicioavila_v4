<?php

namespace App\Filament\Resources\Clientes\Pages;

use App\Filament\Resources\Clientes\ClienteResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewCliente extends ViewRecord
{
    protected static string $resource = ClienteResource::class;


    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Editar Cliente'),
            DeleteAction::make()
                ->label('Eliminar Cliente'),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return "Cliente: " . $this->record->nombre;
    }
}
