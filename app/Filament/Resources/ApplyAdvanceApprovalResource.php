<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApplyAdvanceApprovalResource\Pages;
use App\Filament\Resources\ApplyAdvanceApprovalResource\RelationManagers;
use App\Models\ApplyAdvance;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\DeviceEMI;
use App\Models\AdvanceType;
use Chiiya\FilamentAccessControl\Models\FilamentUser;
use Closure;
use Filament\Notifications\Notification;
use App\Mail\AdvanceApprovedMail;
use App\Mail\AdvanceApplicationMail;
use App\Models\AdvanceApprovalCondition;
use App\Models\AdvanceApprovalRule;
use Illuminate\Support\Facades\Mail;
use App\Models\Level;
use App\Policies\AdvanceApprovalPolicy;




class ApplyAdvanceApprovalResource extends Resource
{

    protected static ?string $model = ApplyAdvance::class;

    protected static ?string $slug = 'apply-advances-approval';
    protected static ?string $navigationGroup = 'Advance/Loan';
    protected static ?string $navigationLabel = 'Advance Approval';
    protected static ?string $pluralModelLabel = 'Advance Approval List';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        $currentUserId = Auth::id();
        $currentDateTime = now();
        $user = FilamentUser::find($currentUserId);
        $empy_id = $user->emp_id;
        $advanceNo = 'ADL|EM|'.$empy_id.'|'.$currentDateTime->format('YmdHis');

