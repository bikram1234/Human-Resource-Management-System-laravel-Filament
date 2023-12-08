<?php

namespace App\Filament\Resources\LeaveEncashmentApprovalRuleResource\Pages;

use App\Filament\Resources\LeaveEncashmentApprovalRuleResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLeaveEncashmentApprovalRule extends EditRecord
{
    protected static string $resource = LeaveEncashmentApprovalRuleResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
