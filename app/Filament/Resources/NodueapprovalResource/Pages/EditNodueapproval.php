<?php

namespace App\Filament\Resources\NodueapprovalResource\Pages;

use App\Filament\Resources\NodueapprovalResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNodueapproval extends EditRecord
{
    protected static string $resource = NodueapprovalResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
