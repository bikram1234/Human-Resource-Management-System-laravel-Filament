<?php

namespace App\Filament\Resources\AdvanceSettlementResource\Pages;

use App\Filament\Resources\AdvanceSettlementResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdvanceSettlements extends ListRecords
{
    protected static string $resource = AdvanceSettlementResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
