<?php

namespace App\Filament\Resources\NodueResource\Pages;

use App\Filament\Resources\NodueResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNodue extends EditRecord
{
    protected static string $resource = NodueResource::class;

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
