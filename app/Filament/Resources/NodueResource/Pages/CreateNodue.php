<?php

namespace App\Filament\Resources\NodueResource\Pages;

use App\Filament\Resources\NodueResource;
use Filament\Pages\Actions;
use App\Models\department;
use Filament\Resources\Pages\CreateRecord;
use App\Mail\NoDueMail;
use Illuminate\Support\Facades\Mail;
use App\Models\MasEmployee;
use Chiiya\FilamentAccessControl\Models\FilamentUser;



class CreateNodue extends CreateRecord
{
    protected static string $resource = NodueResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $currentUser = auth()->user();
        $departmentID = $currentUser->department_id;
    
        // Get all sections under the current department
        $sections = Department::find($departmentID)->sections;
    
        // Find section heads in the current department
        $sectionHeads = FilamentUser::whereIn('section_id', $sections->pluck('id'))
        ->whereHas('roles', fn ($query) => $query->where('name', 'Section Head'))
        ->get();
        // $sectionHead = MasEmployee::where('section_id', $sections)
        //     ->whereHas('roles', fn ($query) => $query->where('name', 'Section Head'))
        //     ->first();
    
        // Send email to each section head
        foreach ($sectionHeads as $sectionHead) {
            $recipient = $sectionHead->email;
            $approval = $sectionHead->first_name . ' ' . $sectionHead->last_name;
    
            // Create a new instance of NoDueMail for each section head
            Mail::to($recipient)->send(new NoDueMail($approval, $currentUser));
        }
    
        return $data;
    }
    protected function getRedirectUrl(): string{
        return $this->getResource()::getUrl('index');
    }   

}
