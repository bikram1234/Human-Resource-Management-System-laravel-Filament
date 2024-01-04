<?php

namespace App\Filament\Resources\AdvanceApprovalResource\Pages;

use App\Filament\Resources\AdvanceApprovalResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAdvanceApproval extends CreateRecord
{
    protected static string $resource = AdvanceApprovalResource::class;
    protected function getRedirectUrl(): string{
        return $this->getResource()::getUrl('index');
    }
}
