<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BudgetCodeResource\Pages;
use App\Filament\Resources\BudgetCodeResource\RelationManagers;
use App\Models\BudgetCode;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BudgetCodeResource extends Resource
{
    protected static ?string $model = BudgetCode::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Loan For Advance';
    protected static ?string $navigationLabel = 'Budget Code';
    protected static ?string $pluralModelLabel = 'All Codes';
    protected static ?string $modelLabel = 'Codes';
    protected static ?int $navigationSort = 2;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('particular')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code'),
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('particular'),
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
            'index' => Pages\ListBudgetCodes::route('/'),
            'create' => Pages\CreateBudgetCode::route('/create'),
            'edit' => Pages\EditBudgetCode::route('/{record}/edit'),
        ];
    }    
}