        $advanceTypes = AdvanceType::all()->pluck('name', 'id')->toArray();

        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                ->default($currentUserId)
                ->disabled()
                ->required(),
                Forms\Components\Hidden::make('advance_no')
                ->default($advanceNo)
                ->disabled()
                ->required(),
                Forms\Components\TextInput::make('date')
                ->type('date')
                ->default(now()->toDateString())  // Set default value to current date
                ->disabled()  // Make the field disabled
                ->required(),
                Forms\Components\Select::make('advance_type_id')
                ->options(
                    AdvanceType::all()->pluck('name', 'id')->toArray()
                )
                ->label('Advance type')
                ->required()
                ->reactive(),
                Forms\Components\Select::make('item_type')
                ->options(DeviceEMI::all()->pluck('type', 'id')->toArray())
                ->label('Item type')
                ->reactive()
                ->searchable()
                ->required()
                ->visible(function ($get) use ($advanceTypes) {
                    $selectedAdvanceTypeId = $get('advance_type_id');
                    if (isset($selectedAdvanceTypeId) && array_key_exists($selectedAdvanceTypeId, $advanceTypes) && $advanceTypes[$selectedAdvanceTypeId] === "Device EMI") {
                        return true;
                    }
                    return false;
                })->afterStateUpdated(function ($state, Closure $set){
                    $amount = DeviceEMI::whereRaw("id =?", [$state])->value("amount");
                    // dd($amount);
                    $set('amount', $amount);
                    $amount = null;
                    $set('interest_rate',$amount);
                    $set('emi_count',$amount);
                    $set('total_amount', $amount);
                    $set('monthly_emi_amount', $amount);
                }),
                Forms\Components\TextInput::make('amount')  
                ->required() 
                ->reactive()
                ->numeric()
                ->label('Amount')
                ->disabled(function ($get) use ($advanceTypes) {
                    $selectedAdvanceTypeId = $get('advance_type_id');
                    if (isset($selectedAdvanceTypeId) && array_key_exists($selectedAdvanceTypeId, $advanceTypes) && $advanceTypes[$selectedAdvanceTypeId] === "Device EMI") {
                        return true;
                    }
                    return false;
                })->afterStateUpdated(function ($state, Closure $set, $get){
                    $amount = null;
                    $set('interest_rate',$amount);
                    $set('emi_count',$amount);
                    $set('total_amount', $amount);
                    $set('monthly_emi_amount', $amount);

                    //dd($set('interest_rate',$amount));
                }),
                Forms\Components\select::make('mode_of_travel')
                ->options([
                    'Car' => 'Car',
                    'Bike'=>'Bike',
                    'Plain'=>'Plain',
                    'Train'=>'Train'                    
                ])->required()
                ->visible(function ($get) use ($advanceTypes) {
                    $selectedAdvanceTypeId = $get('advance_type_id');
                    
                    // Check if the selected type is either "DSA Advance" or "Advance To Staff"
                    if (
                        isset($selectedAdvanceTypeId) &&
                        array_key_exists($selectedAdvanceTypeId, $advanceTypes) &&
                        (
                            $advanceTypes[$selectedAdvanceTypeId] === "DSA Advance" ||
                            $advanceTypes[$selectedAdvanceTypeId] === "Advance To Staff"
                        )
                    ) {
                        return true;
                    }
                    
                    return false;
                }),
                Forms\Components\TextInput::make('from_location')
                ->required()
                ->visible(function ($get) use ($advanceTypes) {
                    $selectedAdvanceTypeId = $get('advance_type_id');
                    
                    // Check if the selected type is either "DSA Advance" or "Advance To Staff"
                    if (
                        isset($selectedAdvanceTypeId) &&
                        array_key_exists($selectedAdvanceTypeId, $advanceTypes) &&
                        (
                            $advanceTypes[$selectedAdvanceTypeId] === "DSA Advance" ||
                            $advanceTypes[$selectedAdvanceTypeId] === "Advance To Staff"
                        )
                    ) {
                        return true;
                    }
                    
                    return false;
                }),
                Forms\Components\TextInput::make('to_location')
                ->required()
                ->visible(function ($get) use ($advanceTypes) {
                    $selectedAdvanceTypeId = $get('advance_type_id');
                    
                    // Check if the selected type is either "DSA Advance" or "Advance To Staff"
                    if (
                        isset($selectedAdvanceTypeId) &&
                        array_key_exists($selectedAdvanceTypeId, $advanceTypes) &&
                        (
                            $advanceTypes[$selectedAdvanceTypeId] === "DSA Advance" ||
                            $advanceTypes[$selectedAdvanceTypeId] === "Advance To Staff"
                        )
                    ) {
                        return true;
                    }
                    
                    return false;
                }),
                Forms\Components\DatePicker::make('from_date')
                ->required()
                ->visible(function ($get) use ($advanceTypes) {
                    $selectedAdvanceTypeId = $get('advance_type_id');
                    
                    // Check if the selected type is either "DSA Advance" or "Advance To Staff"
                    if (
                        isset($selectedAdvanceTypeId) &&
                        array_key_exists($selectedAdvanceTypeId, $advanceTypes) &&
                        (
                            $advanceTypes[$selectedAdvanceTypeId] === "DSA Advance" ||
                            $advanceTypes[$selectedAdvanceTypeId] === "Advance To Staff"
                        )
                    ) {
                        return true;
                    }
                    
                    return false;
                }),
                Forms\Components\DatePicker::make('to_date')
                ->required()
                ->after('from_date')
                ->visible(function ($get) use ($advanceTypes) {
                    $selectedAdvanceTypeId = $get('advance_type_id');
                    
                    // Check if the selected type is either "DSA Advance" or "Advance To Staff"
                    if (
                        isset($selectedAdvanceTypeId) &&
                        array_key_exists($selectedAdvanceTypeId, $advanceTypes) &&
                        (
                            $advanceTypes[$selectedAdvanceTypeId] === "DSA Advance" ||
                            $advanceTypes[$selectedAdvanceTypeId] === "Advance To Staff"
                        )
                    ) {
                        return true;
                    }
                    
                    return false;
                }),
                Forms\Components\TextInput::make('interest_rate')
                    ->numeric()
                    ->minValue(0)
                    ->required()
                    ->reactive()
                ->visible(function ($get) use ($advanceTypes) {
                    $selectedAdvanceTypeId = $get('advance_type_id');
                    
                    // Check if the selected type is either "DSA Advance" or "Advance To Staff"
                    if (
                        isset($selectedAdvanceTypeId) &&
                        array_key_exists($selectedAdvanceTypeId, $advanceTypes) &&
                        (
                            $advanceTypes[$selectedAdvanceTypeId] === "SIFA Loan" ||
                            $advanceTypes[$selectedAdvanceTypeId] === "Device EMI"
                        )
                    ) {
                        return true;
                    }
                    
                    return false;
                })->afterStateUpdated(function ($state, Closure $set, $get){
                    $amount = $get('amount');
                    // dd($amount);
                    $totalAmount = $amount + ($state * ($amount / 100));
                    $totalAmount = round($totalAmount,2);
                    $emi = null;
                    $set('total_amount',$totalAmount);
                    $set('emi_count', $emi);
                    $set('monthly_emi_amount',$emi);
                }),
                Forms\Components\TextInput::make('total_amount')
                ->numeric()
                ->minValue(0)
                // ->required()
                ->disabled()
                ->reactive()
                ->visible(function ($get) use ($advanceTypes) {
                    $selectedAdvanceTypeId = $get('advance_type_id');
                    
                    // Check if the selected type is either "DSA Advance" or "Advance To Staff"
                    if (
                        isset($selectedAdvanceTypeId) &&
                        array_key_exists($selectedAdvanceTypeId, $advanceTypes) &&
                        (
                            $advanceTypes[$selectedAdvanceTypeId] === "SIFA Loan" ||
                            $advanceTypes[$selectedAdvanceTypeId] === "Device EMI"
                        )
                    ) {
                        return true;
                    }
                    
                    return false;
                }),
                Forms\Components\TextInput::make('emi_count')
                ->numeric()
                ->minValue(0)
                ->required()
                ->reactive()
                ->label("EMI amount")
                ->visible(function ($get) use ($advanceTypes) {
                    $selectedAdvanceTypeId = $get('advance_type_id');
                    
                    // Check if the selected type is either "DSA Advance" or "Advance To Staff"
                    if (
                        isset($selectedAdvanceTypeId) &&
                        array_key_exists($selectedAdvanceTypeId, $advanceTypes) &&
                        (
                            $advanceTypes[$selectedAdvanceTypeId] === "SIFA Loan" ||
                            $advanceTypes[$selectedAdvanceTypeId] === "Device EMI" ||
                            $advanceTypes[$selectedAdvanceTypeId] === "Salary Advance"

                        )
                    ) {
                        return true;
                    }
                    
                    return false;
                })->afterStateUpdated(function ($state, Closure $set, $get){
                    $totalamount = $get('total_amount');
                    // dd($amount);
                    $monthlyEMI = $totalamount / $state;
                    $monthlyEMI = round($monthlyEMI,2);

                    $set('monthly_emi_amount',$monthlyEMI);
                }),
                Forms\Components\TextInput::make('monthly_emi_amount')
                ->numeric()
                ->disabled()
                ->minValue(0)
                ->reactive()
                ->label("Monthly EMI amount")
                // ->required()
                ->visible(function ($get) use ($advanceTypes) {
                    $selectedAdvanceTypeId = $get('advance_type_id');
                    
                    // Check if the selected type is either "DSA Advance" or "Advance To Staff"
                    if (
                        isset($selectedAdvanceTypeId) &&
                        array_key_exists($selectedAdvanceTypeId, $advanceTypes) &&
                        (
                            $advanceTypes[$selectedAdvanceTypeId] === "SIFA Loan" ||
                            $advanceTypes[$selectedAdvanceTypeId] === "Device EMI"
                        )
                    ) {
                        return true;
                    }
                    
                    return false;
                }),
                Forms\Components\DatePicker::make('deduction_period')
                ->required()
                ->visible(function ($get) use ($advanceTypes) {
                    $selectedAdvanceTypeId = $get('advance_type_id');
                    
                    // Check if the selected type is either "DSA Advance" or "Advance To Staff"
                    if (
                        isset($selectedAdvanceTypeId) &&
                        array_key_exists($selectedAdvanceTypeId, $advanceTypes) &&
                        (
                            $advanceTypes[$selectedAdvanceTypeId] === "SIFA Loan" ||
                            $advanceTypes[$selectedAdvanceTypeId] === "Device EMI" ||
                            $advanceTypes[$selectedAdvanceTypeId] === "Salary Advance"
                        )
                    ) {
                        return true;
                    }
                    
                    return false;
                }),
                Forms\Components\Textarea::make('purpose')
                ->rows(2),
                Forms\Components\FileUpload::make('upload_file')
                ->preserveFilenames()                   

          

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name'),
                // Tables\Columns\TextColumn::make('advance_no'),
                Tables\Columns\TextColumn::make('advanceType.name'),
                Tables\Columns\TextColumn::make('date')
                ->dateTime(),
                Tables\Columns\TextColumn::make('amount'),
                Tables\Columns\TextColumn::make('status'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Action::make('Approve')
                ->action(fn (ApplyAdvance $record) => ApplyAdvanceApprovalResource::ApproveAdvance($record))
                ->requiresConfirmation()
                ->modalHeading('Approve')
                ->modalSubheading('Are you sure you\'d like to approve? This cannot be undone.')
                ->modalButton('Yes, approve now')
                ->color('success')
                ->hidden(function ( ApplyAdvance $record) {
                    return $record->status === "approved";
                }),
                //Action::make('Reject')
                //->action(fn (AdvanceApproval $record) => AdvanceApprovalResource::RejectAdvance($record))
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
                ->hidden(function ( ApplyAdvance $record) {
                    return $record->status === "approved";
                })
                ->action(function (ApplyAdvance $record, array $data) {
                    $remark = $data['remark'];
                    ApplyAdvanceApprovalResource::RejectAdvance($record, $remark);
                }) ,
                Action::make('Download')
                ->action(fn (ApplyAdvance $record) => ApplyAdvanceApprovalResource::downloadFile($record))
                ->hidden(function ( ApplyAdvance $record) {
                    return $record->upload_file === null;
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
            'index' => Pages\ListApplyAdvanceApprovals::route('/'),
            //'create' => Pages\CreateApplyAdvanceApproval::route('/create'),
            //'edit' => Pages\EditApplyAdvanceApproval::route('/{record}/edit'),
        ];
    } 
    public static function downloadFile($record)
    {
        // Use Storage::url to generate the proper URL for the file
        $upload_file = 'uploads/' . $record->upload_file; // assuming 'public' is the disk name

        // Check if the file exists in storage
        if (!Storage::exists($upload_file)) {
            abort(404, 'File not found');
        }
    
        return Storage::download($upload_file);
    } 
    
    public static function ApproveAdvance($record) {
        $id = $record->id;
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

            if ($leaveApplication->level1 === 'pending' && $approvalType->MaxLevel === 'Level1') {
                $leaveApplication->update([
                    'level1' => 'approved',
                ]);
        
                $content = "The Advance you have applied for has been approved.";
            
                Mail::to($Approvalrecipient)->send(new AdvanceApprovedMail($user, $content));
            
                Notification::make() 
                ->title('Advance Approved successfully')
                ->success()
                ->send();
            
            }else if (
                $leaveApplication->level1 === 'pending' &&
                $leaveApplication->level2 === 'pending' &&
                ($approvalType->MaxLevel === 'Level2' || $approvalType->MaxLevel === 'Level3')
            ) { 
                $leaveApplication->update([
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
                        $approval = $departmentHead;
                        $currentUser = $user;
        
                        Mail::to($recipient)->send(new AdvanceApplicationMail($approval, $currentUser));
                        Notification::make() 
                        ->title('Advance Approved successfully')
                        ->success()
                        ->send();
                    }else{
                        // Access the 'value' field from the level record
                        $levelValue = $levelRecord->value;
                        $userID = $levelRecord->emp_id;
                        $approval = FilamentUser::where('id', $userID)->first();
                        // Determine the recipient based on the levelValue
                        $recipient = $approval->email;
        
                        $currentUser = $user;
        
                        Mail::to($recipient)->send(new AdvanceApplicationMail($approval, $currentUser));  
                    }
                   
                
                }
    
            }else if($leaveApplication->level1==='approved' && $approvalType->MaxLevel === 'Level2') {
                $leaveApplication->AdvanceApproval->update([
                    'level2' => 'approved',
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

            } else if($leaveApplication->level1==='approved' &&$leaveApplication->level2==='pending' && $approvalType->MaxLevel === 'Level3') {
                $leaveApplication->update([
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
            } else if($leaveApplication->level1==='approved' &&$leaveApplication->level2==='approved' && $leaveApplication->level3==="pending" && $approvalType->MaxLevel === 'Level3') {

                $leaveApplication->update([
                    'level3' => 'approved',
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

            $leaveApplication->leaveApproval->update([
                'level1' => 'approved',
                'level2' => 'approved',
                'level3' => 'approved',
                
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
        }
       

    }   
}
