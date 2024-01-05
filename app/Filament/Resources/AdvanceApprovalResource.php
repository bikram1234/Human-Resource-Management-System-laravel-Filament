<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdvanceApprovalResource\Pages;
use App\Filament\Resources\AdvanceApprovalResource\RelationManagers;
use App\Models\AdvanceApproval;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\AdvanceApprovalCondition;
use App\Models\AdvanceApprovalRule;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use App\Mail\AdvanceApprovedMail;
use App\Mail\AdvanceApplicationMail;
use App\Models\ApplyAdvance;
use Illuminate\Support\Facades\Mail;
use App\Models\Level;
use App\Models\MasEmployee;
use Chiiya\FilamentAccessControl\Models\FilamentUser;


class AdvanceApprovalResource extends Resource
{
    protected static ?string $model = AdvanceApproval::class;
    protected static ?string $navigationIcon = 'heroicon-o-check-circle';
    protected static ?string $navigationGroup = 'Advance/Loan';
    protected static ?string $navigationLabel = 'Approval';
    protected static ?string $pluralModelLabel = 'Advance Approval List';
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
                Forms\Components\TextInput::make('remark')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('AdvanceApply.user.name'),
                Tables\Columns\TextColumn::make('AdvanceApply.advanceType.name'),
                Tables\Columns\TextColumn::make('AdvanceApply.date')
                ->label("date"),
                Tables\Columns\TextColumn::make('AdvanceApply.amount')
                ->label("Description"),
                Tables\Columns\TextColumn::make('AdvanceApply.purpose')
                ->label("Attachment"),
                Tables\Columns\TextColumn::make('AdvanceApply.status')
                ->label("Status"),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Action::make('Approve')
                ->action(fn (AdvanceApproval $record) => AdvanceApprovalResource::ApproveAdvance($record))
                ->requiresConfirmation()
                ->modalHeading('Approve')
                ->modalSubheading('Are you sure you\'d like to approve? This cannot be undone.')
                ->modalButton('Yes, approve now')
                ->color('success')
                ->hidden(function ( AdvanceApproval $record) {
                    return $record->AdvanceApply->status === "approved";
                }),
                Action::make('Reject')
                ->action(fn (AdvanceApproval $record) => AdvanceApprovalResource::RejectAdvance($record))
                ->requiresConfirmation()
                ->modalHeading('Reject')
                ->modalSubheading('Are you sure you\'d like to reject? This cannot be undone.')
                ->modalButton('Yes, reject now')
                ->color('danger')
                ->hidden(function ( AdvanceApproval $record) {
                    return $record->AdvanceApply->status === "approved";
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
            'index' => Pages\ListAdvanceApprovals::route('/'),
            'create' => Pages\CreateAdvanceApproval::route('/create'),
        ];
    } 
    public static function ApproveAdvance($record) {
        $id = $record->applied_advance_id;
        $ExpenseApplication = ApplyAdvance::findOrFail($id);
        $expense_id = $ExpenseApplication->advance_type_id;
        $userID = $ExpenseApplication->user_id;
 
        $user = FilamentUser::where('id', $userID)->first();
        $Approvalrecipient = $user->email;

        $approvalRuleId = AdvanceApprovalRule::where('type_id', $expense_id)->value('id');
       
        $approvalType = AdvanceApprovalCondition::where('approval_rule_id', $approvalRuleId)->first();

        $hierarchy_id = $approvalType->hierarchy_id;

        $leaveApplication = ApplyAdvance::findOrFail($id);
        $departmentId = $user->department_id;
        $departmentHead =FilamentUser::where('section_id', $departmentId)
        ->whereHas('roles', fn ($query) => $query->where('name', 'Department Head'))
        ->first();

        if($approvalType->approval_type === "Hierarchy"){

            if ($leaveApplication->AdvanceApproval->level1 === 'pending' && $approvalType->MaxLevel === 'Level1') {
                $leaveApplication->AdvanceApproval->update([
                    'level1' => 'approved',
                ]);
        
                // Update the AppliedLeave model fields
                $leaveApplication->update([
                    'status' => 'approved',
                ]);
             
                $content = "The Advance you have applied for has been approved.";
            
                Mail::to($Approvalrecipient)->send(new AdvanceApprovedMail($user, $content));
            
                Notification::make() 
                ->title('Advance Approved successfully')
                ->success()
                ->send();
            
            }else if (
                $leaveApplication->AdvanceApproval->level1 === 'pending' &&
                $leaveApplication->AdvanceApproval->level2 === 'pending' &&
                ($approvalType->MaxLevel === 'Level2' || $approvalType->MaxLevel === 'Level3')
            ) { 
                $leaveApplication->AdvanceApproval->update([
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
    
                    Mail::to($recipient)->send(new AdvanceApplicationMail($approval, $currentUser));
                    Notification::make() 
                    ->title('Advance Approved successfully')
                    ->success()
                    ->send();
                
                }
    
            }else if($leaveApplication->AdvanceApproval->level1==='approved' && $approvalType->MaxLevel === 'Level2') {
                $leaveApplication->AdvanceApproval->update([
                    'level1' => 'approved',
                ]);
        
                // Update the AppliedLeave model fields
                $leaveApplication->update([
                    'status' => 'approved',
                ]);
    
                $content = "The Advance you have applied for has been approved.";
                Mail::to($Approvalrecipient)->send(new AdvanceApprovedMail($user, $content));
            
                Notification::make() 
                ->title('Advance Approved successfully')
                ->success()
                ->send();

            } else if($leaveApplication->AdvanceApproval->level1==='approved' &&$leaveApplication->AdvanceApproval->level2==='pending' && $approvalType->MaxLevel === 'Level3') {
                $leaveApplication->AdvanceApproval->update([
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
    
                    Mail::to($recipient)->send(new AdvanceApplicationMail($approval, $currentUser));
  
                }
            } else if($leaveApplication->AdvanceApproval->level1==='approved' &&$leaveApplication->AdvanceApproval->level2==='approved' && $leaveApplication->AdvanceApproval->level3==="pending" && $approvalType->MaxLevel === 'Level3') {

                $leaveApplication->AdvanceApproval->update([
                    'level3' => 'approved',
                ]);
        
                // Update the AppliedLeave model fields
                $leaveApplication->update([
                    'status' => 'approved',
                ]);

                $content = "The Advance you have applied for has been approved.";
                
                Mail::to($Approvalrecipient)->send(new AdvanceApprovedMail($user, $content));
            
                // Redirect back with a success message
                return redirect()->back()->with('success', 'Advance application approved successfully.');
            } else {
                // Handle cases where the leave application cannot be approved (e.g., it's not at the expected level or already approved)
                return redirect()->back()->with('error', 'Advance application cannot be approved.');
            }
        }else if($approvalType->approval_type === "Single User"){
             // Update the AppliedLeave model fields
             $leaveApplication->update([
                'status' => 'approved',
            ]);
            $content = "The Advance you have applied for has been approved.";
        
            Mail::to($Approvalrecipient)->send(new AdvanceApprovedMail($user, $content));
        
            Notification::make() 
            ->title('Advance Approved successfully')
            ->success()
            ->send();
        }
       

    }   
    
    public static function RejectAdvance($record) {
        $id = $record->applied_advance_id;
        $ExpenseApplication = ApplyAdvance::findOrFail($id);
        $remark = $ExpenseApplication->remark;
        $expense_id = $ExpenseApplication->advance_type_id;

        $userID = $ExpenseApplication->user_id;
 
        $user = FilamentUser::where('id', $userID)->first();
        $Approvalrecipient = $user->email;

        $approvalRuleId = AdvanceApprovalRule::where('type_id', $expense_id)->value('id');
       
        $approvalType = AdvanceApprovalCondition::where('approval_rule_id', $approvalRuleId)->first();

        $hierarchy_id = $approvalType->hierarchy_id;

        $leaveApplication = ApplyAdvance::findOrFail($id);
        $departmentId = $user->department_id;
        $departmentHead = FilamentUser::where('department_id', $departmentId)
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

                $content = "The Advance you have applied for has been rejected.";
                
                Mail::to($Approvalrecipient)->send(new AdvanceApprovedMail($user, $content));
            
           
        if($approvalType->approval_type === "Single User"){
             // Update the AppliedLeave model fields
             $leaveApplication->update([
                'status' => 'rejected',
            ]);
            $content = "The Advance you have applied for has been rejected.";
        
            Mail::to($Approvalrecipient)->send(new AdvanceApprovedMail($user, $content));
        
            Notification::make() 
            ->title('Advance rejected successfully')
            ->success()
            ->send();
        }
       

    } 
}
