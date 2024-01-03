<?php

namespace App\Filament\Resources\VehicleTypeResource\Pages;

use App\Filament\Resources\VehicleTypeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVehicleType extends EditRecord
{
    protected static string $resource = VehicleTypeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
