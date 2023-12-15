<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseApprovalResource\Pages;
use App\Filament\Resources\ExpenseApprovalResource\RelationManagers;
use App\Models\ExpenseApplication;
use App\Models\ExpenseApproval;
use App\Models\ExpenseApprovalCondition;
use App\Models\ExpenseApprovalRule;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;
use App\Mail\ExpenseApprovedMail;
use App\Mail\ExpenseApplicationMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Level;
use App\Models\MasEmployee;


class ExpenseApprovalResource extends Resource
{
    protected static ?string $model = ExpenseApproval::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-circle';
    protected static ?string $navigationGroup = 'Expense';
    protected static ?string $navigationLabel = 'Expense Approval';


    protected static ?string $pluralModelLabel = 'Expense Approval List';

    protected static ?int $navigationSort = 4;



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\TextInput::make('appliedLeave.leavetype.name')
                //     ->required()
                //     ->maxLength(36)
                //     ->label('Leave Type'),
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
                Tables\Columns\TextColumn::make('ExpenseApply.user.name'),
                Tables\Columns\TextColumn::make('ExpenseApply.expenseType.name'),
                Tables\Columns\TextColumn::make('ExpenseApply.application_date')
                ->label("Application date"),
                Tables\Columns\TextColumn::make('ExpenseApply.description')
                ->label("Description"),
                Tables\Columns\TextColumn::make('ExpenseApply.attachment')
                ->label("Attachment"),
                Tables\Columns\TextColumn::make('ExpenseApply.status')
                ->label("Status"),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Action::make('Approve')
                ->action(fn (ExpenseApproval $record) => ExpenseApprovalResource::ApproveExpense($record))
                ->requiresConfirmation()
                ->modalHeading('Approve')
                ->modalSubheading('Are you sure you\'d like to approve? This cannot be undone.')
                ->modalButton('Yes, approve now')
                ->color('success')
                ->hidden(function ( ExpenseApproval $record) {
                    return $record->ExpenseApply->status === "approved";
                }),    
                Action::make('Reject')
                ->action(fn (ExpenseApproval $record) => ExpenseApprovalResource::RejectExpense($record))
                ->requiresConfirmation()
                ->modalHeading('Reject')
                ->modalSubheading('Are you sure you\'d like to reject? This cannot be undone.')
                ->modalButton('Yes, reject now') 
                ->color('danger')
                ->hidden(function ( ExpenseApproval $record) {
                    return $record->ExpenseApply->status === "approved";
                }),           
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
            'index' => Pages\ListExpenseApprovals::route('/'),
            'create' => Pages\CreateExpenseApproval::route('/create'),
        ];
    } 
    
    public static function ApproveExpense($record) {
        $id = $record->applied_expense_id;
        $ExpenseApplication = ExpenseApplication::findOrFail($id);
        $expense_id = $ExpenseApplication->expense_type_id;
        $userID = $ExpenseApplication->user_id;
 
        $user = MasEmployee::where('id', $userID)->first();
        $Approvalrecipient = $user->email;

        $approvalRuleId = ExpenseApprovalRule::where('type_id', $expense_id)->value('id');
       
        $approvalType = ExpenseApprovalCondition::where('approval_rule_id', $approvalRuleId)->first();

        $hierarchy_id = $approvalType->hierarchy_id;

        $leaveApplication = ExpenseApplication::findOrFail($id);
        $departmentId = $user->department_id;
        $departmentHead =MasEmployee::where('section_id', $departmentId)
        ->whereHas('roles', fn ($query) => $query->where('name', 'Department Head'))
        ->first();

        if($approvalType->approval_type === "Hierarchy"){

            if ($leaveApplication->ExpenseApproval->level1 === 'pending' && $approvalType->MaxLevel === 'Level1') {
                $leaveApplication->ExpenseApproval->update([
                    'level1' => 'approved',
                ]);
        
                // Update the AppliedLeave model fields
                $leaveApplication->update([
                    'status' => 'approved',
                ]);
             
                $content = "The Expense you have applied for has been approved.";
            
                Mail::to($Approvalrecipient)->send(new ExpenseApprovedMail($user, $content));
            
                Notification::make() 
                ->title('Expense Approved successfully')
                ->success()
                ->send();
            
            }else if (
                $leaveApplication->ExpenseApproval->level1 === 'pending' &&
                $leaveApplication->ExpenseApproval->level2 === 'pending' &&
                ($approvalType->MaxLevel === 'Level2' || $approvalType->MaxLevel === 'Level3')
            ) { 
                $leaveApplication->ExpenseApproval->update([
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
                    }
                    $approval = $departmentHead;
                    $currentUser = $user;
    
                    Mail::to($recipient)->send(new ExpenseApplicationMail($approval, $currentUser));
                    Notification::make() 
                    ->title('Expense Approved successfully')
                    ->success()
                    ->send();
                
                }
    
            }else if($leaveApplication->ExpenseApproval->level1==='approved' && $approvalType->MaxLevel === 'Level2') {
                $leaveApplication->ExpenseApproval->update([
                    'level1' => 'approved',
                ]);
        
                // Update the AppliedLeave model fields
                $leaveApplication->update([
                    'status' => 'approved',
                ]);
    
                $content = "The Expense you have applied for has been approved.";
                Mail::to($Approvalrecipient)->send(new ExpenseApprovedMail($user, $content));
            
                Notification::make() 
                ->title('Expense Approved successfully')
                ->success()
                ->send();

            } else if($leaveApplication->ExpenseApproval->level1==='approved' &&$leaveApplication->ExpenseApproval->level2==='pending' && $approvalType->MaxLevel === 'Level3') {
                $leaveApplication->ExpenseApproval->update([
                    'level2' => 'approved'
                ]);
                $levelRecord = Level::where('hierarchy_id', $hierarchy_id)
                ->where('level', 3)
                ->first();
    
                if ($levelRecord) {
                    // Access the 'value' field from the level record
                    $levelValue = $levelRecord->value;
                    $userID = $levelRecord->emp_id;
                    $approval = MasEmployee::where('id', $userID)->first();
                    // Determine the recipient based on the levelValue
                    $recipient = $approval->email;
    
                    $currentUser = $user;
    
                    Mail::to($recipient)->send(new ExpenseApplicationMail($approval, $currentUser));
  
                }
            } else if($leaveApplication->ExpenseApproval->level1==='approved' &&$leaveApplication->ExpenseApproval->level2==='approved' && $leaveApplication->ExpenseApproval->level3==="pending" && $approvalType->MaxLevel === 'Level3') {

                $leaveApplication->ExpenseApproval->update([
                    'level3' => 'approved',
                ]);
        
                // Update the AppliedLeave model fields
                $leaveApplication->update([
                    'status' => 'approved',
                ]);

                $content = "The Expense you have applied for has been approved.";
                
                Mail::to($Approvalrecipient)->send(new ExpenseApprovedMail($user, $content));
            
                // Redirect back with a success message
                return redirect()->back()->with('success', 'Expense application approved successfully.');
            } else {
                // Handle cases where the leave application cannot be approved (e.g., it's not at the expected level or already approved)
                return redirect()->back()->with('error', 'Expense application cannot be approved.');
            }
        }else if($approvalType->approval_type === "Single User"){
             // Update the AppliedLeave model fields
             $leaveApplication->update([
                'status' => 'approved',
            ]);
            $content = "The Expense you have applied for has been approved.";
        
            Mail::to($Approvalrecipient)->send(new ExpenseApprovedMail($user, $content));
        
            Notification::make() 
            ->title('Expense Approved successfully')
            ->success()
            ->send();
        }
       

    }
    public static function RejectExpense($record) {
        $id = $record->applied_advance_id;
        $ExpenseApplication = ExpenseApplication::findOrFail($id);
        $remark = $ExpenseApplication->remark;
        $expense_id = $ExpenseApplication->expense_type_id;

        $userID = $ExpenseApplication->user_id;
 
        $user = MasEmployee::where('id', $userID)->first();
        $Approvalrecipient = $user->email;

        $approvalRuleId = ExpenseApprovalRule::where('type_id', $expense_id)->value('id');
       
        $approvalType = ExpenseApprovalCondition::where('approval_rule_id', $approvalRuleId)->first();

        $hierarchy_id = $approvalType->hierarchy_id;

        $leaveApplication = ExpenseApplication::findOrFail($id);
        $departmentId = $user->department_id;
        $departmentHead = MasEmployee::where('department_id', $departmentId)
        ->where('is_departmentHead', true)
        ->first();

                $leaveApplication->AdvanceApproval->update([
                    'level1' => 'rejected',
                    'level2' => 'rejected',
                    'level3' => 'rejected',
                    
                ]);
                // Update the AppliedLeave model fields
                $leaveApplication->update([
                    'status' => 'rejected',
                ]);

                $content = "The Expense you have applied for has been rejected.";
                
                Mail::to($Approvalrecipient)->send(new ExpenseApprovedMail($user, $content));
            
           
        if($approvalType->approval_type === "Single User"){
             // Update the AppliedLeave model fields
             $leaveApplication->update([
                'status' => 'rejected',
            ]);
            $content = "The Expense have applied for has been rejected.";
        
            Mail::to($Approvalrecipient)->send(new ExpenseApprovedMail($user, $content));
        
            Notification::make() 
            ->title('Expense rejected successfully')
            ->success()
            ->send();
        }
       

    } 


}
