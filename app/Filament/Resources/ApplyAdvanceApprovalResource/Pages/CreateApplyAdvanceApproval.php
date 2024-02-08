<?php

namespace App\Filament\Resources\ApplyAdvanceApprovalResource\Pages;

use App\Filament\Resources\ApplyAdvanceApprovalResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateApplyAdvanceApproval extends CreateRecord
{
    protected static string $resource = ApplyAdvanceApprovalResource::class;

    protected function getRedirectUrl(): string{
        return $this->getResource()::getUrl('index');
    }
}
