<?php

namespace App\Filament\Resources\ApplyAdvanceResource\Pages;

use App\Filament\Resources\ApplyAdvanceResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListApplyAdvances extends ListRecords
{
    protected static string $resource = ApplyAdvanceResource::class;

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
