<?php

namespace App\Filament\Resources\AmbulanciasResource\Pages;

use App\Filament\Resources\AmbulanciasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Imports\AmbulanciasImporter;
use Filament\Actions\ImportAction;
use App\Filament\Resources\AmbulanciasResource\Widgets\AdvancedStatsOverviewWidget;
class ListAmbulancias extends ListRecords
{
    protected static string $resource = AmbulanciasResource::class;
    protected function getHeaderWidgets(): array
    {
        return [
            AdvancedStatsOverviewWidget::class,
        ];
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ImportAction::make()
            ->importer(AmbulanciasImporter::class)
        ];
    }
}
