<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppliedEncashmentResource\Pages;
use App\Filament\Resources\AppliedEncashmentResource\RelationManagers;
use App\Models\AppliedEncashment;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use App\Models\LeaveYearendProcess;
use App\Models\LeaveBalance;
use App\Models\LeavePolicy;
use App\Models\LeaveType;
 



class AppliedEncashmentResource extends Resource
{
    protected static ?string $model = AppliedEncashment::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Leave Encashment';
    protected static ?string $pluralModelLabel = 'Applied Encashment List';

    protected static ?string $navigationGroup = 'Encashment';
    protected static ?string $modelLabel = 'Encashment Request';
    protected static ?int $navigationSort = 2;
    public static function form(Form $form): Form
    {
        $currentUserId = Auth::id();
        $user_id = auth()->user()->id;
        $encash_balance = LeaveBalance::where('employee_id', $user_id)->first();
        // Dump and die to inspect the model instance
        //dd($encash_balance);
        
        if ($encash_balance) {
            $earned_leave_balance = $encash_balance->earned_leave_balance;
            //dd($earned_leave_balance);
            // Now $earned_leave_balance contains the earned leave balance for the employee
        
            // You can use $earned_leave_balance as needed
            echo "Earned Leave Balance: $earned_leave_balance";
        } else {
            // Handle the case where no record is found for the given employee ID
            echo "No leave balance found for the employee with ID: $user_id";
        }


        $leave = LeaveType::where('name', 'Earned Leave')->first();

        if ($leave) {
            $earnedLeaveId = $leave->id;
            //dd($earnedLeaveId);
        } else {
            // Handle the case where no LeaveType with the specified name is found
            echo "LeaveType with the name 'Earned Leave' not found.";
        }       
        if ($earnedLeaveId) {
            // Find the Policies associated with the ExpenseType
            $policies = LeavePolicy::where('leave_id', $earnedLeaveId)->get();
            //dd($policies);
            
            // Get policy IDs
            $policyIds = $policies->pluck('id')->toArray();
            
           // Find the LeaveYearendProcess with the same policy ID
            $leaveyear = LeaveYearendProcess::whereIn('policy_id', $policyIds)
            ->get();
            //dd($leaveyear);

            // Check if there are matching LeaveYearendProcess records
            if ($leaveyear->isEmpty()) {
            // Handle the case where records with the specified policy IDs don't exist
            $min_balance = 0;
            $max_balance = 0;
            } else {
            // You can choose how to handle multiple records here; for now, let's take the first one
            $leaveYearRecord = $leaveyear->first();

            // Access the min_balance and max_balance properties
            $min_balance = $leaveYearRecord->min_balance;
            $max_balance = $leaveYearRecord->max_balance;
            }
        } else {
            $earnedLeaveId = "no earnedLeaveId set"; // Handle the case where the DSA policy doesn't exist
        }
        
        
        return $form
            ->schema([

                Forms\Components\Hidden::make('user_id')
                ->default($currentUserId)
                ->required(),
                Forms\Components\Hidden::make('number_of_days')
                ->default(30)
                ->required(),
                Forms\Components\TextInput::make('date')
                ->type('date')
                ->default(now()->toDateString())  // Set default value to current date
                ->disabled()  // Make the field disabled
                ->required(),
                Forms\Components\TextInput::make('earned_leave_balance')
                ->default($earned_leave_balance)
                ->label("Total Leave For Encashment")
                ->disabled(),
                Forms\Components\TextInput::make('min_balance')
                ->default($min_balance)
                ->label("Leave Eligible For Encashment")
                ->disabled(),
                Forms\Components\TextInput::make('max_balance')
                ->default($max_balance)
                ->label("Leave Apply For Encashment")
                ->disabled(),
                Forms\Components\TextInput::make('amount')
                ->default(28000)
                ->label("Encashed Amount")
                ->disabled()
                ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name'),
                Tables\Columns\TextColumn::make('date')
                    ->date(),
                Tables\Columns\TextColumn::make('number_of_days'),
                Tables\Columns\TextColumn::make('amount'),
               
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListAppliedEncashments::route('/'),
            'create' => Pages\CreateAppliedEncashment::route('/create'),
            'edit' => Pages\EditAppliedEncashment::route('/{record}/edit'),
        ];
    }    
}
