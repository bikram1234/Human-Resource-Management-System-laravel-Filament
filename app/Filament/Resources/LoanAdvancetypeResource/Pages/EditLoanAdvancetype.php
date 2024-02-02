<?php

namespace App\Filament\Resources\LoanAdvancetypeResource\Pages;

use App\Filament\Resources\LoanAdvancetypeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLoanAdvancetype extends EditRecord
{
    protected static string $resource = LoanAdvancetypeResource::class;

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
