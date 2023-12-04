<?php

namespace App\Filament\Resources\ApplyAdvanceResource\Pages;

use App\Filament\Resources\ApplyAdvanceResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\AdvanceApprovalRule;
use App\Models\Level;
use App\Mail\AdvanceApplicationMail;
use Illuminate\Support\Facades\Mail;
use App\Models\MasEmployee;
use App\Models\AdvanceApprovalCondition;

class CreateApplyAdvance extends CreateRecord
{
    protected static string $resource = ApplyAdvanceResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $currentUser = auth()->user();
        $sectionId = auth()->user()->section_id;
        $sectionHead = MasEmployee::where('section_id', $sectionId)
        ->where('is_sectionHead', true)->first();
       
        $advance_id = $data['advance_type_id'];
        $approvalRuleId = AdvanceApprovalRule::where('type_id', $advance_id)->value('id');
        $approvalType = AdvanceApprovalCondition::where('approval_rule_id', $approvalRuleId)->first();
        $hierarchy_id = $approvalType->hierarchy_id;
    
        if ($approvalType->approval_type == "Hierarchy") {
                // Fetch the record from the levels table based on the $hierarchy_id
                $levelRecord = Level::where('hierarchy_id', $hierarchy_id)->first();
    
                if ($levelRecord) {
                    // Access the 'value' field from the level record
                    $levelValue = $levelRecord->value;
    
                    // Determine the recipient based on the levelValue
                    $recipient = '';
    
                    // Check the levelValue and set the recipient accordingly
                    if ($levelValue === "SH") {
                         $recipient = $sectionHead->email; // Replace with the actual field name
                    }
                    $approval = $sectionHead;
    
                    Mail::to($recipient)->send(new AdvanceApplicationMail($approval, $currentUser));
                }
            
        }
        return $data;
     
    } 
}
