<?php

namespace App\Observers;

use App\Models\TrasladoSecundarioPropios;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use App\Models\User;
class TrasladosPropiosObserver
{
    /**
     * Handle the TrasladoSecundarioPropios "created" event.
     */
    public function created(TrasladoSecundarioPropios $trasladoSecundarioPropios): void
    {
        // OBTENER SOLO USUARIOS CON SESIÓN ACTIVA
        $recipients = User::role(['Administrador', 'Médico', 'Gestor'])->get();

        foreach ($recipients as $recipient) {
            Notification::make()
                ->title('Nuevo traslado secundario registrado')
                ->icon('healthicons-o-ambulance')
                ->iconColor('primary')
                ->warning()
                ->body("
                    📌 Correlativo: {$trasladoSecundarioPropios->correlativo} /" . "\n" . "
                    👤 Generado por: {$trasladoSecundarioPropios->operador_nombre} /" . "\n" . "
                    👤 PP: {$trasladoSecundarioPropios->operador_numero} /" . "\n" . "
                    🏥 Diagnóstico: {$trasladoSecundarioPropios->diagnostico_paciente}
                ")
                ->actions([
                    Action::make('Marcar como leído')
                        ->markAsRead()
                        ->button()
                        ->color('warning')
                        ->icon('heroicon-o-check-circle')
                        ->color('gray'),
                ])
                ->sendToDatabase($recipient);

            event(new DatabaseNotificationsSent($recipient));
        }
    }

    /**
     * Handle the TrasladoSecundarioPropios "updated" event.
     */
    public function updated(TrasladoSecundarioPropios $trasladoSecundarioPropios): void
    {
        //
    }

    /**
     * Handle the TrasladoSecundarioPropios "deleted" event.
     */
    public function deleted(TrasladoSecundarioPropios $trasladoSecundarioPropios): void
    {
        //
    }

    /**
     * Handle the TrasladoSecundarioPropios "restored" event.
     */
    public function restored(TrasladoSecundarioPropios $trasladoSecundarioPropios): void
    {
        //
    }

    /**
     * Handle the TrasladoSecundarioPropios "force deleted" event.
     */
    public function forceDeleted(TrasladoSecundarioPropios $trasladoSecundarioPropios): void
    {
        //
    }
}
