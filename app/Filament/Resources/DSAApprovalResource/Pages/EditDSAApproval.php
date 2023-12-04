<?php

namespace App\Filament\Resources\DSAApprovalResource\Pages;

use App\Filament\Resources\DSAApprovalResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDSAApproval extends EditRecord
{
    protected static string $resource = DSAApprovalResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
