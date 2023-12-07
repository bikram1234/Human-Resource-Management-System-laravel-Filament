<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NodueapprovalResource\Pages;
use App\Filament\Resources\NodueapprovalResource\RelationManagers;
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



class NodueapprovalResource extends Resource
{
    protected static ?string $model = Nodueapproval::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
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
                Tables\Columns\TextColumn::make('nodue.reason')->label("Reason"),
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
            'index' => Pages\ListNodueapprovals::route('/'),
        ];
    } 
    
    // public static function ApproveNodue($record) {        
    //     // Approve a request
    //     $id = $record->id;
    //     $currentUser = auth()->user();
    //     $sectionId = $currentUser->section_id;
    //     $sectionhead = $currentUser->is_sectionHead;
    //     $departmenthead = $currentUser->designation_id;
    //    // dd($sectionhead);

    //    if ($sectionhead == true) {
    //     // try {
    //         $approval = nodueapproval::findOrFail($id);
    //        // dd($approval);  // Check if this line is reached
    
    //         $approval->status = 'approved';
    //         $approval->save();
    //         $departmentID = $currentUser->department_id;            
    //         // Get the department's sections
    //         $sections = Department::find($departmentID)->sections;

    //         foreach ($sections as $section) {
    //             $sectionHeads = $section->users()
    //                 ->where('is_sectionHead', true)  // Assuming 'is_sectionHead' is a column in the 'users' table
    //                 ->get();
            
    //             foreach ($sectionHeads as $sectionHead) {
    //                 $approval = nodueapproval::where('no_due_id', $approval->nodue->id)
    //                     ->where('user_id', $sectionHead->id)
    //                     ->first();
            
    //                 if (!$approval || $approval->status !== 'approved') {
    //                     // Not all section heads in the department have approved
    //                     // You can handle this case here, for example, log a message or perform some action.
    //                     break 2; // Break out of both loops
    //                 }
    //             }
    //         }

    //         // If the control reaches here, it means all section heads have approved, so you can proceed to create a new approval
    //         $departmentID = auth()->user()->department_id;
    //         $departmentHead = MasEmployee::where('department_id', $departmentID)->where('is_departmentHead', true)
    //             ->first();
    //         $approval = nodueapproval::findOrFail($id);
    //         //dd($departmentHead);
    //         nodueapproval::create([
    //             //dd($approval),
    //             'no_due_id' => $approval->nodue->id,
    //             'date'=>$approval->date,
    //             'user_id' => $approval->nodue->user_id,
    //             'status' => 'pending',
    //         ]);

    //      }else if($departmenthead == true){
    //         $approval = nodueapproval::findOrFail($id);
    //         $approval->status = 'approved';
    //         $approval->save();
    //         $departments = department::all();
    //         //dd($departments);    

    //         foreach ($departments as $department) {
    //             $departmentHead = $department->users()
    //                 ->where('is_departmentHead', true)
    //                 ->first();  // Use first() to retrieve a single user
            
    //             if (!$departmentHead) {
    //                 // If a department has no department head, continue to the next department
    //                 continue;
    //             }
            
    //             $approval = nodueapproval::where('no_due_id', $approval->nodue->id)
    //                 ->where('user_id', $departmentHead->id)
    //                 ->first();
            
    //             if (!$approval || $approval->status === 'pending') {
    //                 // If any department head's status is pending, return false
    //                 return false;
    //             }
    //         }
            
    //         // If the control reaches here, it means all checks passed, and you can proceed
    //         return true;
            

    //         // If the control reaches here, it means all checks passed, and you can proceed
    //         $approval->nodue->status = 'approved';
    //         $approval->nodue->save();

    //      }
    // }
    // public static function ApproveNodue($record) {        
    //     $id = $record->id;
    //     $currentUser = auth()->user();
    //     $sectionhead = $currentUser->is_sectionHead;
    //     $departmenthead = $currentUser->department_id;
    
    //     if ($sectionhead == true) {
    //         $approval = nodueapproval::findOrFail($id);
    //         $approval->status1 = 'approved';
    //         $approval->save();
        
    //         // Check if all section heads within a department have approved
    //         $departmentID = $currentUser->department_id;
    //         $sections = Department::find($departmentID)->sections;
        
    //         foreach ($sections as $section) {
    //             $sectionHeads = $section->users()
    //                 ->where('is_sectionHead', true)
    //                 ->get();
        
    //             foreach ($sectionHeads as $sectionHead) {
    //                 $sectionHeadApproval = nodueapproval::where('no_due_id', $approval->nodue->id)
    //                     ->where('user_id', $sectionHead->id)
    //                     ->where('status1', '!=', 'approved') // Check if status1 is not approved
    //                     ->first();
        
    //                 if ($sectionHeadApproval) {
    //                     $approval->status1 = 'pending'; // Set status1 back to 'pending' if any section head has not approved
    //                     $approval->save();
    //                     return false; // At least one section head in the department has not approved
    //                 }
    //             }
    //         }
    //     }else if ($departmenthead == true) {
    //         $approval = nodueapproval::findOrFail($id);
    //         $approval->status2 = 'approved';
    //         $approval->save();
    
    //         // Check if all department heads have approved
    //         $departments = Department::all();
    
    //         foreach ($departments as $department) {
    //             $departmentHead = $department->users()
    //                 ->where('is_departmentHead', true)
    //                 ->first();
    
    //             if (!$departmentHead) {
    //                 continue; // If a department has no department head, continue to the next department
    //             }
    
    //             $approval = Nodueapproval::where('no_due_id', $approval->nodue->id)
    //                 ->where('user_id', $departmentHead->id)
    //                 ->where('status2', 'approved')
    //                 ->first();
    
    //             if (!$approval) {
    //                 return false; // If any department head's status is not approved, return false
    //             }
    //         }
    
    //         // If all department heads have approved, update the status of the associated Nodue model
    //         $approval->nodue->status = 'approved';
    //         $approval->nodue->save();
    //     }
    
    //     return true; // All checks passed
    // }
    public static function ApproveNodue($record) {       
        $id = $record->id;
        $user_id = $record->user_id;
        $currentUser = auth()->user();
        $sectionhead = $currentUser->is_sectionHead;
        $departmenthead = $currentUser->is_departmentHead;
     
        // Find the user associated with the record
        $recordUser = MasEmployee::findOrFail($user_id);
     
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
            $sectionHeadIDs = $sections->flatMap(function ($section) {
                return $section->users()->where('is_sectionHead', true)->pluck('id')->toArray();
            })->unique();
        
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
                $approval->status1 = 'approved';
                $approval->save();
            
                // Check if all section heads have approved
                $approvedSectionHeadIDs = $approverUserIds;
            
                if (count($sectionHeadIDs->toArray()) === count($approvedSectionHeadIDs)) {
                    // Update the status to 'approved' if all section heads have approved
                    $approval->status1 = 'approved';
                    $approval->save();
                    dd("All section heads have approved");
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
            $departmentHeadIDs = $departments->flatMap(function ($department) {
                return $department->users()->where('is_departmentHead', true)->pluck('id')->toArray();
            })->unique();
           // dd($departmentHeadIDs );
        
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
}