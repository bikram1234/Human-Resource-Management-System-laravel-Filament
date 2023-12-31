<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TimeZoneResource\Pages;
use App\Filament\Resources\TimeZoneResource\RelationManagers;
use App\Models\TimeZone;
use App\Models\Country;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TimeZoneResource extends Resource
{
    protected static ?string $model = TimeZone::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Work-Structure';

    protected static ?string $navigationLabel = 'Time Zone';
    protected static ?string $pluralModelLabel = 'Time Zone';

    protected static ?string $modelLabel = 'Time Zone';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('country_id')
                ->options(
                    Country::all()->pluck('name', 'id')->toArray()
                )
                ->label("Country")
                ->required(),
                Forms\Components\TextInput::make('timezone')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('status')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('country.name'),
                Tables\Columns\TextColumn::make('timezone'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTimeZones::route('/'),
            'create' => Pages\CreateTimeZone::route('/create'),
            'edit' => Pages\EditTimeZone::route('/{record}/edit'),
        ];
    }    
}
