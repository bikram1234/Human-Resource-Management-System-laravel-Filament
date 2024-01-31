<?php

namespace App\Filament\Resources\ApplyAdvanceResource\Pages;

use App\Filament\Resources\ApplyAdvanceResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\AdvanceApprovalRule;
use App\Models\Level;
use App\Mail\AdvanceApplicationMail;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use App\Models\MasEmployee;
use Chiiya\FilamentAccessControl\Models\FilamentUser;
use App\Models\AdvanceApprovalCondition;

class CreateApplyAdvance extends CreateRecord
{
    protected static string $resource = ApplyAdvanceResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $currentUser = auth()->user();
        $sectionId = auth()->user()->section_id;
        // $sectionHead = MasEmployee::where('section_id', $sectionId)
        // ->where('roles', 'Section Head')->first();
        // Assuming 'Section Head' is the name of the role you want to find

        $sectionHead = FilamentUser::where('section_id', $sectionId)
            ->whereHas('roles', fn ($query) => $query->where('name', 'Section Head'))
            ->first();
       
        $advance_id = $data['advance_type_id'];
        $approvalRuleId = AdvanceApprovalRule::where('type_id', $advance_id)->value('id');
        $approvalType = AdvanceApprovalCondition::where('approval_rule_id', $approvalRuleId)->first();
        $hierarchy_id = $approvalType->hierarchy_id;
    
        if ($approvalType->approval_type == "Hierarchy") {
                // Fetch the record from the levels table based on the $hierarchy_id
                //$levelRecord = Level::where('hierarchy_id', $hierarchy_id)->first();
                $levelRecord = Level::where('hierarchy_id', $hierarchy_id)
                ->where('level', 1)
                ->first();
    
                if ($levelRecord) {
                    // Access the 'value' field from the level record
                    $levelValue = $levelRecord->value;
    
                    // Determine the recipient based on the levelValue
                    $recipient = '';
    
                    // Check the levelValue and set the recipient accordingly
                    if ($levelValue === "SH") {
                         $recipient = $sectionHead->email; // Replace with the actual field name
                         $approval = $sectionHead;
    
                        Mail::to($recipient)->send(new AdvanceApplicationMail($approval, $currentUser));
                    }else{
                        // Access the 'value' field from the level record
                        $levelValue = $levelRecord->value;
                        $userID = $levelRecord->emp_id;
                        $approval = FilamentUser::where('id', $userID)->first();
                        // Determine the recipient based on the levelValue
                        $recipient = $approval->email;
                
                        Mail::to($recipient)->send(new AdvanceApplicationMail($approval, $currentUser));  
                    

                    }
                    
                }
            
        }elseif($approvalType->approval_type == "Single User"){

                $userID = $approvalType->employee_id;
                $approval = FilamentUser::where('id', $userID)->first();
                $recipient = $approval->email;
                Mail::to($recipient)->send(new AdvanceApplicationMail($approval, $currentUser));  
        }
        return $data;
     
    } 
    protected function getRedirectUrl(): string{
        return $this->getResource()::getUrl('index');
    }
}
