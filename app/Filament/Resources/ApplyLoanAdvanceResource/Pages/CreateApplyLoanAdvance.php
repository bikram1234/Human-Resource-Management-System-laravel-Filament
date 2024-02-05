<?php

namespace App\Filament\Resources\ApplyLoanAdvanceResource\Pages;

use App\Filament\Resources\ApplyLoanAdvanceResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateApplyLoanAdvance extends CreateRecord
{
    protected static string $resource = ApplyLoanAdvanceResource::class;

    protected function getRedirectUrl(): string{
        return $this->getResource()::getUrl('index');
    }
}
