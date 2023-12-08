<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaveEncashmentApprovalRuleResource\Pages;
use App\Filament\Resources\LeaveEncashmentApprovalRuleResource\RelationManagers;
use App\Models\LeaveEncashmentApprovalRule;
use App\Models\encashment;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LeaveEncashmentApprovalRuleResource extends Resource
{
    protected static ?string $model = LeaveEncashmentApprovalRule::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationGroup = 'Setting';

    protected static ?string $navigationLabel = 'Leave Encashment Rule';



    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\TextInput::make('For')
                ->required()
                ->maxLength(255)
                ->default("Leave Encashment")
                ->disabled(),
            Forms\Components\TextInput::make('RuleName')
                ->required()
                ->maxLength(255),
            Forms\Components\Select::make('type_id')
            ->options(
                encashment::all()->pluck('name', 'id')->toArray()
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
                Tables\Columns\TextColumn::make('type.name'),
                Tables\Columns\TextColumn::make('RuleName'),
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
             RelationManagers\LeaveEncashmentApprovalConditionRelationManager::class,
             RelationManagers\LeaveEncashmentFormulaRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaveEncashmentApprovalRules::route('/'),
            'create' => Pages\CreateLeaveEncashmentApprovalRule::route('/create'),
            'edit' => Pages\EditLeaveEncashmentApprovalRule::route('/{record}/edit'),
        ];
    }    
}
