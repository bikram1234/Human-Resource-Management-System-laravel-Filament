<?php

namespace App\Filament\Resources\FuelClaimResource\Pages;

use App\Filament\Resources\FuelClaimResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListFuelClaims extends ListRecords
{
    protected static string $resource = FuelClaimResource::class;

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
