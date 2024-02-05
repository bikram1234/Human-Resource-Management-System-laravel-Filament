<?php

namespace App\Filament\Resources\ApplyLoanAdvanceResource\Pages;

use App\Filament\Resources\ApplyLoanAdvanceResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApplyLoanAdvance extends EditRecord
{
    protected static string $resource = ApplyLoanAdvanceResource::class;

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
