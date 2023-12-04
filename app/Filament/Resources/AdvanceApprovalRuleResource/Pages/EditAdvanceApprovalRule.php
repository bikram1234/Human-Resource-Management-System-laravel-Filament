<?php

namespace App\Filament\Resources\AdvanceApprovalRuleResource\Pages;

use App\Filament\Resources\AdvanceApprovalRuleResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdvanceApprovalRule extends EditRecord
{
    protected static string $resource = AdvanceApprovalRuleResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
