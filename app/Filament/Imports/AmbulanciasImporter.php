<?php

namespace App\Filament\Imports;

use App\Models\Ambulancias;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class AmbulanciasImporter extends Importer
{
    protected static ?string $model = Ambulancias::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('unidad')
                ->requiredMapping()
                ->rules(['required', 'max:64']),
            ImportColumn::make('placa')
                ->requiredMapping()
                ->rules(['required', 'max:64']),
        ];
    }

    public function resolveRecord(): ?Ambulancias
    {
        // return Ambulancias::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Ambulancias();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your ambulancias import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
