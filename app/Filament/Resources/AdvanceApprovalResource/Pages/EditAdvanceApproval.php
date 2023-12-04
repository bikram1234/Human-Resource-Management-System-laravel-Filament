<?php

namespace App\Filament\Resources\AdvanceApprovalResource\Pages;

use App\Filament\Resources\AdvanceApprovalResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdvanceApproval extends EditRecord
{
    protected static string $resource = AdvanceApprovalResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
