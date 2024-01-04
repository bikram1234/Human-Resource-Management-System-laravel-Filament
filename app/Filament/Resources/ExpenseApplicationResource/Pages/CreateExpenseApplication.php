<?php

namespace App\Filament\Resources\ExpenseApplicationResource\Pages;

use App\Filament\Resources\ExpenseApplicationResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\ExpenseApprovalRule;
use App\Models\Level;
use App\Mail\ExpenseApplicationMail;
use Illuminate\Support\Facades\Mail;
use App\Models\MasEmployee;
use App\Models\ExpenseApprovalCondition;
use Chiiya\FilamentAccessControl\Models\FilamentUser;


class CreateExpenseApplication extends CreateRecord
{
    protected static string $resource = ExpenseApplicationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $currentUser = auth()->user();
        $sectionId = auth()->user()->section_id;
        // $sectionHead = MasEmployee::where('section_id', $sectionId)
        // ->where('is_sectionHead', true)->first();
        $sectionHead = FilamentUser::where('section_id', $sectionId)
        ->whereHas('roles', fn ($query) => $query->where('name', 'Section Head'))
        ->first();
       
        $expense_id = $data['expense_type_id'];
        $approvalRuleId = ExpenseApprovalRule::where('type_id', $expense_id)->value('id');
        $approvalType = ExpenseApprovalCondition::where('approval_rule_id', $approvalRuleId)->first();
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
    
                    Mail::to($recipient)->send(new ExpenseApplicationMail($approval, $currentUser));
                }
            
        }
        return $data;
     
    } 
    protected function getRedirectUrl(): string{
        return $this->getResource()::getUrl('index');
    }  
}
