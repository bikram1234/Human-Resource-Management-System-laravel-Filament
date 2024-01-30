<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EncashmentApprovalResource\Pages;
use App\Filament\Resources\EncashmentApprovalResource\RelationManagers;
use App\Models\EncashmentApproval;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;
use App\Mail\LeaveEncashmentMail;
use App\Mail\LeaveEncashmentApprovalMail;
use App\Models\AppliedEncashment;
use Illuminate\Support\Facades\Mail;
use App\Models\Level;
use App\Models\encashment;
use Chiiya\FilamentAccessControl\Models\FilamentUser;
use Filament\Tables\Actions\Action;
use App\Models\LeaveEncashmentApprovalCondition;
use App\Models\LeaveEncashmentApprovalRule;
use App\Models\LeaveBalance;




class EncashmentApprovalResource extends Resource
{
    protected static ?string $model = EncashmentApproval::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-circle';
    protected static ?string $navigationLabel = 'Encashment Approval';
    protected static ?string $pluralModelLabel = 'Encashment Approval List';

    protected static ?string $navigationGroup = 'Encashment';
    protected static ?int $navigationSort = 3;
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('EncashmentApply.user.name'),
                Tables\Columns\TextColumn::make('EncashmentApply.date')
                ->label("Application date"),
                Tables\Columns\TextColumn::make('EncashmentApply.number_of_days')
                ->label("Number of Days"),
                Tables\Columns\TextColumn::make('EncashmentApply.amount')
                ->label("Number of Days"),
                Tables\Columns\TextColumn::make('EncashmentApply.status')
                ->label("Status"),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Action::make('Approve')
                ->action(fn (EncashmentApproval $record) => EncashmentApprovalResource::ApproveEncashment($record))
                ->requiresConfirmation()
                ->modalHeading('Approve')
                ->modalSubheading('Are you sure you\'d like to approve? This cannot be undone.')
                ->modalButton('Yes, approve now')
                ->color('success')
                ->hidden(function ( EncashmentApproval $record) {
                    return $record->EncashmentApply->status === "approved";
                }),    
                //Action::make('Reject')
                //->action(fn (EncashmentApproval $record) => EncashmentApprovalResource::RejectEncashment($record))
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
                ->hidden(function ( EncashmentApproval $record) {
                    return $record->EncashmentApply->status === "approved";
                })
                ->action(function (EncashmentApproval $record, array $data) {
                    $remark = $data['remark'];
                    EncashmentApprovalResource::RejectEncashment($record, $remark);
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
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEncashmentApprovals::route('/'),
            'edit' => Pages\EditEncashmentApproval::route('/{record}/edit'),
        ];
    } 
    
