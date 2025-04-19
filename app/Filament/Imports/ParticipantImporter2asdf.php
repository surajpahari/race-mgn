<?php

namespace App\Filament\Imports;

use App\Models\Participant;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ParticipantImporter extends Importer
{
    protected static ?string $model = Participant::class;

    /* public function __construct(array $columnMap = []) */
    /* { */
    /*    parent::__construct($columnMap); */
    /* } */

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('fname')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('lname')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('email')
                ->requiredMapping()
                ->rules(['required', 'email', 'max:255']),
            ImportColumn::make('phone')->requiredMapping()
                ->rules(['max:15']),
            ImportColumn::make('dob')
                ->requiredMapping()
                ->rules(['required', 'date']),
            ImportColumn::make('gender')->requiredMapping()->rules(['required']),
            ImportColumn::make('race_id')
                ->numeric(),
            ImportColumn::make('age_group_id')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
        ];
    }

    /* public function resolveRecord(): ?Participant {} */

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your participant import has completed and '.number_format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
