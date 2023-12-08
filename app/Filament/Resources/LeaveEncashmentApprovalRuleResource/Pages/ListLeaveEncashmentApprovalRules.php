<?php

namespace App\Filament\Resources\LeaveEncashmentApprovalRuleResource\Pages;

use App\Filament\Resources\LeaveEncashmentApprovalRuleResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLeaveEncashmentApprovalRules extends ListRecords
{
    protected static string $resource = LeaveEncashmentApprovalRuleResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
