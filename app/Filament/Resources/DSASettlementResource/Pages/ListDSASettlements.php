<?php

namespace App\Filament\Resources\DSASettlementResource\Pages;

use App\Filament\Resources\DSASettlementResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListDSASettlements extends ListRecords
{
    protected static string $resource = DSASettlementResource::class;

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
