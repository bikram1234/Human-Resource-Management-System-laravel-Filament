<?php

namespace App\Filament\Resources\ApplyAdvanceApprovalResource\Pages;

use App\Filament\Resources\ApplyAdvanceApprovalResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApplyAdvanceApproval extends EditRecord
{
    protected static string $resource = ApplyAdvanceApprovalResource::class;

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
