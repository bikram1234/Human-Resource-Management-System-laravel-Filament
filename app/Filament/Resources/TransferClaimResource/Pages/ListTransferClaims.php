<?php

namespace App\Filament\Resources\TransferClaimResource\Pages;

use App\Filament\Resources\TransferClaimResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListTransferClaims extends ListRecords
{
    protected static string $resource = TransferClaimResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    protected function getTableQuery(): Builder
    {
     $query = parent::getTableQuery()->whereuser_id(Auth::id());
     return $query;
    }
}
