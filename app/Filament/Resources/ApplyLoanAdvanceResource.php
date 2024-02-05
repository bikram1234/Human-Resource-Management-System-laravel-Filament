<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApplyLoanAdvanceResource\Pages;
use App\Filament\Resources\ApplyLoanAdvanceResource\RelationManagers;
use App\Models\ApplyLoanAdvance;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Chiiya\FilamentAccessControl\Models\FilamentUser;
use App\Models\department;
use App\Models\LoanAdvancetype;
use App\Models\BudgetCode;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Storage;






class ApplyLoanAdvanceResource extends Resource
{
    protected static ?string $model = ApplyLoanAdvance::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Loan For Advance';
    protected static ?string $navigationLabel = 'Apply';
    protected static ?string $pluralModelLabel = 'Your Loans';
    protected static ?string $modelLabel = 'Loan';
    protected static ?int $navigationSort = 3;


    public static function form(Form $form): Form
    {
        $currentUserId = Auth::id();
        $currentDateTime = now();
        $user = FilamentUser::find($currentUserId);
        $department_id = $user->department_id;
        $empy_id = $user->emp_id;
        $shortCode = department::where('id', $department_id)->value('short_code');
        //dd($shortCode);
        $referenceNo = 'TIPL|'.$shortCode.'|'.$empy_id;






        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                ->default($currentUserId)
                ->disabled()
                ->required(),
                Forms\Components\Hidden::make('reference_no')
                ->default($referenceNo)
                ->disabled()
                ->required(),
                Forms\Components\TextInput::make('date')
                ->type('date')
                ->default(now()->toDateString())  // Set default value to current date
                ->disabled()  // Make the field disabled
                ->required(),
                Forms\Components\Select::make('loan_type_id')
                ->options(
                    LoanAdvancetype::all()->pluck('condition', 'id')->toArray()
                )
                ->reactive()
                ->label('Loan type')
                ->required(),
                Forms\Components\Select::make('budget_code')
                ->options(
                    BudgetCode::all()->pluck('particular', 'id')->toArray()
                )
                ->label('Budget Code')
                ->searchable()
                ->required(),
                Forms\Components\DatePicker::make('from_date')
                ->required(),
                Forms\Components\DatePicker::make('to_date')
                ->after('from_date')
                ->required(),
                Forms\Components\Select::make('activity')
                ->options([
                    'Planned' => 'Planned',
                    'unplanned' => 'unplanned',
                    'Ad-hoc' => 'Ad-hoc',
                ])
                ->required(),
                Forms\Components\TextInput::make('amount')  
                ->required() 
                ->reactive()
                ->numeric()
                ->label('Amount')
                ->maxValue(function ($get) {
                    $selectedLoanType = LoanAdvancetype::find($get('loan_type_id'));
                    return $selectedLoanType && $selectedLoanType->name === 'Advance Loan Max' ? 100000 : 5000;
                }),
                Forms\Components\Textarea::make('subject')
                ->rows(2),
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
                Tables\Columns\TextColumn::make('reference_no'),
                Tables\Columns\TextColumn::make('code.code'),
                Tables\Columns\TextColumn::make('loantype.condition'),
                Tables\Columns\TextColumn::make('amount'),
                Tables\Columns\TextColumn::make('status'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('Download')
                ->action(fn (ApplyLoanAdvance $record) => ApplyLoanAdvanceResource::downloadFile($record))
                ->hidden(function ( ApplyLoanAdvance $record) {
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
            'index' => Pages\ListApplyLoanAdvances::route('/'),
            'create' => Pages\CreateApplyLoanAdvance::route('/create'),
            'edit' => Pages\EditApplyLoanAdvance::route('/{record}/edit'),
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
