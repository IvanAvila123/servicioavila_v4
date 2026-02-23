<?php

namespace App\Filament\Resources\Servicios\Pages;

use App\Filament\Resources\Servicios\ServicioResource;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Storage;

class ViewServicio extends ViewRecord
{
    protected static string $resource = ServicioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('whatsapp')
                    ->label('Enviar WhatsApp')
                    ->icon('heroicon-m-chat-bubble-left-ellipsis')
                    ->color('success')
                    ->url(function ($record) {
                        $telefono = preg_replace('/\D/', '', $record->cliente->telefono ?? '');
                        if (strlen($telefono) === 10) {
                            $telefono = '52' . $telefono;
                        }

                        $folio = str_pad($record->id, 5, '0', STR_PAD_LEFT);
                        $total = number_format($record->detalles->sum('subtotal') + $record->costo_estimado, 2);

                        $mensaje = "Hola *{$record->cliente->nombre}*, le informamos sobre su servicio en *Servicio Ávila*:\n\n"
                            . "📋 *Folio:* #{$folio}\n"
                            . "🔧 *Equipo/Servicio:* {$record->nombre}\n"
                            . "📊 *Estado:* {$record->estado}\n"
                            . "💰 *Total:* \${$total} MXN\n\n"
                            . "Gracias por confiar en nosotros. Cualquier duda estamos a sus órdenes.";

                        return 'https://wa.me/' . $telefono . '?text=' . urlencode($mensaje);
                    })
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => !empty($record->cliente->telefono)),
                Action::make('imprimir')
                    ->label('Orden de servicio')
                    ->icon('heroicon-m-printer')
                    ->color('info')
                    ->url(fn ($record) => route('servicio.pdf', $record->id))
                    ->openUrlInNewTab(),
                Action::make('subir_orden_firmada')
                    ->label(fn ($record) => $record->orden_firmada ? 'Reemplazar Orden Firmada' : 'Subir Orden Firmada')
                    ->icon('heroicon-m-document-arrow-up')
                    ->color('primary')
                    ->schema([
                        FileUpload::make('orden_firmada')
                            ->label('Documento firmado (foto o PDF)')
                            ->disk('public')
                            ->directory('ordenes-firmadas')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])
                            ->maxSize(10240)
                            ->required(),
                    ])
                    ->action(function (array $data, $record) {
                        if ($record->orden_firmada) {
                            Storage::disk('public')->delete($record->orden_firmada);
                        }
                        $record->update(['orden_firmada' => $data['orden_firmada']]);
                        Notification::make()
                            ->title('Orden firmada guardada')
                            ->success()
                            ->send();
                    }),
            ])
            ->label('Acciones')
            ->icon('heroicon-m-bolt')
            ->color('primary')
            ->button(),

            ActionGroup::make([
                EditAction::make()
                    ->label('Editar Servicio')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('¿Está seguro de editar este servicio?')
                    ->modalDescription('Asegúrese de revisar los cambios antes de confirmar.'),
                DeleteAction::make()
                    ->label('Eliminar Servicio')
                    ->requiresConfirmation()
                    ->modalHeading('¿Está seguro de eliminar este servicio?')
                    ->modalDescription('Esta acción no se puede deshacer.'),
            ])
            ->label('Gestionar')
            ->icon('heroicon-m-cog-6-tooth')
            ->color('gray')
            ->button(),
        ];
    }

    public function getTitle(): string
    {
        return "Servicio: " . $this->record->cliente->nombre;
    }
}
