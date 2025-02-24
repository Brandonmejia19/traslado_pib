<?php

namespace App\Filament\Resources\TrasladoSecundarioPropiosResource\Pages;

use App\Filament\Resources\TrasladoSecundarioPropiosResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrasladoSecundarioPropios extends EditRecord
{
    protected static string $resource = TrasladoSecundarioPropiosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
