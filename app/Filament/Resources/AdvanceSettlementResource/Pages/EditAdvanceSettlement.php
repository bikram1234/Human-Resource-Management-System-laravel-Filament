<?php

namespace App\Filament\Resources\AdvanceSettlementResource\Pages;

use App\Filament\Resources\AdvanceSettlementResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdvanceSettlement extends EditRecord
{
    protected static string $resource = AdvanceSettlementResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
