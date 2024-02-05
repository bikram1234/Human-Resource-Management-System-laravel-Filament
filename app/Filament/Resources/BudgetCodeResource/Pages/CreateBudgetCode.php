<?php

namespace App\Filament\Resources\BudgetCodeResource\Pages;

use App\Filament\Resources\BudgetCodeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBudgetCode extends CreateRecord
{
    protected static string $resource = BudgetCodeResource::class;
    protected function getRedirectUrl(): string{
        return $this->getResource()::getUrl('index');
    }
}
