<?php

namespace App\Filament\Resources\VehicleTypeResource\Pages;

use App\Filament\Resources\VehicleTypeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVehicleTypes extends ListRecords
{
    protected static string $resource = VehicleTypeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
