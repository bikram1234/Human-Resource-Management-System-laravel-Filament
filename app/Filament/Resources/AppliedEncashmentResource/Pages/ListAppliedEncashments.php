<?php

namespace App\Filament\Resources\AppliedEncashmentResource\Pages;

use App\Filament\Resources\AppliedEncashmentResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;


class ListAppliedEncashments extends ListRecords
{
    protected static string $resource = AppliedEncashmentResource::class;

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
