<?php

namespace App\Filament\Resources\BudgetCodeResource\Pages;

use App\Filament\Resources\BudgetCodeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBudgetCode extends EditRecord
{
    protected static string $resource = BudgetCodeResource::class;

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
