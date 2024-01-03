<?php

namespace App\Filament\Resources\VehicleTypeResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VehicleNumberRelationManager extends RelationManager
{
    protected static string $relationship = 'VehicleNumber';

    protected static ?string $recordTitleAttribute = 'vehicle_type';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('vehicle_number')
                ->required()
                ->maxLength(255)
                ->required(),
                Forms\Components\TextInput::make('vehicle_mileage')
                    ->required(),
                Forms\Components\Toggle::make('status')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vehicletype.vehicle_type'),
                Tables\Columns\TextColumn::make('vehicle_number'),
                Tables\Columns\TextColumn::make('vehicle_mileage'),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }    
}
