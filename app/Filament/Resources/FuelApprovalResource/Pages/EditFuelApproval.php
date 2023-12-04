<?php

namespace App\Filament\Resources\FuelApprovalResource\Pages;

use App\Filament\Resources\FuelApprovalResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFuelApproval extends EditRecord
{
    protected static string $resource = FuelApprovalResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
