<?php

namespace App\Filament\Resources;

use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\DSASettlementResource\Pages;
use App\Filament\Resources\DSASettlementResource\RelationManagers;
use App\Models\ApplyAdvance;
use App\Models\RateLimit;
use App\Models\policy;
use App\Models\RateDefinition;
use App\Models\DSAManual;
use App\Models\DSASettlement;
use App\Models\ExpenseType;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Forms;
use Illuminate\Validation\ValidationException;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Closure;
use Doctrine\DBAL\Driver\OCI8\Exception\Error;
use Symfony\Contracts\Service\Attribute\Required;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;


class DSASettlementResource extends Resource
{
    protected static ?string $model = DSASettlement::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationGroup = 'Expense';

    protected static ?string $navigationLabel = 'DSA Settlement';


    protected static ?int $navigationSort = 5;








    public static function form(Form $form): Form
     {
        // $expenseType = ExpenseType::where('name', 'DSA Settlement')->first();
        $expense = DSA_ID;
       
        if ($expense) {
            // Find the Policies associated with the ExpenseType
            $policies = Policy::where('expense_type_id', $expense)->get();
            
            // Get policy IDs
            $policyIds = $policies->pluck('id')->toArray();
            
            // Find the RateLimits with the same policy ID and user's grade
            $rateLimits = RateLimit::whereIn('policy_id', $policyIds)
                ->where('grade', Auth::user()->grade_id)
                ->get();
            
            // Check if there are matching rate limits
            if ($rateLimits->isEmpty()) {
                $da = 0; // Handle the case where rate limits with the user's grade don't exist
            } else {
                // You can choose how to handle multiple rate limits here; for now, let's take the first one
                $rateLimit = $rateLimits->first();
                $da = $rateLimit->limit_amount;
            }
        } else {
            $da = "no da set"; // Handle the case where the DSA policy doesn't exist
        }


        $currentUserId = Auth::id();
        $user = Auth::user();

        $userAdvances = ApplyAdvance::whereHas('advanceType', function ($query) {
            $query->where('name', 'DSA Advance');
            })
            ->where('status', 'approved')
            ->where('user_id', $user->id) 
            ->pluck('advance_no', 'id');
            //dd($userAdvances);

        // Get the IDs of advances that exist in the dsa_settlements table
        $existingAdvanceIds = DB::table('d_s_a_settlements')->pluck('advance_no');

        // Filter the user's advances to include only those that do not exist in the dsa_settlements table
        $userAdvances = $userAdvances->filter(function ($advanceNo, $id) use ($existingAdvanceIds) {
            return !$existingAdvanceIds->contains($id);
        });

        return $form
            ->schema([
                Forms\Components\hidden::make('expensetype_id')
                ->label("Expense Type")
                ->default($expense)
                ->disabled()
                ->reactive()
                ->required()
                ->afterStateHydrated(function ($state, Closure $set){
                    $policy = policy::where('expense_type_id', $state)->value('id');
                    $attachment_required = RateDefinition::where('policy_id', $policy)->value('attachment_required');
                    $set('attachment_required', $attachment_required);
                }),
                Forms\Components\Hidden::make('user_id')
                ->default($currentUserId)
                ->disabled()
                ->required(),
                Forms\Components\TextInput::make('date')
                ->type('date')
                ->default(now()->toDateString())  // Set default value to current date
                ->disabled()  // Make the field disabled
                ->required(),
                Forms\Components\Select::make('advance_no')
                ->options($userAdvances)
                //->required()
                ->reactive()
                ->afterStateUpdated(function ($state, Closure $set){
                    $advance = ApplyAdvance::whereRaw("id =?", [$state])->value("amount");
                    $advance = round($advance,2);
                    $set('advance_amount', $advance);
                    $set('total_amount_adjusted', $advance);
                    $set('net_payable_amount', $advance);
                    $set('balance_amount', 0); 
                })
                ->required(function ($get) use ($userAdvances){
                    if(!$userAdvances->isEmpty()) {
                        return true;
                    }
                    return false;

                }),
                Forms\Components\TextInput::make('advance_amount')
                //->required()
                ->disabled()
                ->default(0)
                ->reactive()
                ->required(function ($get) use ($userAdvances){
                    if(!$userAdvances->isEmpty()) {
                        return true;
                    }
                    return false;

                }),
                Forms\Components\TextInput::make('total_amount_adjusted')
                //->required()
                ->disabled()
                ->reactive()
                ->required(function ($get) use ($userAdvances){
                    if(!$userAdvances->isEmpty()) {
                        return true;
                    }
                    return false;

                }),
                Forms\Components\TextInput::make('net_payable_amount')
                ->disabled()
                ->reactive()
                ->required(function ($get) use ($userAdvances){
                    if(!$userAdvances->isEmpty()) {
                        return true;
                    }
                    return false;

                }),
                Forms\Components\TextInput::make('balance_amount')
                ->default(0)
                ->disabled()
                ->required(function ($get) use ($userAdvances){
                    if(!$userAdvances->isEmpty()) {
                        return true;
                    }
                    return false;

                }),
                Forms\Components\FileUpload::make('upload_file')
                ->preserveFilenames()
                ->required(function(callable $get){
                    if($get('attachment_required') == true){
                        return true;
                    }else{
                        return false;
                    }
                }),

                
                Forms\Components\Card::make()
                ->schema([
                 Forms\Components\Repeater::make('DSAManual')
                ->columns(5)
                ->reactive()
                ->visible(function ($get) use ($userAdvances){
                    if($userAdvances->isEmpty()) {
                        return true;
                    }
                    return false;

                })
                ->relationship()
                ->columnSpanFull()
                ->schema([
                    Forms\Components\Hidden::make('user_id')
                    ->default($currentUserId)
                    ->disabled()
                    ->required(),
                    Forms\Components\DatePicker::make('from_date')
                    ->reactive()
                    ->required(),
                    Forms\Components\TextInput::make('from_location')
                    ->required(),
                    Forms\Components\DatePicker::make('to_date')
                    ->reactive()
                    ->required()
                    ->afterOrEqual('from_date')
                    ->afterStateUpdated(function ($state, Closure $set, $get){
                        $fromDate = strtotime($get('from_date'));
                            $endDate = strtotime($state);
                            $diff = $endDate - $fromDate;
                        
                            // Calculate the number of full days
                            $numberOfDays = floor($diff / (24 * 60 * 60)) + 1;
                            $set('total_days', $numberOfDays);

                            $allRows = $get('../../DSAManual');
                            $seenDates = array();
                            
                            foreach ($allRows as $key => $singleRow) {
                                $currentDate = $singleRow['to_date'];
                            
                                if (in_array($currentDate, $seenDates)) {
                                    echo "Error: Duplicate to_date found - $currentDate";
                                    dd('same to dates');
                                    // Notification::make() 
                                    // ->title('To days are same ')
                                    // ->success()
                                    // ->send();
                                    $seenDates = null;
                                } else {
                                    $seenDates[] = $currentDate;
                                }
                            }

                            
                    }),
                    Forms\Components\TextInput::make('to_location')
                    ->required(),
                    Forms\Components\TextInput::make('total_days')
                    ->reactive()
                    ->required()
                    ->numeric(), 
                    Forms\Components\TextInput::make('da')
                    ->reactive()
                    ->default($da)
                   ->required()
                   ->disabled(),
                    Forms\Components\TextInput::make('ta')
                    ->reactive()
                    ->numeric()
                    ->required()
                    ->afterStateUpdated(function ($state, Closure $set, $get){
                        $rowTotaldays = $get('total_days');
                        $rowDa = $get('da');
                        $rowTotal = ($rowDa*$rowTotaldays)+$state;
                        $set('total_amount',(round($rowTotal,2)));
                        //GRAND TOTAL CALC
                        $allRows = $get('../../DSAManual');
                        $grandTotalAmount = 0;
                        foreach($allRows as $key=>$singleRow){
                           $grandTotalAmount += $singleRow['total_amount'];
                        }
                        $set('../../total_amount_adjusted', $grandTotalAmount);
                        $set('../../net_payable_amount', $grandTotalAmount);
                        //END
                        
                        

                    }),
                    Forms\Components\TextInput::make('total_amount')
                    ->reactive()
                    ->numeric()
                    ->default(0)
                    ->required()
                    ->disabled(),
                    Forms\Components\Textarea::make('remarks')
                    ->rows(1)
                    ->required(),
                ])->createItemButtonLabel('Add')
                ])
            ]);
    }
  
    public static function table(Table $table): Table
    {
        $user = auth()->user();
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name'),
                Tables\Columns\TextColumn::make('advance.advance_no'),
                Tables\Columns\TextColumn::make('date')
                ->dateTime(),
                Tables\Columns\TextColumn::make('total_amount_adjusted'),
                Tables\Columns\TextColumn::make('advance_amount'),
                Tables\Columns\TextColumn::make('status'),            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Action::make('Download')
                ->action(fn (DSASettlement $record) => DSASettlementResource::downloadFile($record))
                ->hidden(function ( DSASettlement $record) {
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
            'index' => Pages\ListDSASettlements::route('/'),
            'create' => Pages\CreateDSASettlement::route('/create'),
            'edit' => Pages\EditDSASettlement::route('/{record}/edit'),
        ];
    } 
    public static function downloadFile($record)
    {
        // Use Storage::url to generate the proper URL for the file
        $upload_file = 'uploads/' . $record->upload_file; // assuming 'uploads' is the disk name

        // Check if the file exists in storage
        if (!Storage::exists($upload_file)) {
            abort(404, 'File not found');
        }
    
        return Storage::download($upload_file);
    }     
}
