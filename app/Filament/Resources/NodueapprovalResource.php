<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NodueapprovalResource\Pages;
use App\Filament\Resources\NodueapprovalResource\RelationManagers;
use App\Mail\NoDueApprovalMail;
use App\Models\nodueapproval;
use App\Models\Nodue;
use App\Models\department;
use App\Models\MasEmployee;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Actions\Action;
use App\Mail\NoDueSectionMail;
use App\Mail\NoDueApproveMail;
use Illuminate\Support\Facades\Mail;
use Filament\Notifications\Notification;
use Chiiya\FilamentAccessControl\Models\FilamentUser;





class NodueapprovalResource extends Resource
{
    protected static ?string $model = Nodueapproval::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-circle';
    protected static ?string $navigationGroup = 'No Due';
    protected static ?string $navigationLabel = 'Approval';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('date')
                    ->type('date')
                    ->default(now()->toDateString())  // Set default value to current date
                    ->disabled()  // Make the field disabled
                    ->required(),
                Forms\Components\TextInput::make('status')
                    ->required(),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name'),
                Tables\Columns\TextColumn::make('date'),
                // Tables\Columns\TextColumn::make('nodue.reason')->label("Reason"),
                Tables\Columns\TextColumn::make('nodue.status')->label("Status"),
                
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Action::make('Approve')
                ->action(fn (nodueapproval $record) => NodueapprovalResource::ApproveNodue($record))
                ->requiresConfirmation()
                ->modalHeading('Approve')
                ->modalSubheading('Are you sure you\'d like to approve? This cannot be undone.')
                ->modalButton('Yes, approve now')
                ->color('success')
                ->hidden(function ( Nodueapproval $record) {
                    return $record->nodue->status === "approved";
                }), 
                Action::make('Reject')
                ->action(fn (nodueapproval $record) => NodueapprovalResource::RejectNodue($record))
                ->requiresConfirmation()
                ->modalHeading('Reject')
                ->modalSubheading('Are you sure you\'d like to reject? This cannot be undone.')
                ->modalButton('Yes, reject now') 
                ->color('danger')
                ->hidden(function ( Nodueapproval $record) {
                    return $record->nodue->status === "approved";
                }),             ])
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
            'index' => Pages\ListNodueapprovals::route('/'),
        ];
    } 
    public static function ApproveNodue($record) {       
        $id = $record->id;
        $user_id = $record->user_id;
        $currentUser = auth()->user();
        $sectionhead = $currentUser->hasRole('Section Head');
        //dd($sectionhead);
        $departmenthead = $currentUser->hasRole('Department Head');
     
        // Find the user associated with the record
        $recordUser = FilamentUser::findOrFail($user_id);
     
        // Check if the current user is in the same department as the record user
        // if ($currentUser->department_id != $recordUser->department_id) {
        //     return false; // The current user is not in the same department as the record user
        // }
        if ($sectionhead == true) {
            // Check if all section heads within a department have approved
            $approval = nodueapproval::findOrFail($id);
            $departmentID = $currentUser->department_id;
            $sections = Department::find($departmentID)->sections;
        
            // Collect section head IDs within the department
            // $sectionHeadIDs = $sections->flatMap(function ($section) {
            //     return $section->users()->where('is_sectionHead', true)->pluck('id')->toArray();
            // })->unique();
            $roleName = 'Section Head';
            $sectionHeadIDs = $sections->flatMap(function ($section) use ($roleName) {
                return $section->users()
                    ->whereHas('roles', fn ($query) => $query->where('name', $roleName))
                    ->pluck('id')
                    ->toArray();
            })->unique();
           //dd($sectionHeadIDs);
        
            if ($sectionHeadIDs->contains($currentUser->id)) {
                // Check if the current user has already approved
                $approverUserIds = json_decode($approval->approver_user_id, true) ?? [];
                if ($approval->status1 === 'approved' && in_array($currentUser->id, $approverUserIds)) {
                    dd("You have already approved this request");
                    return false;
                }
            
                // Add the current user's ID to the array
                $approverUserIds[] = $currentUser->id;
            
                // Update the attribute with the modified array
                $approval->setAttribute('approver_user_id', json_encode($approverUserIds));
            
                // Update the approval information for the current user
                //$approval->status1 = 'approved';
                $approval->save();
            
                // Check if all section heads have approved
                $approvedSectionHeadIDs = $approverUserIds;
            
                if (count($sectionHeadIDs->toArray()) === count($approvedSectionHeadIDs)) {
                    // Update the status to 'approved' if all section heads have approved
                    $approval->status1 = 'approved';
                    $approval->save();
                    $departments = Department::all();
                    // $departmentHeads = $departments->flatMap(function ($department) {
                    //     return $department->users()->where('is_departmentHead', true)->get(); // Fetch user objects, not just IDs
                    // })->unique();
                    $roleName = 'Department Head';

                    $departmentHeads = $departments->flatMap(function ($department) use ($roleName) {
                        return $department->users()
                            ->whereHas('roles', fn ($query) => $query->where('name', $roleName))
                            ->get();
                    })->unique();
                    
                    // Send email to each department head
                    foreach ($departmentHeads as $departmentHead) {
                        $recipient = $departmentHead->email;
                        $department = $departmentHead->first_name . ' ' . $departmentHead->last_name;
                        $approval = nodueapproval::findOrFail($id);
                        $user = $approval->user->name;

                        
                    
                        // Create a new instance of NoDueMail for each department head
                        Mail::to($recipient)->send(new NoDueSectionMail($department,$user ));
                    }
                    dd("All Section heads have approved");



                    return true;
                }
            
                // Output the list of section heads not approved
                $notApprovedSectionHeadIDs = array_diff($sectionHeadIDs->toArray(), $approvedSectionHeadIDs);
                dd("Section Heads IDs not approved: " . implode(', ', $notApprovedSectionHeadIDs));
                return false;
            } else {
                dd("Current user is not a section head");
                return false;
            }         
        }                      
        if ($departmenthead == true) {
            // Check if the current user is a department head
            $approval = nodueapproval::findOrFail($id);
            $departmentID = $currentUser->department_id;
        
            // Fetch departments associated with the user's department
            $departments = Department::all();
        
            // Collect department head IDs within the department
            // $departmentHeadIDs = $departments->flatMap(function ($department) {
            //     return $department->users()->where('is_departmentHead', true)->pluck('id')->toArray();
            // })->unique();
            $role = 'Department Head';

            $departmentHeadIDs = $departments->flatMap(function ($department) use ($role) {
                return $department->users()
                    ->whereHas('roles', fn ($query) => $query->where('name', $role))
                    ->pluck('id')
                    ->toArray();
            })->unique();
            //dd($departmentHeadIDs );
        
            // Check if the current user is a valid department head
            if ($departmentHeadIDs->contains($currentUser->id)) {
                // Check if the current user has already approved
                $approveUserIds = json_decode($approval->department_approval_id, true) ?? [];
                if ($approval->status2 === 'approved' && in_array($currentUser->id, $approveUserIds)) {
                    dd("You have already approved this request");
                    return false;
                }
        
                // Add the current user's ID to the array
                $approveUserIds[] = $currentUser->id;
        
                // Update the attribute with the modified array
                $approval->setAttribute('department_approval_id', json_encode($approveUserIds));
        
                // Update the approval information for the current user
               // $approval->status2 = 'approved';
                $approval->save();
        
                // Check if all department heads have approved
                $approvedDepartmentHeadIDs = $approveUserIds;
        
                if (count($departmentHeadIDs->toArray()) === count($approvedDepartmentHeadIDs)) {
                    // Update the status to 'approved' if all department heads have approved
                    $approval->status2 = 'approved';
                    $approval->nodue->status = 'approved';
                    $approval->save();
                    $approval->nodue->save();
                    $applicant = $approval->nodue->user->email;
                    $user =  $approval->nodue->user->first_name . ' ' .  $approval->nodue->user->last_name;

                    $content = "The No Due application that you have applied for has been approved.";
                    Mail::to($applicant)->send(new NoDueApprovalMail($user, $content));
                    Notification::make() 
                    ->title('No Due Approved successfully')
                    ->success()
                    ->send();
                    dd("All department heads have approved");
                    return true;
                }
        
                // Output the list of department heads not approved
                $notApprovedDepartmentHeadIDs = array_diff($departmentHeadIDs->toArray(), $approvedDepartmentHeadIDs);
                dd("Department Heads IDs not approved: " . implode(', ', $notApprovedDepartmentHeadIDs));
                return false;
            } else {
                dd("Current user is not a department head");
                return false;
            }
        }
    }

    public static function RejectNodue($record) {
        // Decline a request
        $id = $record->id;
        $approval = nodueapproval::findOrFail($id);
        $approval->status1 = 'rejected';
        $approval->status2 = 'rejected';
        $approval->save();
        $currentUser = auth()->user();
        $reject= $currentUser->name;

        // Update the status of the request
        $approval->nodue->status = 'rejected';
        $approval->nodue->save();
        $applicant = $approval->nodue->user->email;
        $user =  $approval->nodue->user->first_name . ' ' .  $approval->nodue->user->last_name;

        $content = "The No Due application that you have applied for has been Rejected. And request has been rejected by " . $reject;
        Mail::to($applicant)->send(new NoDueApprovalMail($user, $content));
        Notification::make() 
        ->title('No Due Rejected successfully')
        ->success()
        ->send();

    }
}