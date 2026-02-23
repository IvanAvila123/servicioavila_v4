<?php

namespace App\Filament\Resources\Clientes\Pages;

use App\Filament\Resources\Clientes\ClienteResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCliente extends EditRecord
{
    protected static string $resource = ClienteResource::class;
    protected static ?string $title = 'Editar Cliente';

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
            ->label('Ver Cliente'),
            DeleteAction::make()
            ->label('Eliminar Cliente'),
        ];
    }
}
