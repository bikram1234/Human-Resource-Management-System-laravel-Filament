<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdvanceSettlementResource\Pages;
use App\Filament\Resources\AdvanceSettlementResource\RelationManagers;
use App\Models\AdvanceSettlement;
use App\Models\ApplyLoanAdvance;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Closure;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Actions\Action;




class AdvanceSettlementResource extends Resource
{
    protected static ?string $model = AdvanceSettlement::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Loan For Advance';
    protected static ?string $navigationLabel = 'Advance Settlement';
    protected static ?string $pluralModelLabel = 'Your Settlement';
    protected static ?string $modelLabel = 'Settlement';
    protected static ?int $navigationSort = 4;


    

    public static function form(Form $form): Form
    {
        $settlement = Advance_Settlement_id;
        $currentUserId = Auth::id();
        $user = Auth::user();

        $userAdvances = ApplyLoanAdvance::where('status', 'approved')
            ->where('user_id', $user->id) 
            ->pluck('reference_no', 'id');

        // Get the IDs of advances that exist in the dsa_settlements table
        $existingAdvanceIds = DB::table('advance_settlements')->pluck('advance_no');
        //dd($existingAdvanceIds);

        // Filter the user's advances to include only those that do not exist in the settlements table
        $userAdvances = $userAdvances->filter(function ($advanceNo, $id) use ($existingAdvanceIds) {
            return !$existingAdvanceIds->contains($id);
        });
        //dd($userAdvances);


        return $form
            ->schema([
                Forms\Components\hidden::make('loantype_id')
                ->default($settlement)
                ->disabled()
                ->required(),
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
                ->label("Reference Number")
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, Closure $set){
                    $advance = ApplyLoanAdvance::whereRaw("id =?", [$state])->value("amount");
                    $advance = round($advance,2);
                    $set('advance_amount', $advance);
                    $set('balance_amount', 0); 
                }),

                Forms\Components\TextInput::make('advance_amount')
                ->required()
                ->disabled()
                ->default(0)
                ->reactive(),
                Forms\Components\TextInput::make('balance_amount')
                ->default(0)
                ->required(),
                Forms\Components\FileUpload::make('attachment')
                ->preserveFilenames() 
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name'),
                Tables\Columns\TextColumn::make('date')
                ->dateTime(),
                Tables\Columns\TextColumn::make('loanadvance.reference_no')
                ->label("Reference Number"),
                Tables\Columns\TextColumn::make('loanadvance.code.code'),                
                Tables\Columns\TextColumn::make('advance_amount'),
                Tables\Columns\TextColumn::make('balance_amount'),
                Tables\Columns\TextColumn::make('status'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Action::make('Download')
                ->action(fn (AdvanceSettlement $record) => AdvanceSettlementResource::downloadFile($record))
                ->hidden(function ( AdvanceSettlement $record) {
                    return $record->attachment === null;
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
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdvanceSettlements::route('/'),
            'create' => Pages\CreateAdvanceSettlement::route('/create'),
            'edit' => Pages\EditAdvanceSettlement::route('/{record}/edit'),
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
}
