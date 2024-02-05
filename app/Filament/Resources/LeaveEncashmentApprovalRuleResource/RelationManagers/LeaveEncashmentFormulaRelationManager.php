<?php

namespace App\Filament\Resources\LeaveEncashmentApprovalRuleResource\RelationManagers;

use App\Models\LeaveEncashmentFormula;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Chiiya\FilamentAccessControl\Models\FilamentUser;


class LeaveEncashmentFormulaRelationManager extends RelationManager
{
    protected static string $relationship = 'EncashmentFormulas';

    protected static ?string $recordTitleAttribute = 'condition';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Select::make('condition')
            ->options([
                'AND' => 'AND',
                'OR' => 'OR',
                'NOT' => 'NOT',
            ]),
            Forms\Components\Select::make('field')
            ->options([
                'User' => 'User',
                'No. of Days' => 'No. of Days',
            ])->required()->reactive(),
            Forms\Components\Select::make('operator')
            ->options([
                'Is' => 'Is',
                'Is Not' => 'Is Not',
                'Is Greater Than' => 'Is Greater Than',
                'Is Less Than' => 'Is Less Than',
                'Is Less Than or Equal To' => 'Is Less Than or Equal To',
                'Is Greater Than or Equal To' => 'Is Greater Than or Equal To',
                // Add other operator options as needed
            ])->required(),
            Forms\Components\Select::make('employee_id')->options(
                FilamentUser::all()->pluck('name', 'id')->toArray()
            )
                ->visible(function(callable $get){
                    if(in_array((string)$get('field'),["User"])){
                        return true;
                    }else{
                        return false;
                    }
                })
                ->required(function(callable $get){
                    if(in_array((string)$get('field'),["User"])){
                        return true;
                    }else{
                        return false;
                    }
                })
            ,     
            Forms\Components\TextInput::make('value')
            ->type('number')
            ->visible(function(callable $get){
                if(in_array((string)$get('field'),["No. of Days"])){
                    return true;
                }else{
                    return false;
                }
            })
            ->required(function(callable $get){
                if(in_array((string)$get('field'),["No. of Days"])){
                    return true;
                }else{
                    return false;
                }
            }),
        ]);
    }

    public function onSave($record, array $data)
    {
        parent::onSave($record, $data);

        // Build the new formula line based on the submitted form data
        $newFormulaLine = $this->buildFormulaLine($data);

        // Check if there's an existing formula for the approval_rule_id
        $existingFormula = LeaveEncashmentFormula::where('approval_rule_id', $data['approval_rule_id'])->first();

        // If an existing formula is found, concatenate the new formula line
        if ($existingFormula) {
            $existingFormula->formula_display .= " $newFormulaLine";
            $existingFormula->save();
        } else {
            // If no existing formula, create a new one
            LeaveEncashmentFormula::create([
                'approval_rule_id' => $data['approval_rule_id'],
                'formula_display' => $newFormulaLine,
            ]);
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('condition'),
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
}
