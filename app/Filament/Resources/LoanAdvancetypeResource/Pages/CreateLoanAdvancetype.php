<?php

namespace App\Filament\Resources\LoanAdvancetypeResource\Pages;

use App\Filament\Resources\LoanAdvancetypeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLoanAdvancetype extends CreateRecord
{
    protected static string $resource = LoanAdvancetypeResource::class;
    
    protected function getRedirectUrl(): string{
        return $this->getResource()::getUrl('index');
    }
}
