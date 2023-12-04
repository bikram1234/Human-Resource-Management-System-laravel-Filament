<?php

namespace App\Filament\Resources\AdvanceApprovalRuleResource\Pages;

use App\Filament\Resources\AdvanceApprovalRuleResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdvanceApprovalRules extends ListRecords
{
    protected static string $resource = AdvanceApprovalRuleResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
