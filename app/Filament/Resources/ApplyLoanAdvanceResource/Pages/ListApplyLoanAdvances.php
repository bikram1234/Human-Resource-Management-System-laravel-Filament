<?php

namespace App\Filament\Resources\ApplyLoanAdvanceResource\Pages;

use App\Filament\Resources\ApplyLoanAdvanceResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApplyLoanAdvances extends ListRecords
{
    protected static string $resource = ApplyLoanAdvanceResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
