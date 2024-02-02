<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanAdvancetypeResource\Pages;
use App\Filament\Resources\LoanAdvancetypeResource\RelationManagers;
use App\Models\LoanAdvancetype;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LoanAdvancetypeResource extends Resource
{
    protected static ?string $model = LoanAdvancetype::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Loan For Advance';
    protected static ?string $navigationLabel = 'Types';
    protected static ?string $pluralModelLabel = 'All Types';
    protected static ?string $modelLabel = 'Types';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('condition')
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
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('condition'),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime(),
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
            'index' => Pages\ListLoanAdvancetypes::route('/'),
            'create' => Pages\CreateLoanAdvancetype::route('/create'),
            'edit' => Pages\EditLoanAdvancetype::route('/{record}/edit'),
        ];
    }    
}
