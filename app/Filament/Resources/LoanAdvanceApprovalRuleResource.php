<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanAdvanceApprovalRuleResource\Pages;
use App\Filament\Resources\LoanAdvanceApprovalRuleResource\RelationManagers;
use App\Models\LoanAdvanceApprovalCondition;
use App\Models\LoanAdvanceApprovalRule;
use App\Models\LoanAdvanceFormula;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\LoanAdvancetype;


class LoanAdvanceApprovalRuleResource extends Resource
{
    protected static ?string $model = LoanAdvanceApprovalRule::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Setting';
    protected static ?string $navigationLabel = 'Loan Approval Rules';
    protected static ?string $pluralModelLabel = 'Rule List';
    protected static ?string $modelLabel = 'Rules';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('For')
                ->required()
                ->maxLength(255)
                ->default("Loan Advance")
                ->disabled(),
            Forms\Components\TextInput::make('RuleName')
                ->required()
                ->maxLength(255),
            Forms\Components\Select::make('type_id')
                ->options(
                    LoanAdvancetype::all()->pluck('name', 'id')->toArray()
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
            RelationManagers\LoanAdvanceApprovalConditionRelationManager::class,
            RelationManagers\LoanAdvanceFormulaRelationManager::class

        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLoanAdvanceApprovalRules::route('/'),
            'create' => Pages\CreateLoanAdvanceApprovalRule::route('/create'),
            'edit' => Pages\EditLoanAdvanceApprovalRule::route('/{record}/edit'),
        ];
    }    
}
