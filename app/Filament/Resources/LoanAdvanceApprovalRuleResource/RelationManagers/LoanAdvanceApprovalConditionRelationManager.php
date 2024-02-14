<?php

namespace App\Filament\Resources\LoanAdvanceApprovalRuleResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Chiiya\FilamentAccessControl\Models\FilamentUser;
use App\Models\Hierarchy;
use App\Models\Level;
use App\Models\LoanAdvanceFormula;

class LoanAdvanceApprovalConditionRelationManager extends RelationManager
{
    protected static string $relationship = 'approvalConditions';

    protected static ?string $recordTitleAttribute = 'approval_type';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('approval_type')->options([
                    "Hierarchy" => "Hierarchy",
                    "Single User" => "Single User" ,
                    "Auto Approval" => "Auto Approval" 
                   ])->required()->reactive(),
       
                   Forms\Components\Select::make('hierarchy_id')->options(
                       Hierarchy::all()->pluck('name', 'id')->toArray()
                   )->label("Hierarchy")
                    ->default(Hierarchy::first()->id)
                   ->visible(function(callable $get){
                       if(in_array((string)$get('approval_type'),["Hierarchy"])){
                           return true;
                       }else{
                           return false;
                       }
                   })->required(function(callable $get){
                       if(in_array((string)$get('approval_type'),["Hierarchy"])){
                           return true;
                       }else{
                           return false;
                       }
                   })->reactive(),
                   Forms\Components\Select::make('MaxLevel')->options(function (callable $get) {
                                   // Get the selected hierarchy_id
                       $hierarchyId = $get('hierarchy_id');
       
                       // Fetch levels based on hierarchyId (replace this with your actual logic)
                       $levels = Level::where('hierarchy_id', $hierarchyId)->pluck('level', 'id')->toArray();
       
                       // Add an empty option
                       $options = [];
       
                       // Prepend "Level" to each option label and store the option itself
                       foreach ($levels as $id => $level) {
                           $options['Level' . $level] = 'Level' . $level;
                       }
       
                       return $options;
                   })
                   ->visible(function(callable $get){
                       if(in_array((string)$get('approval_type'),["Hierarchy"])){
                           return true;
                       }else{
                           return false;
                       }
                   })->required(function(callable $get){
                       if(in_array((string)$get('approval_type'),["Hierarchy"])){
                           return true;
                       }else{
                           return false;
                       }
                   }),
         
                   Forms\Components\Select::make('employee_id')->options(
                       FilamentUser::all()->pluck('name', 'id')->toArray()
                   )
                   ->visible(function(callable $get){
                       if(in_array((string)$get('approval_type'),["Single User"])){
                           return true;
                       }else{
                           return false;
                       }
                   })->required(function(callable $get){
                       if(in_array((string)$get('approval_type'),["Single User"])){
                           return true;
                       }else{
                           return false;
                       }
                   })
                   ,
                  
               ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('approval_type'),
            ])
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
    public static function getRelations(): array
    {
        return [
            LoanAdvanceFormulaRelationManager::class,
        ];
    }     
}
