<?php

namespace App\Filament\Resources\FuelClaimResource\Pages;

use App\Filament\Resources\FuelClaimResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFuelClaim extends EditRecord
{
    protected static string $resource = FuelClaimResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function getRedirectUrl(): string{
        return $this->getResource()::getUrl('index');
    }
}
