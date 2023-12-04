<?php

namespace App\Filament\Resources\FuelResource\Pages;

use App\Filament\Resources\FuelResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListFuels extends ListRecords
{
    protected static string $resource = FuelResource::class;

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
