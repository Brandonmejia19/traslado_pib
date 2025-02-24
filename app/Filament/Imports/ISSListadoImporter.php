<?php

namespace App\Filament\Imports;

use App\Models\ISSListado;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ISSListadoImporter extends Importer
{
    protected static ?string $model = ISSListado::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('nombre')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
        ];
    }

    public function resolveRecord(): ?ISSListado
    {
        // return ISSListado::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new ISSListado();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your i s s listado import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