    public static function ApproveEncashment($record) {
        $id = $record->applied_encashment_id;
        $EncashmentApplication = AppliedEncashment::findOrFail($id);
        $number_of_days = $EncashmentApplication->number_of_days;
        $ExpenseApplication = AppliedEncashment::findOrFail($id);
        $Encashment = encashment:: Where('name','Leave Encashment')->first();
        if ($Encashment) {
            $EncashmentId = $Encashment->id;
            //dd($earnedLeaveId);
        } else {
            // Handle the case where no LeaveType with the specified name is found
            echo "Encashment  with the name 'Leave Encashment' not found.";
        }
        //$expense_id = $ExpenseApplication->expense_type_id;
        $userID = $EncashmentApplication->user_id;
        $userID = $ExpenseApplication->user_id;
 
        $user = FilamentUser::where('id', $userID)->first();
        $Approvalrecipient = $user->email;

        $approvalRuleId = LeaveEncashmentApprovalRule::where('type_id', $EncashmentId)->value('id');
        //dd($approvalRuleId);
       
        $approvalType = LeaveEncashmentApprovalCondition::where('approval_rule_id', $approvalRuleId)->first();

        $hierarchy_id = $approvalType->hierarchy_id;

        $leaveApplication = AppliedEncashment::findOrFail($id);
        $departmentId = $user->department_id;
        $departmentHead =FilamentUser::where('section_id', $departmentId)
        ->whereHas('roles', fn ($query) => $query->where('name', 'Department Head'))
        ->first();

        if($approvalType->approval_type === "Hierarchy"){

            if ($leaveApplication->EncashmentApproval->level1 === 'pending' && $approvalType->MaxLevel === 'Level1') {
                $leaveApplication->EncashmentApproval->update([
                    'level1' => 'approved',
                ]);
        
                // Update the AppliedLeave model fields
                $leaveApplication->update([
                    'status' => 'approved',
                ]);
                $instance = new self(); // Create an instance
                $instance->fetchEarnedLeaveBalance($userID, $number_of_days);
             
                $content = "Leave Encashment has been approved.";
            
                Mail::to($Approvalrecipient)->send(new LeaveEncashmentApprovalMail($user, $content));
            
                Notification::make() 
                ->title('Leave Encashment Approved successfully')
                ->success()
                ->send();
            
            }else if (
                $leaveApplication->EncashmentApproval->level1 === 'pending' &&
                $leaveApplication->EncashmentApproval->level2 === 'pending' &&
                ($approvalType->MaxLevel === 'Level2' || $approvalType->MaxLevel === 'Level3')
            ) { 
                $leaveApplication->EncashmentApproval->update([
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
    
                    Mail::to($recipient)->send(new LeaveEncashmentMail($approval, $currentUser));
                    Notification::make() 
                    ->title('Leave Encashment Approved successfully')
                    ->success()
                    ->send();
                
                }
    
            }else if($leaveApplication->EncashmentApproval->level1==='approved' && $approvalType->MaxLevel === 'Level2') {
                $leaveApplication->EncashmentApproval->update([
                    'level1' => 'approved',
                ]);
        
                // Update the AppliedLeave model fields
                $leaveApplication->update([
                    'status' => 'approved',
                ]);

                $instance = new self(); // Create an instance
                $instance->fetchEarnedLeaveBalance($userID, $number_of_days);
    
                $content = "Leave Encashment has been approved.";
                Mail::to($Approvalrecipient)->send(new LeaveEncashmentApprovalMail($user, $content));
            
                Notification::make() 
                ->title('Leave Encashment Approved successfully')
                ->success()
                ->send();

            } else if($leaveApplication->EncashmentApproval->level1==='approved' &&$leaveApplication->EncashmentApproval->level2==='pending' && $approvalType->MaxLevel === 'Level3') {
                $leaveApplication->EncashmentApproval->update([
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
    
                    Mail::to($recipient)->send(new LeaveEncashmentMail($approval, $currentUser));
  
                }
            } else if($leaveApplication->EncashmentApproval->level1==='approved' &&$leaveApplication->EncashmentApproval->level2==='approved' && $leaveApplication->EncashmentApproval->level3==="pending" && $approvalType->MaxLevel === 'Level3') {

                $leaveApplication->EncashmentApproval->update([
                    'level3' => 'approved',
                ]);
        
                // Update the AppliedEncashment model fields
                $leaveApplication->update([
                    'status' => 'approved',
                ]);
                $instance = new self(); // Create an instance
                $instance->fetchEarnedLeaveBalance($userID, $number_of_days);

                $content = "Leave Encashment has been approved.";
                
                Mail::to($Approvalrecipient)->send(new LeaveEncashmentApprovalMail($user, $content));
            
                // Redirect back with a success message
                return redirect()->back()->with('success', 'Leave Encashment approved successfully.');
            } else {
                // Handle cases where the leave application cannot be approved (e.g., it's not at the expected level or already approved)
                return redirect()->back()->with('error', 'Leave Encashment cannot be approved.');
            }
        }else if($approvalType->approval_type === "Single User"){
             // Update the AppliedLeave model fields
             $leaveApplication->update([
                'status' => 'approved',
            ]);
            $content = "Leave Encashment has been approved.";
        
            Mail::to($Approvalrecipient)->send(new LeaveEncashmentApprovalMail($user, $content));
        
            Notification::make() 
            ->title('Leave Encashment Approved successfully')
            ->success()
            ->send();
        }
       

    }
    public function fetchEarnedLeaveBalance( $userID, $number_of_days)
    {
        $user = FilamentUser::where('id', $userID)->first();

        $totalAppliedDays = $number_of_days;
      
        // $leaveBalanceNow = $leaveDuration - $totalAppliedDays;

        $leaveBalanceRecord = LeaveBalance::where('Employee_id', $userID)
        ->first();

        if ($leaveBalanceRecord) {
                $leaveBalanceNow = ($leaveBalanceRecord->earned_leave_balance) - $totalAppliedDays;
                $leaveBalanceRecord->earned_leave_balance = $leaveBalanceNow;
                $leaveBalanceRecord->save();
            // Update the existing leave balance record
        
        } 
    }
     
    public static function RejectEncashment($record, $remark) {
        $id = $record->applied_encashment_id;
        $ExpenseApplication = AppliedEncashment::findOrFail($id);
       // $expense_id = $ExpenseApplication->expense_type_id;
       $Encashment = encashment:: Where('name','Leave Encashment')->first();
       if ($Encashment) {
           $EncashmentId = $Encashment->id;
           //dd($earnedLeaveId);
       } else {
           // Handle the case where no LeaveType with the specified name is found
           echo "Encashment with the name 'Leave Encashment' not found.";
       }

        $userID = $ExpenseApplication->user_id;
 
        $user = FilamentUser::where('id', $userID)->first();
        $Approvalrecipient = $user->email;

        $approvalRuleId = LeaveEncashmentApprovalRule::where('type_id', $EncashmentId)->value('id');
       
        $approvalType = LeaveEncashmentApprovalCondition::where('approval_rule_id', $approvalRuleId)->first();

        $hierarchy_id = $approvalType->hierarchy_id;

        $leaveApplication = AppliedEncashment::findOrFail($id);

                $leaveApplication->EncashmentApproval->update([
                    'level1' => 'rejected',
                    'level2' => 'rejected',
                    'level3' => 'rejected',
                    'remark' => $remark
                    
                ]);
                // Update the AppliedEncashment model fields
                $leaveApplication->update([
                    'status' => 'rejected',
                    'remark' => $remark

                ]);

                $content = "Leave Encashment has been rejected.";
                
                Mail::to($Approvalrecipient)->send(new LeaveEncashmentApprovalMail($user, $content));
            
           
        if($approvalType->approval_type === "Single User"){
                // Update the AppliedEncashment model fields
                $leaveApplication->update([
                'status' => 'rejected',
                'remark' => $remark

            ]);
            $content = "Leave Encashment has been rejected.";
        
            Mail::to($Approvalrecipient)->send(new LeaveEncashmentApprovalMail($user, $content));
        
            Notification::make() 
            ->title('Leave Encashment rejected successfully')
            ->success()
            ->send();
        
        }
       

    } 
}

