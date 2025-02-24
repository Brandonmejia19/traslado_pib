<?php

namespace App\Filament\Resources\AmbulanciasResource\Pages;

use App\Filament\Resources\AmbulanciasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAmbulancias extends EditRecord
{
    protected static string $resource = AmbulanciasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
