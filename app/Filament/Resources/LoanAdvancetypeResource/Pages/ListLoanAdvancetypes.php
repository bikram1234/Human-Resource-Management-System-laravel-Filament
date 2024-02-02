<?php

namespace App\Filament\Resources\LoanAdvancetypeResource\Pages;

use App\Filament\Resources\LoanAdvancetypeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLoanAdvancetypes extends ListRecords
{
    protected static string $resource = LoanAdvancetypeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
