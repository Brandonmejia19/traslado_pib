<?php

namespace App\Observers;

use App\Models\TrasladoSecundario;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use App\Models\User;
class Traslados24HObserver
{
    /**
     * Handle the TrasladoSecundario "created" event.
     */
    public function created(TrasladoSecundario $trasladoSecundario): void
    {
        //
    }

    /**
     * Handle the TrasladoSecundario "updated" event.
     */
    public function updated(TrasladoSecundario $trasladoSecundario): void
    {
        if (
            $trasladoSecundario->estado === 'Finalizado'
        ) {

            // Obtener usuarios con roles específicos
            $recipients = User::role(['Administrador', 'Médico', 'APH', 'Gestor'])->get();

            foreach ($recipients as $recipient) {
                Notification::make()
                    ->title('Traslado Finalizado')
                    ->icon('heroicon-o-check-circle')
                    ->iconColor('success')
                    ->success()
                    ->body("
                    ✅ Correlativo: {$trasladoSecundario->correlativo} /" . "\n" . "
                    🏥 Diagnóstico: {$trasladoSecundario->diagnostico_paciente} /" . "\n" . "
                    👤 Finalizado por: {$trasladoSecundario->operador_nombre}
                ")
                    ->actions([
                        Action::make('Marcar como leído')
                            ->markAsRead()
                            ->button()
                            ->color('gray')
                            ->icon('heroicon-o-check-circle'),
                    ])
                    ->sendToDatabase($recipient);

                event(new DatabaseNotificationsSent($recipient));
            }
        }
    }

    /**
     * Handle the TrasladoSecundario "deleted" event.
     */
    public function deleted(TrasladoSecundario $trasladoSecundario): void
    {
        //
    }

    /**
     * Handle the TrasladoSecundario "restored" event.
     */
    public function restored(TrasladoSecundario $trasladoSecundario): void
    {
        //
    }

    /**
     * Handle the TrasladoSecundario "force deleted" event.
     */
    public function forceDeleted(TrasladoSecundario $trasladoSecundario): void
    {
        //
    }
}
