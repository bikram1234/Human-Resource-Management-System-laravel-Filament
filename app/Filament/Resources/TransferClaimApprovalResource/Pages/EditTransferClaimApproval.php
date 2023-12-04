<?php

namespace App\Filament\Resources\TransferClaimApprovalResource\Pages;

use App\Filament\Resources\TransferClaimApprovalResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransferClaimApproval extends EditRecord
{
    protected static string $resource = TransferClaimApprovalResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
