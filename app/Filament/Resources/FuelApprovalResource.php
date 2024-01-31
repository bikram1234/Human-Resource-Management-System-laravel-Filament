<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FuelApprovalResource\Pages;
use App\Filament\Resources\FuelApprovalResource\RelationManagers;
use App\Models\FuelApproval;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;
use App\Mail\ExpenseApprovedMail;
use App\Mail\ExpenseApplicationMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Level;
use Chiiya\FilamentAccessControl\Models\FilamentUser;
use App\Models\ExpenseApprovalCondition;
use App\Models\ExpenseApprovalRule;
use App\Models\Fuel;
use App\Models\FuelClaim;
use Filament\Tables\Actions\Action;


class FuelApprovalResource extends Resource
{
    protected static ?string $model = FuelApproval::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-circle';
    protected static ?string $navigationGroup = 'Expense';
    protected static ?string $navigationLabel = 'Fuel Approval';

    protected static ?string $pluralModelLabel = 'Fuel Approval List';


    protected static ?int $navigationSort = 8;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('level1')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('level2')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('level3')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('remark')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('FuelApply.user.name'),
                Tables\Columns\TextColumn::make('FuelApply.application_date')
                ->label("Application date"),
                Tables\Columns\TextColumn::make('FuelApply.location')
                ->label("Location"),
                Tables\Columns\TextColumn::make('FuelApply.vehicletype.vehicle_type')
                ->label("Vehicle"),
                Tables\Columns\TextColumn::make('FuelApply.vehicle.vehicle_mileage')
                ->label("Mileage"),
                Tables\Columns\TextColumn::make('FuelApply.status')
                ->label("Status"),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Action::make('Approve')
                ->action(fn (FuelApproval $record) => FuelApprovalResource::ApproveExpense($record))
                ->requiresConfirmation()
                ->modalHeading('Approve')
                ->modalSubheading('Are you sure you\'d like to approve? This cannot be undone.')
                ->modalButton('Yes, approve now')
                ->color('success')
                ->hidden(function ( FuelApproval $record) {
                    return $record->FuelApply->status === "approved";
                }),
                //Action::make('Reject')
                //->action(fn (FuelApproval $record) => FuelApprovalResource::RejectExpense($record))
                Action::make('Reject')
                ->form([
                    Forms\Components\Textarea::make('remark')
                        ->placeholder('Enter Remark (Required)')
                        ->rows(3)
                        ->required(),
                ])
                ->requiresConfirmation()
                ->modalHeading('Reject')
                ->modalSubheading('Are you sure you\'d like to reject? This cannot be undone.')
                ->modalButton('Yes, reject now')
                ->color('danger')
                ->hidden(function ( FuelApproval $record) {
                    return $record->FuelApply->status === "approved";
                })
                ->action(function (FuelApproval $record, array $data) {
                    $remark = $data['remark'];
                    FuelApprovalResource::RejectExpense($record, $remark);
                })            
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
    // public function widgets(): array
    // {
    //     return [
    //         // Other widgets...
    //         LatestApproval::class,
    //     ];
    // }
    
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFuelApprovals::route('/'),
            'create' => Pages\CreateFuelApproval::route('/create'),
        ];
    }  
    public static function ApproveExpense($record) {
        $id = $record->applied_expense_id;
        $ExpenseApplication = FuelClaim::findOrFail($id);
        $expense_id = $ExpenseApplication->expense_type_id;
        $userID = $ExpenseApplication->user_id;
 
        $user = FilamentUser::where('id', $userID)->first();
        $Approvalrecipient = $user->email;

        $approvalRuleId = ExpenseApprovalRule::where('type_id', $expense_id)->value('id');
       
        $approvalType = ExpenseApprovalCondition::where('approval_rule_id', $approvalRuleId)->first();

        $hierarchy_id = $approvalType->hierarchy_id;

        $leaveApplication = FuelClaim::findOrFail($id);
        $departmentId = $user->department_id;
        $departmentHead =FilamentUser::where('section_id', $departmentId)
        ->whereHas('roles', fn ($query) => $query->where('name', 'Department Head'))
        ->first();

        if($approvalType->approval_type === "Hierarchy"){

            if ($leaveApplication->FuelApproval->level1 === 'pending' && $approvalType->MaxLevel === 'Level1') {
                $leaveApplication->FuelApproval->update([
                    'level1' => 'approved',
                ]);
        
                // Update the AppliedLeave model fields
                $leaveApplication->update([
                    'status' => 'approved',
                ]);
             
                $content = "Fuel Claim applied for has been approved.";
            
                Mail::to($Approvalrecipient)->send(new ExpenseApprovedMail($user, $content));
            
                Notification::make() 
                ->title('Fuel Claim Approved successfully')
                ->success()
                ->send();
            
            }else if (
                $leaveApplication->FuelApproval->level1 === 'pending' &&
                $leaveApplication->FuelApproval->level2 === 'pending' &&
                ($approvalType->MaxLevel === 'Level2' || $approvalType->MaxLevel === 'Level3')
            ) { 
                $leaveApplication->FuelApproval->update([
                    'level1' => 'approved'
                ]);
                $levelRecord = Level::where('hierarchy_id', $hierarchy_id)
                ->where('level', 2)
                ->first();
    
                if ($levelRecord) {
                    // Access the 'value' field from the level record
                    $levelValue = $levelRecord->value;
    
                    // Determine the recipient based on the levelValue
                    $recipient = '';
    
                    // Check the levelValue and set the recipient accordingly
                    if ($levelValue === "DH") {
                        // Set the recipient to the section head's email address or user ID
                        $recipient = $departmentHead->email; // Replace with the actual field name
                        $approval = $departmentHead;
                        $currentUser = $user;
        
                        Mail::to($recipient)->send(new ExpenseApplicationMail($approval, $currentUser));
                        Notification::make() 
                        ->title('Fuel Claim Approved successfully')
                        ->success()
                        ->send();
                    }else{
                        // Access the 'value' field from the level record
                        $levelValue = $levelRecord->value;
                        $userID = $levelRecord->emp_id;
                        $approval = FilamentUser::where('id', $userID)->first();
                        // Determine the recipient based on the levelValue
                        $recipient = $approval->email;
        
                        $currentUser = $user;
        
                        Mail::to($recipient)->send(new ExpenseApplicationMail($approval, $currentUser));  
                    }
                  
                
                }
    
            }else if($leaveApplication->FuelApproval->level1==='approved' && $approvalType->MaxLevel === 'Level2') {
                $leaveApplication->FuelApproval->update([
                    'level1' => 'approved',
                ]);
        
                // Update the AppliedLeave model fields
                $leaveApplication->update([
                    'status' => 'approved',
                ]);
    
                $content = "Fuel Claim applied for has been approved.";
                Mail::to($Approvalrecipient)->send(new ExpenseApprovedMail($user, $content));
            
                Notification::make() 
                ->title('Fuel Claim Approved successfully')
                ->success()
                ->send();

            } else if($leaveApplication->FuelApproval->level1==='approved' &&$leaveApplication->FuelApproval->level2==='pending' && $approvalType->MaxLevel === 'Level3') {
                $leaveApplication->FuelApproval->update([
                    'level2' => 'approved'
                ]);
                $levelRecord = Level::where('hierarchy_id', $hierarchy_id)
                ->where('level', 3)
                ->first();
    
                if ($levelRecord) {
                    // Access the 'value' field from the level record
                    $levelValue = $levelRecord->value;
                    $userID = $levelRecord->emp_id;
                    $approval = FilamentUser::where('id', $userID)->first();
                    // Determine the recipient based on the levelValue
                    $recipient = $approval->email;
    
                    $currentUser = $user;
    
                    Mail::to($recipient)->send(new ExpenseApplicationMail($approval, $currentUser));
  
                }
            } else if($leaveApplication->FuelApproval->level1==='approved' &&$leaveApplication->FuelApproval->level2==='approved' && $leaveApplication->FuelApproval->level3==="pending" && $approvalType->MaxLevel === 'Level3') {

                $leaveApplication->FuelApproval->update([
                    'level3' => 'approved',
                ]);
        
                // Update the AppliedLeave model fields
                $leaveApplication->update([
                    'status' => 'approved',
                ]);

                $content = "Fuel Cliam applied for has been approved.";
                
                Mail::to($Approvalrecipient)->send(new ExpenseApprovedMail($user, $content));
            
                // Redirect back with a success message
                return redirect()->back()->with('success', 'Fuel application approved successfully.');
            } else {
                // Handle cases where the leave application cannot be approved (e.g., it's not at the expected level or already approved)
                return redirect()->back()->with('error', 'Fuel application cannot be approved.');
            }
        }else if($approvalType->approval_type === "Single User"){

            $leaveApplication->leaveApproval->update([
                'level1' => 'approved',
                'level2' => 'approved',
                'level3' => 'approved',
                
            ]);
             // Update the AppliedLeave model fields
             $leaveApplication->update([
                'status' => 'approved',
            ]);
            $content = "Fuel Claim applied for has been approved.";
        
            Mail::to($Approvalrecipient)->send(new ExpenseApprovedMail($user, $content));
        
            Notification::make() 
            ->title('Fuel Claim Approved successfully')
            ->success()
            ->send();
        }
       

    } 
    public static function RejectExpense($record, $remark) {
        $id = $record->applied_expense_id;
        $ExpenseApplication = FuelClaim::findOrFail($id);
        $expense_id = $ExpenseApplication->expense_type_id;

        $userID = $ExpenseApplication->user_id;
 
        $user = FilamentUser::where('id', $userID)->first();
        $Approvalrecipient = $user->email;

        $approvalRuleId = ExpenseApprovalRule::where('type_id', $expense_id)->value('id');
       
        $approvalType = ExpenseApprovalCondition::where('approval_rule_id', $approvalRuleId)->first();

        $hierarchy_id = $approvalType->hierarchy_id;

        $leaveApplication = FuelClaim::findOrFail($id);
      

                $leaveApplication->FuelApproval->update([
                    'level1' => 'rejected',
                    'level2' => 'rejected',
                    'level3' => 'rejected',
                    'remark' => $remark
                    
                ]);
                // Update the fuelClaim model fields
                $leaveApplication->update([
                    'status' => 'rejected',
                    'remark' => $remark
                ]);

                $content = "Fuel you have applied for has been rejected.";
                
                Mail::to($Approvalrecipient)->send(new ExpenseApprovedMail($user, $content));
            
           
        if($approvalType->approval_type === "Single User"){
             // Update the AppliedLeave model fields
             $leaveApplication->update([
                'status' => 'rejected',
                'remark' => $remark
            ]);
            $content = "Fuel have applied for has been rejected.";
        
            Mail::to($Approvalrecipient)->send(new ExpenseApprovedMail($user, $content));
        
            Notification::make() 
            ->title('Fuel expense rejected successfully')
            ->success()
            ->send();
        }
       

    } 
}
