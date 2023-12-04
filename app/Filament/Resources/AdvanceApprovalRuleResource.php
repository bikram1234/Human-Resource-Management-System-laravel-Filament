<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdvanceApprovalRuleResource\Pages;
use App\Filament\Resources\AdvanceApprovalRuleResource\RelationManagers;
use App\Models\AdvanceApprovalRule;
use App\Models\AdvanceType;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdvanceApprovalRuleResource extends Resource
{
    protected static ?string $model = AdvanceApprovalRule::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Setting';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('For')
                ->required()
                ->maxLength(255)
                ->default("Advance")
                ->disabled(),
            Forms\Components\TextInput::make('RuleName')
                ->required()
                ->maxLength(255),
            Forms\Components\Select::make('type_id')
                ->options(
                    AdvanceType::all()->pluck('name', 'id')->toArray()
                )
                ->required(),
            Forms\Components\DatePicker::make('start_date')
                ->required(),
            Forms\Components\DatePicker::make('end_date'),
            Forms\Components\Toggle::make('status')
                ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('For'),
                Tables\Columns\TextColumn::make('RuleName'),
                Tables\Columns\TextColumn::make('type.name'),
                Tables\Columns\TextColumn::make('start_date')
                    ->date(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date(),
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
            RelationManagers\AdvanceApprovalConditionRelationManager::class,
            RelationManagers\AdvanceFormulaRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdvanceApprovalRules::route('/'),
            'create' => Pages\CreateAdvanceApprovalRule::route('/create'),
            'edit' => Pages\EditAdvanceApprovalRule::route('/{record}/edit'),
        ];
    }    
}
