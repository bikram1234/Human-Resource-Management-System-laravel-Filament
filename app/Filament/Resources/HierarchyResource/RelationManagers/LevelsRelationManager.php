<?php

namespace App\Filament\Resources\HierarchyResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\department;
use Chiiya\FilamentAccessControl\Models\FilamentUser;
use Filament\Forms\Get;
use Illuminate\Support\Facades\Log;




class LevelsRelationManager extends RelationManager
{
    protected static string $relationship = 'levels';

    protected static ?string $recordTitleAttribute = 'hierarchy_id';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Select::make('level')->options([
                1=> "Level1",
                2=> "Level2",
                3 => "Level3", 
            ])->required(),
            Forms\Components\Select::make('value')->options([
                //'IS' => "Immediate Supervisor",
                'SH'=> "Section Head",
                'DH'=> "Department Head",
                'MM' => "Management",
                'HR' => "Human Resource",
                'FH' => "Finance Head"
            ])->required()->reactive()->label('Verifier'), 
            Forms\Components\Select::make('emp_id')
            ->options(function ($get): array {
                $verifier = $get('value');
                if (!$verifier) {
                    Log::warning('No verifier value found');
                    return [];
                }
                switch ($verifier) {
                    case 'MM':
                        $departmentName = 'Management Information System';
                        break;
                    case 'HR':
                        $departmentName = 'Human Resource Management';
                        break;
                    case 'FH':
                        $departmentName = 'Finance';
                        break;
                    default:
                        $departmentName = null;
                }
        
                if ($departmentName !== null) {
                    $department = Department::where('name', $departmentName)->first();
                    if ($department) {
                        return FilamentUser::where('department_id', $department->id)->pluck('employee_display', 'id')->toArray();
                    }
                }
        
                Log::warning("No department found for verifier: {$verifier}");
                return [];
            })
            ->reactive()
            ->label("Employee")
            ->visible(function(callable $get){
                if(in_array((string)$get('value'),["MM", "HR", "FH"])){
                    return true;
                }else{
                    return false;
                }})
            ->required(function(callable $get){
                if(in_array((string)$get('value'),["MM", "HR", "FH"])){
                    return true;
                }else{
                    return false;
                    }
                }),            
            Forms\Components\DatePicker::make('start_date')
                ->required(),
            Forms\Components\DatePicker::make('end_date')
                ->required(),
            Forms\Components\Toggle::make('status')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('hierarchy.name'),
            Tables\Columns\TextColumn::make('employeeName.employee_display'),
            Tables\Columns\TextColumn::make('level'),
            Tables\Columns\TextColumn::make('value')->label('Verifier'),
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