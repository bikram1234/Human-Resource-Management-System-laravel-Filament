<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransferClaimApprovalResource\Pages;
use App\Filament\Resources\TransferClaimApprovalResource\RelationManagers;
use App\Models\TransferClaimApproval;
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
use App\Models\ExpenseApprovalCondition;
use App\Models\ExpenseApprovalRule;
use Illuminate\Support\Facades\Mail;
use App\Models\Level;
use App\Models\MasEmployee;
use App\Models\TransferClaim;
use Filament\Tables\Actions\Action;

class TransferClaimApprovalResource extends Resource
{
    protected static ?string $model = TransferClaimApproval::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Expense';
    protected static ?int $navigationSort = 9;



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
                Tables\Columns\TextColumn::make('ExpenseApply.user.name'),
                Tables\Columns\TextColumn::make('ExpenseApply.date')
                ->label("Date"),
                Tables\Columns\TextColumn::make('ExpenseApply.designation')
                ->label("Designation"),
                Tables\Columns\TextColumn::make('ExpenseApply.basic_pay')
                ->label("Basic Pay"),
                Tables\Columns\TextColumn::make('ExpenseApply.transfer_claim_type')
                ->label("Transfer Claim"),
                Tables\Columns\TextColumn::make('ExpenseApply.claim_amount')
                ->label("Amount"),
                Tables\Columns\TextColumn::make('ExpenseApply.status')
                ->label("Status"),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Action::make('Approve')
                ->action(fn (TransferClaimApproval $record) => TransferClaimApprovalResource::ApproveExpense($record))
                ->requiresConfirmation()
                ->modalHeading('Approve')
                ->modalSubheading('Are you sure you\'d like to approve? This cannot be undone.')
                ->modalButton('Yes, approve now')
                ->color('success')
                ->hidden(function ( TransferClaimApproval $record) {
                    return $record->TransferApply->status === "approved";
                }), 
                Action::make('Reject')
                ->action(fn (TransferClaimApproval $record) => TransferClaimApprovalResource::RejectExpense($record))
                ->requiresConfirmation()
                ->modalHeading('Reject')
                ->modalSubheading('Are you sure you\'d like to reject? This cannot be undone.')
                ->modalButton('Yes, reject now')
                ->color('danger')
                ->hidden(function ( TransferClaimApproval $record) {
                    return $record->TransferApply->status === "approved";
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
    public function widgets(): array
    {
        return [
            // Other widgets...
            LatestApproval::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransferClaimApprovals::route('/'),
            'create' => Pages\CreateTransferClaimApproval::route('/create'),
        ];
    }

    public static function ApproveExpense($record) {
        $id = $record->applied_expense_id;
        $ExpenseApplication = TransferClaim::findOrFail($id);
        $expense_id = $ExpenseApplication->expense_type_id;
        $userID = $ExpenseApplication->user_id;
 
        $user = MasEmployee::where('id', $userID)->first();
        $Approvalrecipient = $user->email;

        $approvalRuleId = ExpenseApprovalRule::where('type_id', $expense_id)->value('id');
       
        $approvalType = ExpenseApprovalCondition::where('approval_rule_id', $approvalRuleId)->first();

        $hierarchy_id = $approvalType->hierarchy_id;

        $leaveApplication = TransferClaim::findOrFail($id);
        $departmentId = $user->department_id;
        $departmentHead = MasEmployee::where('department_id', $departmentId)
        ->where('is_departmentHead', true)
        ->first();

        if($approvalType->approval_type === "Hierarchy"){

            if ($leaveApplication->TransferClaimApproval->level1 === 'pending' && $approvalType->MaxLevel === 'Level1') {
                $leaveApplication->TransferClaimApproval->update([
                    'level1' => 'approved',
                ]);
        
                // Update the AppliedLeave model fields
                $leaveApplication->update([
                    'status' => 'approved',
                ]);
             
                $content = "Transfer Claim applied for has been approved.";
            
                Mail::to($Approvalrecipient)->send(new ExpenseApprovedMail($user, $content));
            
                Notification::make() 
                ->title('Transfer Claim Approved successfully')
                ->success()
                ->send();
            
            }else if (
                $leaveApplication->TransferClaimApproval->level1 === 'pending' &&
                $leaveApplication->TransferClaimApproval->level2 === 'pending' &&
                ($approvalType->MaxLevel === 'Level2' || $approvalType->MaxLevel === 'Level3')
            ) { 
                $leaveApplication->TransferClaimApproval->update([
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
                    ->title('Transfer Claim Approved successfully')
                    ->success()
                    ->send();
                
                }
    
            }else if($leaveApplication->TransferClaimApproval->level1==='approved' && $approvalType->MaxLevel === 'Level2') {
                $leaveApplication->TransferClaimApproval->update([
                    'level1' => 'approved',
                ]);
        
                // Update the AppliedLeave model fields
                $leaveApplication->update([
                    'status' => 'approved',
                ]);
    
                $content = "Transfer Claim applied for has been approved.";
                Mail::to($Approvalrecipient)->send(new ExpenseApprovedMail($user, $content));
            
                Notification::make() 
                ->title('Transfer Claim Approved successfully')
                ->success()
                ->send();

            } else if($leaveApplication->TransferClaimApproval->level1==='approved' &&$leaveApplication->TransferClaimApproval->level2==='pending' && $approvalType->MaxLevel === 'Level3') {
                $leaveApplication->TransferClaimApproval->update([
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
            } else if($leaveApplication->TransferClaimApproval->level1==='approved' &&$leaveApplication->TransferClaimApproval->level2==='approved' && $leaveApplication->TransferClaimApproval->level3==="pending" && $approvalType->MaxLevel === 'Level3') {

                $leaveApplication->TransferClaimApproval->update([
                    'level3' => 'approved',
                ]);
        
                // Update the AppliedLeave model fields
                $leaveApplication->update([
                    'status' => 'approved',
                ]);

                $content = "Transfer Claim applied for has been approved.";
                
                Mail::to($Approvalrecipient)->send(new ExpenseApprovedMail($user, $content));
            
                // Redirect back with a success message
                return redirect()->back()->with('success', 'Transfer Claim application approved successfully.');
            } else {
                // Handle cases where the leave application cannot be approved (e.g., it's not at the expected level or already approved)
                return redirect()->back()->with('error', 'Transfer Claim application cannot be approved.');
            }
        }else if($approvalType->approval_type === "Single User"){
             // Update the AppliedLeave model fields
             $leaveApplication->update([
                'status' => 'approved',
            ]);
            $content = "Transfer Claim applied for has been approved.";
        
            Mail::to($Approvalrecipient)->send(new ExpenseApprovedMail($user, $content));
        
            Notification::make() 
            ->title('Transfer Claim Approved successfully')
            ->success()
            ->send();
        }
       

    }
    
    public static function RejectExpense($record) {
        $id = $record->applied_advance_id;
        $ExpenseApplication = TransferClaim::findOrFail($id);
        $remark = $ExpenseApplication->remark;
        $expense_id = $ExpenseApplication->expense_type_id;

        $userID = $ExpenseApplication->user_id;
 
        $user = MasEmployee::where('id', $userID)->first();
        $Approvalrecipient = $user->email;

        $approvalRuleId = ExpenseApprovalRule::where('type_id', $expense_id)->value('id');
       
        $approvalType = ExpenseApprovalCondition::where('approval_rule_id', $approvalRuleId)->first();

        $hierarchy_id = $approvalType->hierarchy_id;

        $leaveApplication = TransferClaim::findOrFail($id);
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

                $content = "Transfer Claim you have applied for has been rejected.";
                
                Mail::to($Approvalrecipient)->send(new ExpenseApprovedMail($user, $content));
            
           
        if($approvalType->approval_type === "Single User"){
             // Update the AppliedLeave model fields
             $leaveApplication->update([
                'status' => 'rejected',
            ]);
            $content = "Transfer Claim have applied for has been rejected.";
        
            Mail::to($Approvalrecipient)->send(new ExpenseApprovedMail($user, $content));
        
            Notification::make() 
            ->title('Transfer Claim rejected successfully')
            ->success()
            ->send();
        }
       

    } 

}
